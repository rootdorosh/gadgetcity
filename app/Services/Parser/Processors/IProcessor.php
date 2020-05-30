<?php

namespace App\Services\Parser\Processors;

interface IProcessor
{
    public function parse(string $post): array;
}
