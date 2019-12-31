<?php

namespace LaravelEnso\Excel\App\Contracts;

interface ExportsExcel
{
    public function filename(): string;

    public function heading(): array;

    public function rows(): array;
}
