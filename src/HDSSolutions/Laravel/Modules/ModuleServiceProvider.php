<?php

namespace HDSSolutions\Laravel\Modules;

abstract class ModuleServiceProvider extends \Illuminate\Support\ServiceProvider {

    /**
    * Publishes configuration file.
    *
    * @return  void
    */
    public final function boot() {
        // boot for console
        if ($this->app->runningInConsole()) $this->bootCli();
        // normal boot
        $this->bootEnv();
    }

    abstract protected function bootEnv():void {}

    protected function bootCli():void {}

}
