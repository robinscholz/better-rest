<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('robinscholz/better-rest', [
    'options' => [
        'srcset' => [375, 667, 1024, 1680], // array|boolean|null
        'kirbytags' => true, // boolean
        'smartypants' => false, // boolean
        'language' => null, // null = autodetect | string {language code}
    ],
    'blueprints' => [
        'users/betterrest' => __DIR__ . '/blueprints/users/betterrest.yml',
    ],
    'routes' => function (\Kirby\Cms\App $kirby) {
        return [
            [
                'pattern' => 'rest/(:all)',
                'method' => 'GET',
                'env' => 'api',
                'action' => function (string $path = null) {
                    return \Robinscholz\Betterrest::rest();
                },
            ],
        ];
    },
]);
