<?php

namespace ShakeTheNations\Console;

use Symfony\Component\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

use ShakeTheNations\Console\Command\DirectCommand;
use ShakeTheNations\Console\Command\InteractiveCommand;

use ShakeTheNations\DependencyInjection\Application;

class ConsoleApplication extends SymfonyConsoleApplication
{
    private $app;

    public function getApp()
    {
        return $this->app;
    }

    public function __construct($app)
    {
        $this->app = $app;

        parent::__construct('shakethenations', $this->app->getVersion());

        $this->add(new DirectCommand());
        $this->add(new InteractiveCommand());
        $this->definition = new InputDefinition(array(
            new InputArgument(
                'command', InputArgument::REQUIRED, 'The command to execute'
            ),
            new InputOption(
                '--help', '-h', InputOption::VALUE_NONE, 'Shows this help message'
            ),
        ));
    }

    public function getHelp()
    {
        $help = array(
            $this->app['app.signature'],
            'Tool to get sismic news around you.',
            'To get help about a command, type <info>shake [command] --help</info>'
        );

        return implode("\n", $help);
    }
}
