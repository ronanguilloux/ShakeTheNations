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

class DirectCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('from')
            ->setDescription('(with arguments) Get sismic events news around a given location')
            ->setDefinition(array(
                new InputArgument(
                    'location', InputArgument::REQUIRED, "Your location"
                ),
            )
        );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->app['app.signature']);
        $output->writeln(array(
            '',
            ' Welcome to the ShakeTheNations interactive cli-tool',
            ''
        ));
        $location = $input->getArgument('location');
        // These validators would throw exceptions
        try {
            $notEmpty = Validator::validateNonEmptyString('location', $location);
            $position = Validator::validateGeocodable($location);
        } catch (Exception $e) {
            $output->writeln($e);
        }
        $output->writeln(
            sprintf("Looking from some shake around %s (distance max: %d km)",
            $location, Shake::DEFAULT_DISTANCE));

        $lat =  $position['answer']['lat'];
        $lng = $position['answer']['lng'];

        $output->writeln(sprintf("%s: %F;%F",$location,$lat, $lng));
        $shakes = Shake::getAround($location, $lat, $lng);
    }
}