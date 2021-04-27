<?php

namespace HDSSolutions\Laravel\Modules;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

final class LaravelModulesServiceProvider extends ModuleServiceProvider {

    protected function bootCli():void {
        // capture db:seed command
        if ($this->consoleCommandContains([ 'db:seed', '--seed' ], [ '--class', 'help', '-h' ]))
            // capture command execution finished
            Event::listen(CommandFinished::class, function(CommandFinished $event) {
                // ignore sub-sequential calls
                if ($event->input->hasParameterOption('--class')) return;
                // Accept command in console only, exclude all commands from Artisan::call() method.
                if ($event->output instanceof ConsoleOutput)
                    // execure modules seeds
                    $this->runModulesSeeds( $event->output, $event->input->getParameterOption('--force') );
            });
    }

    public function register() {
        // register helpers
        if (file_exists($helpers = realpath(__DIR__.'/../../helpers.php')))
            //
            require_once $helpers;
        // register singleton
        app()->singleton(ModulesManager::class, fn() => new ModulesManager);
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

    private function runModulesSeeds(ConsoleOutput $console, $force = false) {
        // Run seeders from registered paths
        $console->writeln('<comment>Running seeders from modules</comment>');
        // foreach registered paths
        foreach (app()->make(ModulesManager::class)->getPaths() as $path) {
            // find Seeders on current path
            foreach (glob( $path.'/*.php') as $filename) {
                //
                require_once $filename;
                // get all Seeders class on current file
                foreach ($this->getClassesFromFile($filename) as $class) {
                    //
                    $console->writeln("<comment>Seeding:</comment> {$class}");
                    $start = microtime(true);
                    // execute artisan db:seed with specified class
                    Artisan::call('db:seed', [ '--class' => $class, '--force' => $force ], $console);
                    $elapsed = round(microtime(true) - $start, 2);
                    //
                    $console->writeln("<info>Seeded:</info> {$class} ({$elapsed}ms)");
                }
            }
        }
    }

    private function getClassesFromFile(string $filename):array {
        // Get namespace of class (if vary)
        $namespace = '';
        $lines = file($filename);
        $namespaceLines = preg_grep('/^namespace /', $lines);
        if (is_array($namespaceLines)) {
            $namespaceLine = array_shift($namespaceLines);
            $match = [];
            preg_match('/^namespace (.*);$/', $namespaceLine, $match);
            $namespace = array_pop($match);
        }

        // Get name of all class has in the file.
        $classes = [];
        $php_code = file_get_contents($filename);
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $class_name = $tokens[$i][1];
                $classes[] = $namespace !== '' ? $namespace . "\\$class_name": $class_name;
            }
        }

        return $classes;
    }

}
