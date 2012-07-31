<?php

namespace Slimfra;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;

/**
 * Slimfra Console automatically injects the Slimfra Application into
 * Slimfra Commands, making it accessible in Commands
 * the same as via a Controller
 *
 * @package Slimfra
 * @author Evan Villemez
 */
class Console extends ConsoleApplication
{
    protected $app;
    
    /**
     * Builds a console around an already-instantiated Slimfra Application
     *
     * @param \Slimfra\Application $app 
     */
    public function __construct(\Slimfra\Application $app)
    {
        $this->app = $app;
        
        $name = isset($app['app.name']) ? $app['app.name'] : "Slimfra Console";
        $version = isset($app['app.version']) ? $app['app.version'] : "Slimfra Console";
        
        parent::__construct($name, $version);
    }
    
    /**
     * {@inheritdoc}
     */
    public function add(Command $command)
    {
        if ($command instanceof \Slimfra\Command) {
            $command->setApp($this->app);
        }
        
        parent::add($command);
    }
    
}