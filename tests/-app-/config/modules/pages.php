<?php

return [
    'fields' => [
        'slug',
        'keywords',
        'title',
        'description',
        'source',
    ],
    'with'   => [
        'keywords' => true
    ],
    'page'   => 'page'
];