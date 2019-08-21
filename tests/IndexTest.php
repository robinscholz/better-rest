<?php

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        $this->setOutputCallback(function () {
        });
    }

//    public function testFindsHomePageEN()
//    {
//        $response = kirby()->render('en');
//        $this->assertTrue($response->code() === 200);
//        $this->assertStringContainsString('Home', $response->body());
//    }
//
//    public function testFindsHomePageDE()
//    {
//        $response = kirby()->render('de');
//        $this->assertTrue($response->code() === 200);
//        $this->assertStringContainsString('Home', $response->body());
//    }

    public function testFindsTestPage()
    {
        $response = kirby()->call('en/test');
        $this->assertEquals('en', kirby()->language()->code());
        $this->assertStringContainsString('Test EN', $response->title()->value());

        $response = kirby()->call('de/test');
        $this->assertEquals('de', kirby()->language()->code());
        $this->assertStringContainsString('Test DE', $response->content('de')->title()->value());

        // has image from field
        $response = kirby()->render('/en/test');
        $this->assertRegExp('/media\/pages\/test\/.*-.*\/test.jpeg/', $response->body());
    }

//    public function testFindsRouteFromTestConfig()
//    {
//        $response = kirby()->render('en/path/pages/test');
//        $this->assertTrue($response->code() === 200);
//        $this->assertTrue('application/json' === $response->type());
//        $this->assertStringContainsString(
//            'pages/test',
//            json_decode($response->body())->path
//        );
//    }
}
