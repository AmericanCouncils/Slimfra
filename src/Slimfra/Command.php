<?php

namespace Slimfra;

use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand implements \ArrayAccess
{
	protected $app;

	/**
	 * Get the Slimfra Application
	 *
	 * @return Slimfra\Application
	 */
    public function getApp() {
        return $this->app;
    }
    
	/**
	 * Set the Slimfra App
	 *
	 * @param Application $app 
	 */
    public function setApp(Application $app) {
        $this->app = $app;
    }
	
	/**
	 * Implements \ArrayAccess for accessing '$this->app' for configuration and services
	 */
    public function offsetExists($key) {
        return isset($this->app[$key]);
    }
    
	/**
	 * Implements \ArrayAccess for accessing '$this->app' for configuration and services
	 */
    public function offsetGet($key) {
        return $this->app[$key];
    }
    
	/**
	 * Implements \ArrayAccess for accessing '$this->app' for configuration and services
	 */
    public function offsetSet($key, $val) {
        return $this->app[$key] = $val;
    }
    
	/**
	 * Implements \ArrayAccess for accessing '$this->app' for configuration and services
	 */
    public function offsetUnset($key) {
        unset($this->app[$key]);
    }
    
	/**
	 * A more verbose method for requesting container services
	 * 
	 * @param string $id - id of service
	 */
    protected function getService($id) {
        return $this->app->getService($id);
    }

	/**
	 * A more verbose method for requested config, and optionally specifying a default value
	 *
	 * @param string $id - id of config value to return
	 * @param mixed $default - default value to return if requested id does not exist
	 * @return mixed
	 */
    protected function getParameter($id, $default = null) {
        return $this->app->getParameter($id, $default);
    }
    
}
