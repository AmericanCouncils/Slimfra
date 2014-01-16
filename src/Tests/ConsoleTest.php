<?php

namespace Slimfra\Tests;

use Slimfra\Application;
use Slimfra\Console;
use Symfony\Component\Console\Tester\CommandTester;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $console = new Console(new Application);
        $this->assertNotNull($console);
        $this->assertTrue($console instanceof Console);
    }

    public function testRunCommand()
    {
        $console = new Console(new Application(array("app.someconfig" => "success")));
        $console->add(new Mock\TestCommand);

        $command = $console->find('test');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/success/', $commandTester->getDisplay());
    }

}
