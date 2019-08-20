<?php

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        $this->setOutputCallback(function () {
        });
    }

    public function testFindsHomePage()
    {
        $response = kirby()->render('/');
        $this->assertTrue($response->code() === 200);
        $this->assertStringContainsString('Home', $response->body());
    }

    public function testFindsTestPage()
    {
        $response = kirby()->render('/test');
        $this->assertTrue($response->code() === 200);

        // reads title from content
        $this->assertStringContainsString('Test', $response->body());

        // has image from field
        $this->assertRegExp('/media\/pages\/test\/.*-.*\/test.jpeg/', $response->body());
    }

    public function testFindsFeedRoute()
    {
        $response = kirby()->render('/rest/test');
        $this->assertTrue($response->code() === 200);
        $this->assertTrue('application/json' === $response->type());
    }

    public function testFailsOnInvalidRoute()
    {
        $response = kirby()->render('/rest/does-not-exist');
        $this->assertTrue($response->code() === 404);
        $this->assertTrue('application/json' === $response->type());
    }

}
