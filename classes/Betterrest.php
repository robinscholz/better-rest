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
    private $content;

    /* All data parsed from content
     *
     * @var null|array
     */
    private $data;

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
            'smartypants' => \option('robinscholz.better-rest.smartypants'),
            'language' => \option('robinscholz.better-rest.language'),
            'query' => null,
        ];
        $this->options = array_merge($defaults, $options);
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
        $path = preg_replace('/rest/', '', (string) $request->path(), 1);

        // @codeCoverageIgnoreStart
        // auto detect language
        if (! \Kirby\Toolkit\A::get($this->options, 'language')) {
            $language = $request->header('x-language');
            if ($language) {
                $this->options['language'] = $language;
            }
        }
        // @codeCoverageIgnoreEnd

        // if has language and is multilang setup...
        $language = \Kirby\Toolkit\A::get($this->options, 'language');
        if ($language && kirby()->languages()->count() !== 0) {
            $this->kirby->setCurrentLanguage($language);
        }

        // options from query
        $query = $request->query()->toArray();
        $queryToOptions = array_merge(\Kirby\Toolkit\A::get($this->options, 'query', []), $query);
        $this->options = array_merge($this->options, $this->optionsFromQuery($queryToOptions));

        $queryWithoutBetterRestParams = $query;
        foreach($query as $key => $value) {
            if (substr($key, 0, 3) === 'br-') {
                unset($queryWithoutBetterRestParams[$key]);
            }
        }

        // method api() is @internal
        $render = $this->kirby->api()->render(
            (string) $path,
            (string) $request->method(),
            [
                // 'body' => $request->body()->toArray(),
                'headers' => $request->headers(),
                'query' => $queryWithoutBetterRestParams,
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
                // NOTE: order of calls is important
                $value = $betterrest->applySmartypants((string) $value);
                $value = $betterrest->applyKirbytags((string) $value);
                return $value;
            }

            // it is an array. if it is an image...
            if (\Kirby\Toolkit\A::get($value, 'type') === 'image' && \Kirby\Toolkit\A::get($value, 'url') !== null) {
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
     * @param string $value
     * @return string
     */
    public function applySmartypants(?string $value): string
    {
        return \Kirby\Toolkit\A::get($this->options, 'smartypants') ? \smartypants($value) : $value;
    }

    /**
     * @param $value
     * @return array
     */
    public function applySrcSet($value): array
    {
        $srcset = \Kirby\Toolkit\A::get($this->options, 'srcset');
        if (! $srcset) {
            // $value['srcset'] = null; // NOTE: not setting is better for frontend than `null`
            return $value;
        }

        if (array_key_exists('id', $value)) {
            $file = $this->kirby->file($value['id']);
            $value['srcset'] = $file->srcset($srcset);
        }

        return $value;
    }

    /**
     * @param array|null $query
     * @return array
     */
    private function optionsFromQuery(?array $query = null): array
    {
        if (! $query) {
            return [];
        }

        $query = array_change_key_case($query); // to lowercase
        $optionsFromQuery = [];

        if ($kirbytags = \Kirby\Toolkit\A::get($query, 'br-kirbytags')) {
            $optionsFromQuery['kirbytags'] = self::isTrue($kirbytags);
        }
        if ($smartypants = \Kirby\Toolkit\A::get($query, 'br-smartypants')) {
            $optionsFromQuery['smartypants'] = self::isTrue($smartypants);
        }
        if ($language = \Kirby\Toolkit\A::get($query, 'br-language')) {
            $optionsFromQuery['language'] = strval($language);
        }
        if ($srcset = \Kirby\Toolkit\A::get($query, 'br-srcset')) {
            $srcset = str_replace([' ', '%20'], ['', ''], (string) $srcset);
            $optionsFromQuery['srcset'] = in_array($srcset, ['false', '0', 'null']) ? null : array_map(static function($v) { return intval($v); }, explode(',', $srcset));
        }

        return $optionsFromQuery;
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
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
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

    /**
     * @param $val
     * @param bool $return_null
     * @return bool|mixed|null
     */
    public static function isTrue($val, bool $return_null = false)
    {
        $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        $boolval = $boolval === null && ! $return_null ? false : $boolval;
        return $boolval;
    }
}
