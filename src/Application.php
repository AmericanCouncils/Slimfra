<?php

namespace Slimfra;

use Silex\Application as BaseApplication;

/**
 * The main application class for Slimfra, which is an extension of the Silex\Application
 *
 * @package Slimfra
 * @author Evan Villemez
 */
class Application extends BaseApplication
{
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
    public function __construct(array $configData = array())
    {
        //bootstrap Silex first
        parent::__construct($configData);

        $app = $this;

        //set default override configuration
        $this['debug'] = false;
        $this['root_dir'] = getcwd();

        //overrides controller resolver that checks specifically for
        //Slimfra controllers
        $this['resolver'] = $this->share(function () use ($app) {
            return new ControllerResolver($app);
        });

        //set the error handler
        set_error_handler(array($this, 'handleError'));
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
     public function registered($providerClass)
     {
         return isset($this->registeredProviders[$providerClass]);
     }

    /**
     * Get a container service.
     *
     * @param  string $id
     * @return mixed
     */
    public function getService($id)
    {
        return $this[$id];
    }

    /**
     * Get a config parameter, optionally returning a default value.
     *
     * @param  string $id
     * @param  mixed  $default
     * @return mixed
     */
    public function getParameter($id, $default = null)
    {
        return isset($this[$id]) ? $this[$id] : $default;
    }

    /**
     * A generic error handler, will convert errors into exceptions in debug mode.
     *
     * @param  int            $errno
     * @param  string         $errstr
     * @param  string         $errfile
     * @param  int            $errline
     * @throws ErrorException
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if (!$this['debug']) {
            return;
        }

        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

}
