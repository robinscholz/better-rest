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
        $this->assertTrue($options['markdown'] === false);
        $this->assertTrue($options['language'] === null);
    }

    public function testCustomOptions()
    {
        $rest = new Robinscholz\Betterrest([
            'srcset' => false,
            'kirbytags' => false,
            'markdown' => true,
            'language' => 'de',
        ]);
        $options = $rest->getOptions();
        $this->assertIsArray($options);

        $this->assertTrue($options['srcset'] === false);
        $this->assertTrue($options['kirbytags'] === false);
        $this->assertTrue($options['markdown'] === true);
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
        $rest->setContent(kirby()->api()->call('pages/test'));
        $rest->setData($rest->modifyContent($rest->content));

        $this->assertIsArray($rest->data);
    }
}
