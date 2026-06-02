<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BookArchiveProcessor
{
    public function __construct(
        private EpubService $epubService,
        private Fb2Service $fb2Service,
        private PdfService $pdfService,
    ) {}

    /**
     * Обработка распакованного архива книг
     *
     * @param string $directory путь к распакованной папке
     * @return array
     */
    public function process(string $directory): array
    {
        $books = [];

        $files = File::allFiles($directory);

        foreach ($files as $file) {
            $path = $file->getPathname();
            $extension = strtolower($file->getExtension());

            $meta = $this->extractMetadata($path, $extension);

            if (!$meta) {
                continue;
            }

            $books[] = [
                'file_path'    => $path,
                'title'        => $meta['title'] ?? $this->guessTitle($file->getFilename()),
                'author'       => $meta['author'] ?? 'Unknown',
                'description'  => $meta['description'] ?? null,
                'cover'        => $meta['cover'] ?? null,
                'extension'    => $extension,
            ];
        }

        return $books;
    }

    /**
     * Определение сервиса и извлечение метаданных
     */
    private function extractMetadata(string $path, string $extension): ?array
    {
        $meta = match ($extension) {
            'epub' => $this->epubService->extract($path),
            'fb2'  => $this->fb2Service->extract($path),
            'pdf'  => $this->pdfService->extract($path),
            default => null,
        };

        if (!is_array($meta)) {
            return null;
        }

        return $this->normalizeMetadata($meta);
    }

    /**
     * Нормализация метаданных (защита от отсутствующих полей)
     */
    private function normalizeMetadata(array $meta): array
    {
        return [
            'title'       => $meta['title'] ?? null,
            'author'      => $meta['author'] ?? null,
            'description' => $meta['description'] ?? null,
            'cover'       => $meta['cover'] ?? null,
        ];
    }

    /**
     * Попытка угадать название из имени файла
     */
    private function guessTitle(string $filename): string
    {
        return Str::of($filename)
            ->beforeLast('.')
            ->replace(['_', '.'], ' ')
            ->title()
            ->toString();
    }
}