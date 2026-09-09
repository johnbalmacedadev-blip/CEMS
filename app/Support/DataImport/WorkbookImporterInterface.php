<?php

namespace App\Support\DataImport;

interface WorkbookImporterInterface
{
    public function key(): string;

    public function label(): string;

    /** @return array{label:string,route:string|null} */
    public function page(): array;

    /** @return array<int, string> */
    public function tables(): array;

    public function matches(string $originalName, array $sheetNames): bool;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listSheets(string $path): array;

    /**
     * @param  array<int, string>  $sheetNames
     * @return array<string, mixed>
     */
    public function analyze(string $path, array $sheetNames): array;

    /**
     * @param  array<int, string>  $sheetNames
     * @return array<string, mixed>
     */
    public function import(string $token, string $path, array $sheetNames): array;
}
