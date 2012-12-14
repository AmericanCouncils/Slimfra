<?php

namespace Slimfra\Tests;

use Slimfra\Application;
use Symfony\Component\HttpFoundation\Request;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
	
    public function setUp()
    {
        @unlink(__DIR__."/app");
    }
    
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
    
    public function testBootstrapWithAllDefaultServices()
    {
        chdir(__DIR__);
        mkdir(__DIR__."/templates");
        $app = new Application(array(
            'app.service.db' => true,
            'app.service.mail' => true,
            'app.service.cache' => true,
            'app.service.forms' => true,
            'app.service.logging' => true,
            'app.service.sessions' => true,
            'app.service.validation' => true,
            'app.service.http_cache' => true,
            'app.service.templating' => true,
            'app.service.translation' => true,
        ));
        
        //request all services, no exceptions should be thrown
        $s = $app['db'];
        $s = $app['mailer'];
        $s = $app['twig'];
        $s = $app['cache'];
        $s = $app['form.factory'];
        $s = $app['logger'];
        $s = $app['validator'];
        $s = $app['http_cache'];
        $s = $app['translator'];
        
        rmdir(__DIR__."/templates");
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