<?php

namespace Slimfra\Provider;

class UserServiceProvider implements ServiceProviderInterface {

	public function register(Application $app) {

		//user service, gets user based on session
		$app['user'] = $app->share(function() use ($app) {
			//check session first
			
		});
		
		//user provider service
		$app['user.provider'] = $app->share(function () use ($app) {
			//do stuff
		});
	}
	
	public function boot(Application $app) {
		
	}

}