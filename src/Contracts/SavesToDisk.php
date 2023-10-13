<?php

namespace LaravelLiberu\Excel\Contracts;

interface SavesToDisk
{
    public function folder(): string;
}
