<?php

namespace LaravelEnso\Excel\Contracts;

interface SavesToDisk
{
    public function folder(): string;
}
