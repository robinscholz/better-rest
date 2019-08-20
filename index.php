<?php
<<<<<<< HEAD

Kirby::plugin('robinscholz/better-rest', [
    'options' => [
        'srcset' => [375, 667, 1024, 1680], // array|boolean|null
        'kirbytags' => true, // boolean
        'markdown' => false, // boolean
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
=======
Kirby::plugin('robinscholz/better-rest', [
  'options' => [
    'srcset' => [375, 667, 1024, 1680],
    'kirbytags' => true
  ],
	'routes' => function ($kirby) {
    return [
      [
        'pattern' => 'rest/(:all)',
        'method'  => 'GET',
        'env'     => 'api',
        'action'  => function ($path = null) {

          $kirby = Kirby::instance();
          $request = $kirby->request();

          if ($languageCode = $request->header('x-language')) {
            $kirby->setCurrentLanguage($languageCode);
          }

          $render = $kirby->api()->render($path, $this->method(), [
            'body'    => $request->body()->toArray(),
            'headers' => $request->headers(),
            'query'   => $request->query()->toArray(),
          ]);

          $decoded = json_decode($render, true);

          function addSrcSet($value, $srcset_option, $kirby) {
            $file = $kirby->file($value['id']);
            $value['srcset'] = $file->srcset($srcset_option);
            return $value;
          }

          function modifyContent($array, $kirby) {
            $srcset_option = $kirby->option('robinscholz.better-rest.srcset');
            $ktags_option = $kirby->option('robinscholz.better-rest.kirbytags');

            return array_map(
              static function($value) 
              use ($srcset_option, $ktags_option, $kirby) 
            {
                // Loop and check for images
                if (is_array($value)) {
                  if (
                    !empty($srcset_option) &&
                    array_key_exists('type', $value) && 
                    $value['type'] === 'image'
                  ) {
                    return addSrcSet($value, $srcset_option, $kirby);
                  } else {
                    return modifyContent($value, $kirby);
                  }
                }
                // Kirbytags
                if ($ktags_option) {
                  $value = kirbytags($value);
                }

                return $value;
            }, $array);
          }

          $decoded = modifyContent($decoded, $kirby);
          return $decoded;
        }
      ]
    ];
  }
>>>>>>> 3af4460bc149f2c900cdafd28d10bb9321d67f17
]);
