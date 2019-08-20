<?php

/*
 * WARNING
 * This is only the config used for automated testing with travis ci.
 * It makes the api available at an public unprotected route.
 * Do not use this settings for production environments!
 *
 * https://getkirby.com/docs/guide/api/authentication#http-basic-auth
 */
return [
    'api' => [
        'allowInsecure' => 'true',
    ],
    'languages' => true,
    'routes' => function (\Kirby\Cms\App $kirby) {
        return [
            [
                'pattern' => 'path/(:all)',
                'method' => 'GET',
                'language' => '*',
                'action' => function (string $path = null) {
                    return ['path' => $path];
                },
            ],
        ];
    },
];
