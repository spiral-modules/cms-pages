<?php

namespace Spiral\Pages;

/**
 * Interface EditorInterface
 *
 * @package Spiral\Pages
 */
interface EditorInterface
{
    /**
     * Current page editor name.
     *
     * @return string
     */
    public function getName(): string;
}