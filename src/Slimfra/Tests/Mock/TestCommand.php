<?php
namespace Slimfra\Tests\Mock;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class TestCommand extends \Slimfra\Command
{
    protected function configure()
    {
        $this->setName("test");
    }
    
	protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->app['app.someconfig']);
    }
}
