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

            $imagick->setResolution(100, 100);
            $imagick->readImage($filePath . '[0]');
            $imagick->setImageFormat('jpeg');

            $filename = 'covers/' . uniqid() . '.jpg';
            $fullPath = storage_path('app/public/' . $filename);

            $imagick->writeImage($fullPath);

            $imagick->clear();
            $imagick->destroy();

            return $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
