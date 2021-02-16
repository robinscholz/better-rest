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
        $this->assertTrue($options['smartypants'] === false);
        $this->assertTrue($options['language'] === null);
        $this->assertTrue($options['query'] === null);
    }

    public function testCustomOptions()
    {
        $rest = new Robinscholz\Betterrest([
            'srcset' => false,
            'kirbytags' => false,
            'smartypants' => true,
            'language' => 'de',
            'query' => [
                'select' => 'files',
            ],
        ]);
        $options = $rest->getOptions();
        $this->assertIsArray($options);

        $this->assertTrue($options['srcset'] === false);
        $this->assertTrue($options['kirbytags'] === false);
        $this->assertTrue($options['smartypants'] === true);
        $this->assertTrue($options['language'] === 'de');
        $this->assertCount(1, $options['query']);
    }

    public function testContentFromAPICall()
    {
        kirby()->impersonate('kirby');

        $rest = new Robinscholz\Betterrest();
        $rest->setContent(kirby()->api()->call('pages/test'));

        $this->assertIsArray($rest->getContent());
        $this->assertTrue($rest->getContent()['code'] === 200);
    }

        
    /*
    * @runInSeparateProcess
    */
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

        $data = $rest->modifyContent($rest->getContent());

        $this->assertIsArray($rest->getOptions()['srcset']);
        $this->assertRegExp(
            '/^\/media\/pages\/test\/.*(375w,).*(667w,).*(1024w,).*(1680w)$/',
            $data['data']['content']['testimage'][0]['srcset']
        );

        $this->assertIsArray($data);
        $rest->setData($data);
        $this->assertTrue($rest->getData() === $data);
    }

    public function testNoSrcset()
    {
        kirby()->impersonate('kirby');

        $rest = new Robinscholz\Betterrest([
            'srcset' => null,
        ]);
        $content = kirby()->api()->call('pages/test');
        $rest->setContent($content);
        $data = $rest->modifyContent($rest->getContent());

        $this->assertNull($rest->getOptions()['srcset']);
        $this->assertArrayNotHasKey(
            'srcset',
            $data['data']['content']['testimage'][0]
        );
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

    public function testCustomQuery()
    {
        kirby()->impersonate('kirby');

        $rest = new Robinscholz\Betterrest([
            'query' => [
                'select' => 'files',
            ]
        ]);
        $this->assertTrue($rest->getOptions()['query']['select'] === 'files');

        $content = $rest->contentFromRequest(
            new \Kirby\Http\Request([
                'url' => 'pages/test',
                'query' => [
                    'select' => 'files',
                    'br-smartypants' => 1,
                    'br-language' => 'de',
                    'br-kirbytags' => 'false',
                    'br-srcset' => '375,1200',
                ]
            ])
        );
        $rest->setContent($content);

        // check if options got applied
        $this->assertTrue($rest->getOptions()['smartypants'] === true);
        $this->assertTrue($rest->getOptions()['kirbytags'] === false);
        $this->assertTrue($rest->getOptions()['language'] === 'de');
        $this->assertCount(2, $rest->getOptions()['srcset']);

        $response = $rest->response();
        $this->assertTrue($response['data']['files'][0]['id'] === 'test/test.jpeg');
    }
}
