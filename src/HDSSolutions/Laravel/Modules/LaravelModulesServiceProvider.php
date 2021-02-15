<?php

namespace HDSSolutions\Laravel\Modules;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

final class LaravelModulesServiceProvider extends ModuleServiceProvider {

    protected function bootCli() {
        // capture db:seed command
        if ($this->consoleCommandContains([ 'db:seed', '--seed' ], [ '--class', 'help', '-h' ]))
            // capture command execution finished
            Event::listen(CommandFinished::class, function(CommandFinished $event) {
                // Accept command in console only, exclude all commands from Artisan::call() method.
                if ($event->output instanceof ConsoleOutput)
                    // execure modules seeds
                    $this->runModulesSeeds();
            });
    }

    public function register() {
        // register helpers
        if (file_exists($helpers = realpath(__DIR__.'/../../helpers.php')))
            //
            require_once $helpers;
    }


    /**
     * Get a value that indicates whether the current command in console
     * contains a string in the specified $fields.
     *
     * @param string|array $contain_options
     * @param string|array $exclude_options
     *
     * @return bool
     */
    protected function consoleCommandContains($contain_options, $exclude_options = null):bool {
        $args = Request::server('argv', null);
        if (is_array($args)) {
            $command = implode(' ', $args);
            if (
                Str::contains($command, $contain_options) &&
                ($exclude_options == null || !Str::contains($command, $exclude_options))
            ) {
                return true;
            }
        }
        return false;
    }

    public function runModulesSeeds() {
        // TODO: Run registered seeders
    }

}
