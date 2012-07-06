<?php

namespace Slimfra;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

/**
 * The main application class for Slimfra, which is an extension of the Silex\Application
 *
 * @package Slimfra
 * @author Evan Villemez
 */
class Application extends BaseApplication {
	
	/**
	 * Array of registered provider class names
	 *
	 * @var array
	 */
	protected $registeredProviders = array();
    
	/**
	 * Set default configuration, and process overrides.  Setup custom services and providers.
	 *
	 * @param mixed $configData - can be either an array of configuration, or a string path to a config file
	 */
    public function __construct($configData = null) {
		$app = $this;

		//debug is false unless set explicitly in configuration
		$this['debug'] = false;
		
		//set the error handler
		set_error_handler(array($this, 'handleError'));

        //bootstrap Silex
        parent::__construct();
		
		//set custom/overridden services
        $this['resolver'] = $this->share(function () use ($app) {
            return new ControllerResolver($app, $app['logger']);
        });
        
        //register custom config
        $config = $this->getDefaultConfigs();
        if(is_array($configData)) {
            $config = array_merge($config, $configData);
        } else if(is_string($configData) && file_exists($configData)) {
            $config = array_merge($config, include($configData));
        }
        foreach($config as $key => $val) {
            $this[$key] = $val;
        }
		
		//check for routes in config
		if(isset($this['routes'])) {
			foreach($this['routes'] as $def => $controller) {
				$exp = explode(" ", $def);
				$method = strtolower($exp[0]);
				$pattern = $exp[1];
				$this->$method($pattern, $controller);
			}
		}
        
        //register custom providers
		$this->registerDefaultProviders();
    }
	
     /**
      * Keep track of registered providers.
      *
	  * {@inheritdoc}
      */
     public function register(ServiceProviderInterface $provider, array $values = array())
     {
		 $this->registeredProviders[get_class($provider)] = true;
		 
		 parent::register($provider, $values);
     }
	 
	 /**
	  * Check whether or a provider has been registered based on class name.
	  *
	  * @param string $providerClass 
	  * @return Boolean
	  */
	 public function registered($providerClass) {
		 return isset($this->registeredProviders[$providerClass]);
	 }
	
	/**
	 * Get a container service.
	 *
	 * @param string $id 
	 * @return mixed
	 */
    public function getService($id) {
        return $this[$id];
    }
    
	/**
	 * Get a config parameter, optionally returning a default value.
	 *
	 * @param string $id 
	 * @param mixed $default 
	 * @return mixed
	 */
    public function getParameter($id, $default = null) {
        return isset($this[$id]) ? $this[$id] : $default;
    }

	/**
	 * Register default configuration values, can be overriden via config files or the constructor.  Mainly just sets file paths.
	 *
	 * @return array
	 */
    protected function getDefaultConfigs() {
		$cwd = getcwd();
		
		//define default paths first
		$configs = array(
			'root_dir' => $cwd,
			'cache_dir' => $cwd."/app/cache",
			'templates_dir' => $cwd."/templates",
			'data_dir' => $cwd."/app/data",
			'config_dir' => $cwd."/app/config",
			'assets_dir' => $cwd."/assets",
			'uploads_dir' => $cwd."/uploads",
		);
		
		return $configs;
    }
	
	/**
	 * Register default providers.
	 */
	protected function registerDefaultProviders() {
		$this->register(new DoctrineServiceProvider(), array(
			'db.options' => array(
		        'driver'   => 'pdo_sqlite',
		        'path'     => $configs['data_dir'].'/db.sqlite',
			),
		));

		$this->register(new SessionServiceProvider(), array(
			'session.storage.save_path' => $configs['cache_dir']."/sessions",	
		));

		$this->register(new TwigServiceProvider(), array(
			'twig.path' => $this['templates_dir'],
		    'twig.options' => array('cache' => $this['cache_dir']."/twig"),
		));
		
		$this->register(new HttpCacheServiceProvider(), array(
			'http_cache.cache_dir' => $this['cache_dir']."/http_cache",
		));
	}
	
	/**
	 * A generic error handler, will convert errors into exceptions in debug mode.
	 *
	 * @param int $errno 
	 * @param string $errstr 
	 * @param string $errfile 
	 * @param int $errline 
	 * @return void
	 * @throws ErrorException
	 */
	public function handleError($errno, $errstr, $errfile, $errline) {
		if($this['debug'] === false) {
			return;
		}

		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}

}