<?php

namespace Slimfra;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Slimfra\Provider\CacheServiceProvider;
    
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
	 * @param string $rootDir - project root directory, by default is whatever directory returned from 'getcwd()'
	 */
    public function __construct($configData = null, $rootDir = null) {
		$app = $this;
        
		//set the error handler
		set_error_handler(array($this, 'handleError'));

        //bootstrap Silex first
        parent::__construct();
		
		//set default configuration
		$this['debug'] = false;
        $this['root_dir'] = (is_null($rootDir)) ? getcwd() : (string) $rootDir;
        
        //register default paths first
        foreach ($this->getDefaultPaths() as $key => $val) {
            $this[$key] = $val;
        }
        
        //register default service provider configurations
        foreach ($this->getDefaultConfiguration() as $key => $val) {
            $this[$key] = $val;
        }
        
        //now register custom configuration, either received as an array, or a path to a php configuration file
        if ($configData) {
            $config = (is_array($configData)) ? $configData : require($configData);
            foreach ($config as $key => $val) {
                $this[$key] = $val;
            }
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
        
		//set custom/overridden Silex services
        $this['resolver'] = $this->share(function () use ($app) {
            return ($app['app.service.logging']) ? new ControllerResolver($app, $app['monolog']) : new ControllerResolver($app);
        });

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
	 * Register default path settings, can be overriden via config files or the constructor.
	 *
	 * @return array
	 */
    protected function getDefaultPaths() {
        $this['root_dir'] = getcwd();

		//define default paths first
		$configs = array(
			'cache_dir' => $this['root_dir']."/app/cache",
			'templates_dir' => $this['root_dir']."/templates",
			'data_dir' => $this['root_dir']."/app/data",
			'config_dir' => $this['root_dir']."/app/config",
			'assets_dir' => $this['root_dir']."/assets",
			'uploads_dir' => $this['root_dir']."/uploads",
		);
        
		return $configs;
    }
	
    /**
     * Registers default configuration used by providers, can be overriden via constructor.
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return array(
            //Slimfra app settings
            'app.name' => "Slimfra Project",
            
            //services enabled by default
            'app.service.cache' => true,
            'app.service.templating' => true,
            
            //services disabled by default
            'app.service.db' => false,
            'app.service.mail' => false,
            'app.service.forms' => false,
            'app.service.logging' => false,
            'app.service.sessions' => false,
            'app.service.validation' => false,
            'app.service.http_cache' => false,
            'app.service.translation' => false,
            
            //service provider default configs
            'db.options' => array(
		        'driver'   => 'pdo_sqlite',
		        'path'     => $this['data_dir'].'/db.sqlite',
            ),
            'session.storage.save_path' => $this['cache_dir']."/sessions",
            'twig.path' => $this['templates_dir'],
            'twig.options' => array('cache' => $this['cache_dir']."/twig"),
            'http_cache.cache_dir' => $this['cache_dir']."/http_cache",
            'monolog.logfile' => $this['root_dir']."/app/app.log",
            'monolog.level' => 4,
            'monolog.name' => 'app',
            'cache.dir' => $this['cache_dir']."/app",
            'swiftmailer.options' => null,
            'form.secret' => null,
            'locale' => 'en',
            'locale_fallback' => 'en',
            'translator.domains' => null,
        );
    }
	
	/**
	 * Register default service providers.
	 */
	protected function registerDefaultProviders() {
        //register this anyway
		$this->register(new HttpCacheServiceProvider(), array(
			'http_cache.cache_dir' => $this['http_cache.cache_dir'],
		));

        // all other service providers are optional, and can be enabled/disabled via the
        //corresponding `app.service.SERVICE_NAME` config
        
        if ($this['app.service.db']) {
    		$this->register(new DoctrineServiceProvider(), array(
    			'db.options' => $this['db.options'],
    		));
        }
        
        if ($this['app.service.templating']) {
    		$this->register(new TwigServiceProvider(), array(
    			'twig.path' => $this['twig.path'],
    		    'twig.options' => $this['twig.options'],
    		));
        }
		
        if ($this['app.service.logging']) {
            $this->register(new MonologServiceProvider(), array(
                'monolog.logfile' => $this['monolog.logfile'],
                'monolog.level' => $this['monolog.level'],
                'monolog.name' => $this['monolog.name']
            ));
        }
        
        if ($this['app.service.mail']) {
            $this->register(new SwiftmailerServiceProvider(), array(
                'swiftmailer.options' => $this['swiftmailer.options']
            ));
        }
        
        if ($this['app.service.sessions']) {
    		$this->register(new SessionServiceProvider(), array(
    			'session.storage.save_path' => $this['session.storage.save_path'],
    		));
        }
        
        if ($this['app.service.validation']) {
            $this->register(new ValidatorServiceProvider());
        }
        
        if ($this['app.service.forms']) {
            $this->register(new FormServiceProvider(), array(
                'form.secret' => $this['form.secret']
            ));
        }
        
        if ($this['app.service.translation']) {
            $this->register(new TranslationServiceProvider(), array(
                'translator.domains' => $this['translator.domains'],
                'locale' => $this['locale'],
                'locale_fallback' => $this['locale_fallback']
            ));
        }
        
        //now register custom Slimfra providers        

        if ($this['app.service.cache']) {
            $this->register( new CacheServiceProvider(), array(
                'cache.path' => $this['cache.dir'],
            ));
        }
	}
	
	/**
	 * A generic error handler, will convert errors into exceptions in debug mode.
	 *
	 * @param int $errno 
	 * @param string $errstr 
	 * @param string $errfile 
	 * @param int $errline 
	 * @throws ErrorException
	 */
	public function handleError($errno, $errstr, $errfile, $errline) {
		if(!$this['debug']) {
			return;
		}

		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}

}