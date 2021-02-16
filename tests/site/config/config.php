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
        'allowImpersonation' => 'true',
    ],
    'languages' => true,
    'routes' => function (\Kirby\Cms\App $kirby) {
        return [
            [
                'pattern' => 'path/(:all)',
                'method' => 'GET',
                'language' => 'en',
                'action' => function ($language, string $path = null) {
                    return ['path' => $path];
                },
            ],
        ];
    },
];
