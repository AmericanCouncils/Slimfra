<?php

namespace Slimfra\Tests;

use Slimfra\Application;

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
	
    public function testConfigOverwritesDefaults() {
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
}