<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Robinscholz\Betterrest;
use PHPUnit\Framework\TestCase;

class BetterrestTest extends TestCase
{
    public function testConstruct()
    {
        $rest = new Robinscholz\Betterrest();
        $this->assertInstanceOf(Robinscholz\Betterrest::class, $rest);
    }

    public function testDefaultOptions()
    {
        $rest = new Robinscholz\Betterrest();
        $options = $rest->getOptions();
        $this->assertIsArray($options);

        // this makes sure the defaults do not change later
        $this->assertCount(4, $options['srcset']);
        $this->assertTrue($options['kirbytags'] === true);
        $this->assertTrue($options['language'] === null);
    }

    public function testCustomOptions()
    {
        $rest = new Robinscholz\Betterrest([
            'srcset' => false,
            'kirbytags' => false,
            'language' => 'de',
        ]);
        $options = $rest->getOptions();
        $this->assertIsArray($options);

        $this->assertTrue($options['srcset'] === false);
        $this->assertTrue($options['kirbytags'] === false);
        $this->assertTrue($options['language'] === 'de');
    }

    public function testContentFromAPICall()
    {
        kirby()->impersonate('kirby');

        $rest = new Robinscholz\Betterrest();
        $rest->content = kirby()->api()->call('pages/test');

        $this->assertIsArray($rest->content);
        $this->assertTrue($rest->content['code'] === 200);
    }

    public function testContentModification()
    {
        kirby()->impersonate('kirby');

        $rest = new Robinscholz\Betterrest();
        $content = kirby()->api()->call('pages/test');
        $rest->setContent($content);
        $this->assertTrue($rest->getContent() === $content);

        // test no data to modify
        $this->assertNull($rest->modifyContent(null));
        $this->assertNull($rest->modifyContent([]));

        $data = $rest->modifyContent($rest->content);

        $this->assertIsArray($rest->getOptions()['srcset']);
        $this->assertRegExp(
            '/^\/media\/pages\/test\/.*(375w,).*(667w,).*(1024w,).*(1680w)$/',
            $data['data']['content']['testimage'][0]['srcset']
        );

        $this->assertIsArray($data);
        $rest->setData($data);
        $this->assertTrue($rest->getData() === $data);
    }

    public function testNoDataResponse()
    {
        kirby()->impersonate('kirby');

        $rest = new Robinscholz\Betterrest();
        $rest->setContent(null);

        // no data yet: empty array and 404
        $response = $rest->response();
        $this->assertIsArray($response);
        $this->assertCount(0, $response);
        $this->assertTrue(kirby()->response()->code() === 404);
    }

    public function testStaticRest()
    {
        $response = Robinscholz\Betterrest::rest();

        // no data yet: empty array and 404
        $this->assertIsArray($response);
        $this->assertCount(0, $response);
        $this->assertTrue(kirby()->response()->code() === 404);
    }

    public function testContentFromRequest()
    {
        kirby()->impersonate('kirby');

        $rest = new Robinscholz\Betterrest();
        $options = $rest->getOptions();
        $this->assertNull($options['language']);

        // trigger setting of language
        $content = $rest->contentFromRequest(
            new \Kirby\Http\Request([
                'url' => 'pages/test'
            ])
        );
        $this->assertIsArray($content);
        $this->assertTrue($content['code'] === 200);
    }

    public function testLanguage()
    {
        kirby()->impersonate('kirby');

        $rest = new Robinscholz\Betterrest([
            'language' => 'de',
        ]);
        $options = $rest->getOptions();
        $this->assertTrue($options['language'] === 'de');

        // trigger setting of language
        $content = $rest->contentFromRequest(
            new \Kirby\Http\Request([
                'url' => 'pages/test'
            ])
        );
        $this->assertIsArray($content);
        $this->assertTrue($content['code'] === 200);
        $this->assertTrue(kirby()->language()->code() === 'de');
    }
}
