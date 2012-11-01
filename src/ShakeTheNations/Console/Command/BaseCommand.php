<?php

namespace ShakeTheNations\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
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

    public function asText()
    {
        $app = $this->getApplication()->getApp();
        $txt = $app['app.signature']
               ."\n"
               .parent::asText();

        return $txt;
    }

/*
    private function registerEventSubscribers($dir, $namespace = '')
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = $this->app->get('finder')->files()
            ->name('*Plugin.php')
            ->in($dir)
        ;

        foreach ($files as $file) {
            $className = $file->getBasename('.php');  // strip .php extension
           $r = new \ReflectionClass($namespace.'\\'.$className);
            if ($r->implementsInterface('Symfony\\Component\\EventDispatcher\\EventSubscriberInterface')) {
                $this->app->get('dispatcher')->addSubscriber($r->newInstance());
            }
        }
    }
 */
}
