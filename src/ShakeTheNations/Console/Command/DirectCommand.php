<?php

namespace ShakeTheNations\Console\Command;

use Symfony\Component\Console\Command\Command;

class DirectCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('from')
            ->setDescription('(with arguments) Get sismic events news around a given location');

        return true;
    }
}
