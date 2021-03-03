<?php

namespace HDSSolutions\Laravel\Modules;

use Illuminate\Routing\Router;

abstract class ModuleServiceProvider extends \Illuminate\Support\ServiceProvider {

    protected string $middlewaresGroup = 'web';

    protected array $middlewares = [];

    /**
    * Publishes configuration file.
    *
    * @return  void
    */
    public final function boot(Router $router) {
        // normal boot
        $this->bootEnv();
        // boot for console
        if ($this->app->runningInConsole()) $this->bootCli();
        // register middlewares
        foreach ($this->middlewares as $middleware)
            // register middleware on web group
            $router->pushMiddlewareToGroup($this->middlewaresGroup, $middleware);
    }

    protected function bootEnv():void {}

    protected function bootCli():void {}

    protected final function loadSeedersFrom(string|array $paths):void {
        // register paths on ModulesManager
        app()->make(ModulesManager::class)->register($paths);
    }

}
