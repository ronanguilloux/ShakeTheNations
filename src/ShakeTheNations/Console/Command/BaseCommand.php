<?php

namespace ShakeTheNations\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use ShakeTheNations\Helpers\Validator;
use ShakeTheNations\Helpers\Shake;

abstract class BaseCommand extends Command
{
    protected $app;

    public function getApp()
    {
        return $this->app;
    }

    protected function initialize(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->app = $this->getApplication()->getApp();
    }

    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument(
                    'location', InputArgument::REQUIRED, "Your location"
                ),
                new InputArgument(
                    'distance', InputArgument::OPTIONAL, "(optional) Maximum search distance area from your location"
                )
            )
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $location = $input->getArgument('location');
        $distance = $input->getArgument('distance');
        // Picking up the defaut value for the optional arg
        $distance = (empty($distance)) ? Shake::DEFAULT_DISTANCE : $distance;
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
            $location, $distance));

        $position = $this->app->get('geocoder')->geocode($location);
        $lat = $position['latitude'];
        $lng = $position['longitude'];

        $output->writeln(sprintf("%s: %f;%f",$location,$lat, $lng));
        $foo = $this->app->get('feeder');
//        var_dump($foo);die();
        $bar = $foo->fetch($location, $lat, $lng);

        $shakes = Shake::getAround($location, $lat, $lng);
    }

    public function asText()
    {
        $app = $this->getApplication()->getApp();
        $txt = $app['app.signature']
            ."\n"
            .parent::asText();

        return $txt;
    }
}
