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

use ShakeTheNations\Helpers\Shake;

use Geo\Service\GoogleMap\GeoCode;

class InteractiveCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('ask')
            ->setDescription('(interactive) Get sismic events news')
            ->setDefinition(array(
                new InputArgument(
                    'location', InputArgument::REQUIRED, "Your location"
                ),
            )
        );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $location = $input->getArgument('location');
        // These validators would throw exceptions
        try {
            $notEmpty = Validator::validateNonEmptyString('location', $location);
            $position = Validator::validateGeocodable($location);
        } catch (Exception $e) {
            $output->writeln($e);
            return false;
        }
        $output->writeln(
            sprintf("Looking from some shake around %s (distance max: %d km)",
            $location, Shake::DEFAULT_DISTANCE));

        $lat =  $position['answer']['lat'];
        $lng = $position['answer']['lng'];
        $output->writeln(sprintf("%s: %F;%F",$location,$lat, $lng));

        $shakes = Shake::getAround($location, $lat, $lng);


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
                // These validators would throw exceptions
                $input->setArgument('location', $location);
            }
        );
    }
}
