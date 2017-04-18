<?php

namespace Spiral\Pages;

class Utils
{
    /**
     * Fetch data from provided array using keys array.
     *
     * @param array $fields
     * @param array $keys
     * @return array
     */
    public function fetchKeys(array $fields, array $keys): array
    {
        return array_intersect_key($fields, array_flip($keys));
    }
}