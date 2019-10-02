<?php

namespace LaravelEnso\Excel\app\Contracts;

interface SavesToDisk
{
    public function path(): string;
}
