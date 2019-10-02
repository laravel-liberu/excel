<?php
/**
 * Created with luv for rolcrispa.
 * User: mihai
 * Date: 10/2/19
 * Time: 9:36 AM
 */

namespace LaravelEnso\Commercial\app\Contracts;


interface ExportsExcel
{
    public function filename(): string;

    public function heading(): array;

    public function rows(): array;
}
