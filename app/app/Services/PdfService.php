<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Imagick;

class PdfService
{
    public function extract(string $filePath): array
    {
        $title = null;
        $author = null;
        $description = null;

        try {
            $parser = new Parser();

            $pdf = $parser->parseFile($filePath);

            $details = $pdf->getDetails();

            $title = $details['Title'] ?? null;
            $author = $details['Author'] ?? null;
            $description = $details['Subject'] ?? null;
        } catch (\Throwable $e) {
            // Игнорируем ошибки чтения метаданных
        }

        return [
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'cover' => $this->extractCover($filePath),
        ];
    }

    private function extractCover(string $filePath): ?string
    {
        try {
            $imagick = new \Imagick();

            // Чем выше DPI, тем качественнее превью
            $imagick->setResolution(150, 150);

            // Загружаем только первую страницу
            $imagick->readImage($filePath . '[0]');

            $imagick->setImageFormat('jpeg');

            $cover = $imagick->getImageBlob();

            $imagick->clear();
            $imagick->destroy();

            return $cover;
        } catch (\Throwable $e) {
            return null;
        }
    }
}