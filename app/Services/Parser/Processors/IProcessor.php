<?php

namespace App\Services\Parser\Processors;

interface IProcessor
{
    /**
     * @param string $post
     * @param array $params
     * @return array
     */
    public function parse(string $post, array $params = []): array;
}
