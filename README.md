# 🤝 Better REST

![Release](https://flat.badgen.net/packagist/v/robinscholz/better-rest?color=f28d1a)
![Stars](https://flat.badgen.net/packagist/ghs/robinscholz/better-rest?color=gray)
![Downloads](https://flat.badgen.net/packagist/dt/robinscholz/better-rest?color=gray)
![Issues](https://flat.badgen.net/packagist/ghi/robinscholz/better-rest?color=yellow)
[![Build Status](https://flat.badgen.net/travis/robinscholz/better-rest)](https://travis-ci.com/robinscholz/better-rest)
[![Coverage Status](https://flat.badgen.net/coveralls/c/github/robinscholz/better-rest)](https://coveralls.io/github/robinscholz/better-rest) 
[![Twitter](https://flat.badgen.net/badge/twitter/RobinScholz)](https://twitter.com/RobinScholz)
[![Twitter](https://flat.badgen.net/badge/twitter/bnomei)](https://twitter.com/bnomei)

A [Kirby](https://getkirby.com) plugin that exposes the internal REST API at `/rest` with the option to convert Kirbytags to HTML and add a `srcset` to images in the process. Intended to convert Kirby into a headless CMS.


## Usage

The API can be accessed at `/rest`. The plugin only allows `GET` requests.

### Authentification
Requests need to be authenticated via _Basic Auth_. It’s recommended to create a seperate _API User_ with either a custom blueprint or with the one provided by this plugin called [better-rest API](https://github.com/robinscholz/better-rest/blob/master/blueprints/users/betterrest.yml). Read more about [user roles in the docs](https://getkirby.com/docs/guide/users/roles).

_Basic Auth_ needs to be enabled in the `site/config/config.php`:

```
return [
    'api' => [
        'basicAuth' => true
    ]
];
```

### Kirby 3 API

Examples:

- `rest/pages/:id` : https://getkirby.com/docs/reference/api/pages
- `rest/site` : https://getkirby.com/docs/reference/api/site
- `rest/users/:id` : https://getkirby.com/docs/reference/api/users

> [Official Kirby 3 API docs](https://getkirby.com/docs/reference/api/)

### Better-Rest Settings from Query

All standard setting as well as settings defined in `site/config/onfig.php` can be overwritten on a per-request basis. Simply prefix the setting with `br-` and include it as a query.

Examples:

- `rest/pages/test?br-srcset=375,1200` : **br-srcset**
- `rest/pages/test?br-smartypants=1` : **br-smartypants**
- `rest/pages/test?br-language=fr` : **br-language**
- `rest/pages/test?br-kirbytags=0&br-srcset=0` : **br-kirbytag br-srcset**

### Multilang
The plugin supports multiple language settings. To fetch content for a specific language include a _X-Language header_ containing the desired language code with your request. Alternatively a `br-language` query can be used.

## Settings

### Config File

- The plugin converts _kirbytags_ to HTML and adds a `srcset` to images by default.
- Additionally it is possible to enable [smartypants](https://michelf.ca/projects/php-smartypants/).
- To overwrite the default language it is possible to set a language code.

All settings need to be prefixed with `robinscholz.better-rest.`!

| Settings    | Default                  | Options            |
| ----------- | ------------------------ | ------------------ |
| kirbytags   | `true`                   | `boolean`          |
| smartypants | `false`                  | `boolean`          |
| srcset      | `[375, 667, 1024, 1680]` | `Array` or `false` |
| language    | `null`                   | `null` or `string` |

## Caveats

### HTTPS
The Kirby installation needs to be served with a _TLS Certicificate_ via `https`.

### Local setup
For local development use [Laravel Valet](https://laravel.com/docs/master/valet) or disable `https` in the `site/config/config.php`:

``` php
return [
  'api' => [
    'basicAuth' => true,
    'allowInsecure' => true
  ]
];
```
> **WARNING**: Do not use this setting for production environments!

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

## Credits
A big thanks to [@bnomei](https://github.com/bnomei) who refactored the initial source code into something extendable and future proof. If you are using this plugin please consider [buying him a ☕](https://buymeacoff.ee/bnomei)!

## License
MIT
