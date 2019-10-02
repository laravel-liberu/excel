<?php
/**
 * Created with luv for rolcrispa.
 * User: mihai
 * Date: 10/2/19
 * Time: 10:54 AM
 */

namespace LaravelEnso\Commercial\app\Contracts;


interface SavesToDisk
{
    public function filePath(): string;
}
