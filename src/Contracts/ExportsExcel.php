<?php

namespace LaravelEnso\Excel\Contracts;

interface ExportsExcel
{
    public function filename(): string;

    public function heading(string $sheet): array;

    public function rows(string $sheet): array;

    public function sheets(): array;
}
