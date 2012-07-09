<?php

namespace Slimfra\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class UserAuthenticationServiceProvider implements ServiceProviderInterface {
	
	/**
	 * {@inheritdoc}
	 */
	public function register(Application $app) {
		//route for loging in
		$app->get("/authenticate", function() use ($app) {
			//TODO
		});
		
		//route for processing login
		$app->post("/authenticate", function() use ($app) {
			//TODO
		});

		//authenticator service
		$app['user.authenticator'] = $app->share(function() use ($app) {
			return new Slimfra\User\Authenticator();
		});
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function boot(Application $app) {
				
		//register exception handler if user isn't logged in
		$app['dispatcher']->registerListener(KernelEvents::EXCEPTION, function (\Exception $e) use ($app) {
			if($e instanceof Slimfra\Exception\AuthenticationRequiredException) {
				$message = (is_null($e->getMessage())) ? "You must log in before viewing the requested material." : $e->getMessage();

				$app['session']->setFlash($message);
				
				return $app->redirect(sprintf("/authenticate?continue=%s", $app['request']->getPathInfo()));
			}
		});
	}
}