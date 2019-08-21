# 🤝 Better REST

![GitHub release](https://img.shields.io/github/release/robinscholz/better-rest.svg?maxAge=900) ![License](https://img.shields.io/github/license/mashape/apistatus.svg) ![Kirby Version](https://img.shields.io/badge/Kirby-3-black.svg) ![Kirby 3 Pluginkit](https://img.shields.io/badge/Pluginkit-YES-cca000.svg) [![Build Status](https://travis-ci.com/robinscholz/better-rest.svg?branch=master)](https://travis-ci.com/robinscholz/better-rest) [![Coverage Status](https://coveralls.io/repos/github/robinscholz/better-rest/badge.svg?branch=master)](https://coveralls.io/github/robinscholz/better-rest?branch=master)

Small [Kirby](https://getkirby.com) plugin that exposes the internal REST API at `/rest` with the option to convert Kirbytags to HTML and add a `srcset` to images in the process. Intended to convert Kirby into a headless CMS.


## Usage

You can access the Better-Rest API route at `/rest`. The plugin only allows `GET` requests.

### Authentification
Requests need to be authenticated via _Basic Auth_. It’s recommended to create a seperate _API User_ with a special blueprint at `site/blueprints/users/api.yml` or use the one provided by this plugin called [Better-Rest](https://github.com/robinscholz/better-rest/blob/master/blueprints/users/betterrest.yml). Read more about [User Roles in the docs](https://getkirby.com/docs/guide/users/roles).

### Kirby 3 API

Examples:

- `rest/pages/:id` : https://getkirby.com/docs/reference/api/pages
- `rest/users/:id` : https://getkirby.com/docs/reference/api/users

> [official Kirby 3 API docs](https://getkirby.com/docs/reference/api/)

### Better-Rest Settings from Query

You can override the Settings defined in your Config file with each request in prefixing the param with `br-`.

Examples:

- `rest/pages/test?br-srcset=375,1200` : **br-srcset**
- `rest/pages/test?br-smartypants=1` : **br-smartypants**
- `rest/pages/test?br-language=fr` : **br-language**
- `rest/pages/test?br-kirbytags=0&br-srcset=0` : **br-kirbytag br-srcset**

### Multilang
The plugin supports multiple language settings. To fetch content for a specific language include a _X-Language header_ containing the desired language code with your request or use the Query.

## Settings

### Config File

- The plugin converts _kirbytags_ to HTML and adds a `srcset` to images by default.
- You can enable smartytags to be applied.
- You can enforce a specific language in setting its language code.

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
For local development use [Laravel Valet](https://laravel.com/docs/master/valet) or disable `https` in your `site/config/config.php` like this:

``` php
return [
  'api' => [
    'allowInsecure' => 'true'
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
A big thanks to [@bnomei](https://github.com/bnomei) who refactored the initial source code into something extendable and future proof. If you are using this plugin please consider to [buy him ☕](https://buymeacoff.ee/bnomei)!

## License
MIT
