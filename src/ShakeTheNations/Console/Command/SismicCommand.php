<?php

namespace ShakeTheNations\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use ShakeTheNations\DependencyInjection\Application;
use ShakeTheNations\Helpers\Validator;

use ShakeTheNations\Helpers\Geo;

class SismicCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('get')
            ->setDescription('Get sismic news around you')
            ->setDefinition(array(
                new InputArgument(
                    'location', InputArgument::REQUIRED, "Your location"
                ),
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tuple = each($input->getArgument('location'));
        $location = $tuple['key'];
        $position = $tuple['value'];
        var_export($position);
        echo " TODO : transfrom 47.21, -1.55 in 45.21/48.21 & -3.55/0.55";
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

        // check `location` argument
        $location = $input->getArgument('location') ?: $dialog->askAndValidate(
            $output,
            "Please, type your <info>location</info>"
            ." (e.g. <comment>Nantes, France</comment>)"
            ."\n > ",
            function ($location) {
                // These validators would throw exceptions
                $notEmpty = Validator::validateNonEmptyString('location', $location);
                $geocoded = Validator::validateGeocodable($location);
                return array($location => $geocoded);;
            }
        );
        $input->setArgument('location', $location);
    }
}
