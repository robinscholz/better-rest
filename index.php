<?php
Kirby::plugin('robinscholz/better-rest', [
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

          function ktags($array) {
            return array_map(static function($value) {
              if (is_array($value)) {
                  return ktags($value);
              }
              return kirbytags($value);
            }, $array);
          }

          $decoded = ktags($decoded);
          return $decoded;
        }
      ]
    ];
  }
]);
