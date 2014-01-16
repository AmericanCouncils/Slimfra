<?php

namespace Slimfra\Tests;

use Slimfra\Application;
use Symfony\Component\HttpFoundation\Request;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiateEmpty()
    {
        $app = new Application();
        $this->assertNotNull($app);
        $this->assertTrue($app instanceof Application);
    }

    public function testInstantiateWithConfig()
    {
        $app = new Application(array(
            'foo' => 'bar',
        ));

        $this->assertSame('bar', $app['foo']);
    }

    public function testRequest()
    {
        //$this->markTestSkipped();
        $app = new Application(array('hello.name' => 'Success'));
        $app->get('/', 'Slimfra\Tests\Mock\TestController::testRequest');
        ob_start();
        $app->run(Request::create("/"));
        $body = ob_get_contents();
        ob_end_clean();
        $this->assertSame("Success", $body);
    }

}
