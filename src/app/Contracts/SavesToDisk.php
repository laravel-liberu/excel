<?php

namespace LaravelEnso\Excel\App\Contracts;

interface SavesToDisk
{
    public function path(): string;
}
