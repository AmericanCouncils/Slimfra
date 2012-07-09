<?php

namespace Slimfra\Tests;

use Slimfra\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
	
	public function testInstantiateEmpty() {
		$app = new Application();
		$this->assertNotNull($app);
		$this->asserTrue($app instanceof Application);
	}
	
	public function testInstantiateWithConfig() {
		$app = new Application();
		$this->assertNotNull($app);
		$this->asserTrue($app instanceof Application);
	}
	
	public function testInstantiateWithFilepath() {
		$app = new Application();
		$this->assertNotNull($app);
		$this->asserTrue($app instanceof Application);
	}
	
	
}