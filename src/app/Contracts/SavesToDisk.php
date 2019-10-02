<?php
/**
 * Created with luv for rolcrispa.
 * User: mihai
 * Date: 10/2/19
 * Time: 10:54 AM
 */

namespace LaravelEnso\Excel\app\Contracts;


interface SavesToDisk
{
    public function path(): string;
}
