<?php

namespace ShakeTheNations\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InteractiveCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('ask')
            ->setDescription('(interactive) Get sismic events news')
            ->setHelp("
                The <info>shake ask</info> interactive command allows you to determine
                <comment>from where</comment> and <comment>for wich period</comment>
                you want to fetch infos about sismic event
                ");
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->app['app.signature']);
        $output->writeln(array(
            '',
            ' Welcome to the ShakeTheNations interactive cli-tool',
            ''
        ));
        $dialog = $this->getHelperSet()->get('dialog');
        $location = $input->getArgument('location') ?: $dialog->askAndValidate(
            $output,
            "Please, type your <info>location</info>"
            ." (e.g. <comment>Nantes, France</comment>)"
            ."\n > ",
            function ($location) use ($input) {
                $input->setArgument('location', $location);
            }
        );
        $distance = $input->getArgument('distance') ?: $dialog->askAndValidate(
            $output,
            "Please, type the max <info>distance</info> (kilometers)"
            ." (e.g. <comment>500</comment>)"
            ."\n > ",
            function ($distance) use ($input) {
                $input->setArgument('distance', $distance);
            }
        );
    }
}
