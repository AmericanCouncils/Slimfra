<?php

namespace Slimfra\Tests;

use Slimfra\Application;
use Slimfra\Console;
use Slimfra\Provider\CacheServiceProvider;
use Slimfra\Command\ClearCacheCommand;
use Symfony\Component\Console\Tester\CommandTester;

class CacheProviderAndCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function createApp()
    {
        $app = new Application();

        $app->register(new CacheServiceProvider(), array(
            'cache.path' => sys_get_temp_dir().'/SlimfraTests/cache'
        ));

        return $app;
    }

    public function testCacheService()
    {
        $app = $this->createApp();
        $cache = $app['cache'];

        $cache->save('foo', 'bar');

        $this->assertSame('bar', $cache->fetch('foo'));
    }

    /**
     * @depends testCacheService
     * @return [type] [description]
     */
    public function testClearCacheCommand()
    {
        $console = new Console($this->createApp());
        $console->add(new ClearCacheCommand);

        $command = $console->find('cache:clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/Application cache flushed/', $commandTester->getDisplay());
    }
}
