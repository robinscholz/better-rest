<?php

declare(strict_types=1);

namespace Robinscholz;

final class Betterrest
{
    /* Reference to Kirby App instance
     *
     * @var \Kirby\Cms\App
     */
    private $kirby;

    /* The content pulled from the request
     *
     * @var null|array
     */
    public $content;

    /* All data parsed from content
     *
     * @var null|array
     */
    public $data;

    /* All config values
     *
     * @var null|array
     */
    private $options;

    /**
     * Betterrest constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->kirby = \Kirby\Cms\App::instance();
        $this->content = null;
        $this->data = null;

        $defaults = [
            'srcset' => \option('robinscholz.better-rest.srcset'),
            'kirbytags' => \option('robinscholz.better-rest.kirbytags'),
            'language' => \option('robinscholz.better-rest.language'),
        ];
        $this->options = array_merge($defaults, $options);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get content as array from Request object.
     *
     * @param \Kirby\Http\Request|null $request
     * @return array|null
     */
    public function contentFromRequest(?\Kirby\Http\Request $request = null): ?array
    {
        // default to current request
        $request = $request ?? $this->kirby->request();
        $path = preg_replace('/rest/', '', (string)$request->path(), 1);

        // auto detect language
        if (! \Kirby\Toolkit\A::get($this->options, 'language')) {
            $language = $request->header('x-language');
            if ($language) {
                $this->options['language'] = $language;
            }
        }

        // if has language and is multilang setup...
        $language = \Kirby\Toolkit\A::get($this->options, 'language');
        if ($language && kirby()->languages()->count() !== 0) {
            $this->kirby->setCurrentLanguage($language);
        }

        // method api() is @internal
        $render = $this->kirby->api()->render(
            (string)$path,
            (string)$request->method(),
            [
                'headers' => $request->headers(),
                'query' => $request->query()->toArray(),
            ]
        );

        $json = json_decode($render->body(), true);
        if (is_array($json) && intval(\Kirby\Toolkit\A::get($json, 'code')) === 404) {
            return null;
        }
        return $json;
    }

    /**
     * Apply various string transformations to the
     * provided content array and return the result.
     *
     * @param array|null $array
     * @return array|null
     */
    public function modifyContent(array $array = null): ?array
    {
        if (! $array || count($array) === 0) {
            return null;
        }

        $betterrest = $this;
        $data = array_map(static function ($value) use ($betterrest) {

            // flat? exit early
            if (! is_array($value)) {
                $value = $betterrest->applyKirbytags((string) $value);
                return $value;
            }

            // it is an array. if it is an image...
            if (\Kirby\Toolkit\A::get($value, 'type') === 'image') {
                return $betterrest->applySrcSet($value);

            } else { // ... call recursive
                // TODO: this could be an if clause checking for type in [page, structure, file], right?
                return $betterrest->modifyContent($value);
            }
        }, $array);

        return $data;
    }

    /**
     * @param string $value
     * @return string
     */
    public function applyKirbytags(?string $value): string
    {
        return \Kirby\Toolkit\A::get($this->options, 'kirbytags') ? \kirbytags($value) : $value;
    }

    /**
     * @param $value
     * @return string
     */
    public function applySrcSet($value): array
    {
        $srcset = \Kirby\Toolkit\A::get($this->options, 'srcset');
        $file = $this->kirby->file($value['id']);
        $value['srcset'] = $file->srcset($srcset);

        return $value;
    }

    /**
     * Build data for a response.
     * 1) fetch content from current request
     * 2) apply transformations to content and store result as data
     * 3) set http status code on failure
     * 4) return array of data
     *
     * @return array
     */
    public function response(): array
    {
        $this->content = $this->content ?? $this->contentFromRequest();
        $this->data = $this->data ?? $this->modifyContent($this->content);

        if (! $this->data) {
            $this->data = [];
            $this->kirby->response()->code(404);
        }
        return $this->data;
    }

    /**
     * Get modified content for current request
     * and current language. Additional options
     * can be set to override config values.
     *
     * @param array $options
     * @return array
     */
    public static function rest(array $options = [])
    {
        $betterrest = new self($options);
        return $betterrest->response();
    }

    /**
     * @return null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param null $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}
