<?php

namespace ShakeTheNations\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use ShakeTheNations\DependencyInjection\Application;
use ShakeTheNations\Helpers\Validator;

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
            ))
            //->setHelp(file_get_contents(__DIR__.'/Resources/SismicCommandHelp.txt'))
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = Validator::validateNonEmptyString(
            'location', $input->getArgument('location')
        );
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
            "\n Please, type your <info>location</info>"
            ." (e.g. <comment>Nantes, France</comment>)"
            ."\n > ",
            function ($location) {
              $notEmpty = Validator::validateNonEmptyString('location', $location);
              $geocodable = Validator::validateGeocodable($location);
                    return ($notEmpty && $geocodable);
            }
        );
        $input->setArgument('location', $location);
    }
}
