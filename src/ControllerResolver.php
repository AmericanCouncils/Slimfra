<?php

namespace Slimfra;

use Silex\ControllerResolver as SilexResolver;

/**
 * A minor modification to the default Silex ControllerResolver, which injects the app instance into
 * the controller, allowing Slimfra\Controller instances to reference the application via "$this->app".
 *
 * @package Slimfra
 * @author Evan Villemez
 */
class ControllerResolver extends SilexResolver
{
    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     *
     * @return mixed A PHP callable
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        //inject the app into the controller, if it's a Slimfra Controller
        $controller = new $class();
        if ($controller instanceof Controller) {
            $controller->setApp($this->app);
        }

        return array($controller, $method);
    }

}
