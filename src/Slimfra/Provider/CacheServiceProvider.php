<?php

namespace Slimfra\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Doctrine\Common\Cache\FilesystemCache;

/**
 * Provides an application-level cache service via Doctrine Common's cache component. Defaults to file-based cache.
 *
 * @package Slimfra
 * @author Evan Villemez
 */
class CacheServiceProvider implements ServiceProviderInterface
{
	
	public function register(Application $app)
	{
		$app['cache'] = $app->share(function($c) {
            return new FilesystemCache($c['cache.path']);
        });
	}
    
    public function boot(Application $app)
    {
        
    }
	
}