# 🤝 Better REST

![GitHub release](https://img.shields.io/github/release/robinscholz/better-rest.svg?maxAge=900) ![License](https://img.shields.io/github/license/mashape/apistatus.svg) ![Kirby Version](https://img.shields.io/badge/Kirby-3-black.svg) ![Kirby 3 Pluginkit](https://img.shields.io/badge/Pluginkit-YES-cca000.svg) [![Build Status](https://travis-ci.com/robinscholz/better-rest.svg?branch=master)](https://travis-ci.com/robinscholz/better-rest) [![Coverage Status](https://coveralls.io/repos/github/robinscholz/better-rest/badge.svg?branch=master)](https://coveralls.io/github/robinscholz/better-rest?branch=master)

Small [Kirby](https://getkirby.com) plugin that exposes the internal REST API at `/rest` with the option to convert Kirbytags to HTML and add a `srcset` to images in the process. Intended to convert Kirby into a headless CMS.

## Caveats

### GET only
The plugin only allows `GET` requests.

### HTTPS
The Kirby installation needs to be served with a _TLS Certicificate_ via `https`.

### Local setup
For local development use [Laravel Valet](https://laravel.com/docs/master/valet) or disable `https` in your `site/config/config.php` like this:

``` php
return [
  'api' => [
    'allowInsecure' => 'true'
  ]
];
```
> **WARNING**: Do not use this setting for production environments!

### Settings

The plugin converts _kirbytags_ to HTML and adds a `srcset` to images by default. You can also enforce a specific language in setting its language code.

All settings need to be prefixed with `robinscholz.better-rest.`!

| Settings  | Default                  | Options            |
| --------- | ------------------------ | ------------------ |
| kirbytags | `true`                   | `boolean`          |
| srcset    | `[375, 667, 1024, 1680]` | `Array` or `false` |
| language  | `null`                   | `null` or `string` |

### Authentification
Requests need to be authenticated via _Basic Auth_. It’s recommended to create a seperate _API User_ with a special blueprint at `site/blueprints/users/api.yml` or use the one provided by this plugin called [Better-Rest](https://github.com/robinscholz/better-rest/blob/master/blueprints/users/betterrest.yml).

### Multilang
The plugin supports multiple language settings. To fetch content for a specific language include a _X-Language header_ containing the desired language code with your request.

## Installation

### Download
Download and copy this repository to `/site/plugins/better-rest`.

### Composer 
```
composer require robinscholz/better-rest
```

### Git submodule
```
git submodule add https://github.com/robinscholz/better-rest.git site/plugins/better-rest
```

### Credits
A big thanks to [@bnomei](https://github.com/bnomei) who refactored the initial source code into something extendable and future proof. If you are using this plugin please consider buying a Kirby license through his [affiliate link](https://getkirby.com/buy?status=accepted&expires=1568965667&seller=1129&affiliate=35731&link=1170&p_tok=f0bf50a7-ff74-4662-a633-f4b3cee7e939)!

## License
MIT
