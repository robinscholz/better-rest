# 🤝 Better REST

Small [Kirby](https://getkirby.com) plugin that exposes the internal REST API at `/rest` with the option to convert Kirbytags and Markdown to HTML and add a `srcset` to images in the process. Intended to convert Kirby into a headless CMS.

## Caveats

### GET only
The plugin only allows `GET` requests.

### HTTPS
The Kirby installation needs to be served with a _TLS Certicificate_ via `https`.

### Local setup
For local development use [Laravel Valet](https://laravel.com/docs/5.8/valet) or disable `https` in your `site/config/config.php`:

``` php
return [
  'api' => [
    'allowInsecure' => 'true'
  ]
];
```
> **WARNING**: Do not use this setting for production environments!


### Settings
The plugin converts _kirbytags_ to HTML and adds a `srcset` to images by default. Additionally it is able to convert _markdown_ to HTML. To turn off either option set it to `false`.

| Settings  | Default                  | Options            |
| --------- | ------------------------ | ------------------ |
| kirbytags | `true`                   | `boolean`          |
| markdown  | `false`                  | `boolean`          |
| srcset    | `[375, 667, 1024, 1680]` | `Array` or `false` |

> All settings need to be prefixed with `robinscholz.better-rest.`

### Authentification
Requests need to be authenticated via _Basic Auth_. It’s recommended to create a seperate _API User_ with a special blueprint at `site/blueprints/users/api.yml`:

``` yml
title: API access
extends: users/default

permissions:
  access:
    panel: true
    users: false
    site: false
  site:
    update: false
  pages:
    create: false
    changeTemplate: false
    changeTitle: false
    changeURL: false
    hide: false
    sort: false
    update: false
    delete: false
  users:
    create: false
    createAvatar: false
    deleteAvatar: false
    changeName: false
    changeEmail: false
    changePassword: false
    changeRole: false
    delete: false
    update: false
  files:
    create: false
    changeName: false
    delete: false
    replace: false
    update: false
```

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

## License
MIT
