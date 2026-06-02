<?php

namespace App\Services;

use SimpleXMLElement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Fb2Service
{
    public function extract(string $filePath): array
    {
        $xml = simplexml_load_file($filePath);

        if (!$xml instanceof SimpleXMLElement) {
            throw new \RuntimeException('Invalid FB2 file.');
        }

        $description = $xml->description;

        $titleInfo = $description->{'title-info'};

        return [
            'title'       => $this->extractTitle($titleInfo),
            'author'      => $this->extractAuthor($titleInfo),
            'description' => $this->extractDescription($titleInfo),
            'cover'       => $this->extractCover($xml, $titleInfo),
        ];
    }

    private function extractTitle(SimpleXMLElement $titleInfo): ?string
    {
        return trim((string) $titleInfo->{'book-title'}) ?: null;
    }

    private function extractAuthor(SimpleXMLElement $titleInfo): ?string
    {
        $author = $titleInfo->author;

        if (!$author) {
            return null;
        }

        $parts = array_filter([
            (string) $author->{'first-name'},
            (string) $author->{'middle-name'},
            (string) $author->{'last-name'},
        ]);

        return $parts ? implode(' ', $parts) : null;
    }

    private function extractDescription(SimpleXMLElement $titleInfo): ?string
    {
        if (!isset($titleInfo->annotation)) {
            return null;
        }

        $text = '';

        foreach ($titleInfo->annotation->children() as $child) {
            $text .= trim((string) $child) . "\n";
        }

        return trim($text) ?: null;
    }

    private function extractCover(SimpleXMLElement $xml, SimpleXMLElement $titleInfo): ?string
    {
        $binary = $this->resolveCoverBinary($xml, $titleInfo);

        if (!$binary) {
            return null;
        }

        $fileName = 'covers/' . uniqid('fb2_', true) . '.jpg';

        Storage::disk('public')->put($fileName, $binary);

        return $fileName;
    }

    private function resolveCoverBinary(SimpleXMLElement $xml, SimpleXMLElement $titleInfo): ?string
    {
        // 1. coverpage
        $coverPage = $titleInfo->{'coverpage'} ?? null;

        Log::channel('info_file')->info(print_r($titleInfo, true));

        if ($coverPage && $coverPage->image) {
            $img = $this->resolveBinaryImage($xml, $coverPage->image);

            if ($img) {
                return $img;
            }
        }

        // 2. fallback: первое изображение в книге
        foreach ($xml->binary as $binary) {

            $type = (string) $binary['content-type'];

            if (str_starts_with($type, 'image/')) {
                return base64_decode((string) $binary);
            }
        }

        return null;
    }

    private function resolveBinaryImage(SimpleXMLElement $xml, SimpleXMLElement $image): ?string
    {
        $href = null;

        $href = $this->getHref($image);

        if (!$href) {
            return null;
        }

        foreach ($xml->binary as $binary) {
            if ((string) $binary['id'] === $href) {
                return base64_decode((string) $binary);
            }
        }

        return null;
    }

    private function getHref(SimpleXMLElement $image): ?string
    {
        $namespaces = $image->getNamespaces(true);

        foreach (['l', 'xlink'] as $ns) {

            if (!isset($namespaces[$ns])) {
                continue;
            }

            $attrs = $image->attributes($namespaces[$ns]);

            if (isset($attrs['href'])) {
                return ltrim((string) $attrs['href'], '#');
            }
        }

        // fallback: иногда без namespace
        $attrs = $image->attributes();

        if (isset($attrs['href'])) {
            return ltrim((string) $attrs['href'], '#');
        }

        return null;
    }

    public function toHtml(string $path): string
    {
        $xml = simplexml_load_file($path);

        return $this->renderBody($xml->body);
    }

    private function renderBody($body): string
    {
        $html = '';

        foreach ($body->section as $section) {
            $html .= $this->renderSection($section);
        }

        return $html;
    }

    private function renderSection($section): string
    {
        $html = '';

        // заголовок
        if (isset($section->title)) {
            $titleText = '';

            foreach ($section->title->p as $p) {
                $titleText .= (string) $p . ' ';
            }

            $html .= '<h2>' . e(trim($titleText)) . '</h2>';
        }

        // параграфы
        foreach ($section->p as $p) {
            $html .= '<p>' . e((string) $p) . '</p>';
        }

        // вложенные секции (ВАЖНО)
        if (isset($section->section)) {
            foreach ($section->section as $subSection) {
                $html .= $this->renderSection($subSection);
            }
        }

        return $html;
    }
}
