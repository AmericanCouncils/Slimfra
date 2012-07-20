<?php

namespace Slimfra\Tests;

use Slimfra\Application;
use Symfony\Component\HttpFoundation\Request;

//TODO: test the app
class ApplicationTest extends \PHPUnit_Framework_TestCase {
	
	public function testInstantiateEmpty() {
		$app = new Application();
		$this->assertNotNull($app);
		$this->assertTrue($app instanceof Application);
	}
	
	public function testInstantiateWithConfig() {
		$app = new Application(array(
		    'foo' => 'bar',
		));
        
        $this->assertSame('bar', $app['foo']);
	}
	
	public function testInstantiateWithFilepath() {
		$app = new Application(__DIR__."/files/config.php");
        $this->assertSame('bar', $app['foo']);
	}
	
    public function testCustomConfigOverridesDefaults() {
        chdir(__DIR__);
        $app = new Application();
        $defaultCacheDir = __DIR__."/app/cache/sessions";
        
        $this->assertSame($defaultCacheDir, $app['session.storage.save_path']);
        
        $overriddenCacheDir = '/new/path';
        $app = new Application(array(
            'session.storage.save_path' => $overriddenCacheDir
        ));
        
        $this->assertSame($overriddenCacheDir, $app['session.storage.save_path']);
    }
    
    
    public function testRequest()
    {
        $app = new Application();
        $app->get('/', 'Slimfra\Tests\Mock\TestController::testRequest');
        ob_start();
        $app->run(Request::create("/"));
        $body = ob_get_contents();
        ob_end_clean();
        $this->assertSame("Success", $body);
    }
    
}