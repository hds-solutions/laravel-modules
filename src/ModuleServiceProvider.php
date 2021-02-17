<?php

namespace HDSSolutions\Laravel\Modules;

abstract class ModuleServiceProvider extends \Illuminate\Support\ServiceProvider {

    /**
    * Publishes configuration file.
    *
    * @return  void
    */
    public final function boot() {
        // normal boot
        $this->bootEnv();
        // boot for console
        if ($this->app->runningInConsole()) $this->bootCli();
    }

    protected function bootEnv():void {}

    protected function bootCli():void {}

    protected final function loadSeedersFrom(string|array $paths):void {
        // register paths on ModulesManager
        app()->make(ModulesManager::class)->register($paths);
    }

}
