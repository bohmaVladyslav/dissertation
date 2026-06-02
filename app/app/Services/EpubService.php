<?php

namespace App\Services;

use ZipArchive;
use SimpleXMLElement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class EpubService
{
    protected \ZipArchive $zip;
    protected string $tmpPath;

    public function extract(string $filePath): array
    {
        $this->zip = new \ZipArchive();

        if ($this->zip->open($filePath) !== true) {
            throw new \Exception("Cannot open EPUB file: {$filePath}");
        }

        $opfPath = $this->getOpfPath();
        $opfXml  = $this->getFile($opfPath);

        $opf = new SimpleXMLElement($opfXml);

        $metadata = $this->parseMetadata($opf);
        $manifest = $this->parseManifest($opf);

        $coverPath = $this->extractCover($opfPath, $opf, $manifest, $filePath);

        $this->zip->close();

        $metadata['cover'] = $coverPath;


        return $metadata;
    }

    /**
     * META-INF/container.xml → путь к .opf
     */
    protected function getOpfPath(): string
    {
        $containerXml = $this->getFile('META-INF/container.xml');

        $xml = new SimpleXMLElement($containerXml);

        return (string) $xml->rootfiles->rootfile['full-path'];
    }

    /**
     * Чтение файла из epub (zip)
     */
    protected function getFile(string $path): string
    {
        $content = $this->zip->getFromName($path);

        if ($content === false) {
            throw new \Exception("File not found in EPUB: {$path}");
        }

        return $content;
    }

    /**
     * Метаданные книги
     */
    protected function parseMetadata(SimpleXMLElement $opf): array
    {
        $ns = $opf->getNamespaces(true);
        $metadata = $opf->metadata;

        $dc = $metadata->children($ns['dc'] ?? null);

        // short description (dc:description)
        $shortDescription = (string) ($dc->description ?? '');

        // long description (meta property="se:long-description")
        $longDescription = null;

        foreach ($metadata->meta as $meta) {
            $attributes = $meta->attributes();

            if (
                isset($attributes['property']) &&
                (string)$attributes['property'] === 'se:long-description'
            ) {
                $longDescription = (string) $meta;
                break;
            }
        }

        return [
            'title' => (string) ($dc->title ?? 'Unknown'),
            'author' => (string) ($dc->creator ?? 'Unknown'),
            'description' => $longDescription ?: $shortDescription ?: 'Unknown',
        ];
    }

    /**
     * Manifest (список файлов внутри epub)
     */
    protected function parseManifest(SimpleXMLElement $opf): array
    {
        $manifest = [];

        foreach ($opf->manifest->item as $item) {
            $manifest[(string)$item['id']] = (string)$item['href'];
        }

        return $manifest;
    }

    /**
     * Извлечение обложки
     */
    protected function extractCover(
        string $opfPath,
        SimpleXMLElement $opf,
        array $manifest,
        string $epubFile
    ): ?string {
        $coverId = null;

        foreach ($opf->metadata->meta as $meta) {
            if ((string)$meta['name'] === 'cover') {
                $coverId = (string)$meta['content'];
                break;
            }
        }

        if (!$coverId || !isset($manifest[$coverId])) {
            return null;
        }

        $coverRelativePath = $this->resolvePath($opfPath, $manifest[$coverId]);

        $imageBinary = $this->zip->getFromName($coverRelativePath);

        if (!$imageBinary) {
            return null;
        }

        $fileName = 'covers/' . Str::random(20) . '.jpg';

        Storage::disk('public')->put($fileName, $imageBinary);

        return $fileName;
    }

    /**
     * EPUB пути часто относительные → нормализуем
     */
    protected function resolvePath(string $opfPath, string $resource): string
    {
        $dir = dirname($opfPath);

        if ($dir === '.') {
            return $resource;
        }

        return $dir . '/' . $resource;
    }
}