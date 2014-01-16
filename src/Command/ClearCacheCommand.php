<?php

namespace Slimfra\Command;

use Slimfra\Command as Base;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Cache\CacheProvider;

class ClearCacheCommand extends Base
{
    protected function configure()
    {
        $this->setName('cache:clear')
            ->setDescription('Clears data stored by the `cache` service.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this['cache']) {
            $output->writeln('No cache configured, exiting.');

            return;
        }

        $cache = $this['cache'];

        if ($cache instanceof CacheProvider) {
            $cache->flushAll();
        }

        $output->writeln('Application cache flushed.');
    }
}
