<?php

namespace HDSSolutions\Laravel\Modules;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;

abstract class ModuleServiceProvider extends \Illuminate\Support\ServiceProvider {

    protected array $globalMiddlewares = [];

    protected string $middlewaresGroup = 'web';

    protected array $middlewares = [];

    /**
    * Publishes configuration file.
    *
    * @return  void
    */
    public final function boot(Router $router, Kernel $kernel) {
        // normal boot
        $this->bootEnv();
        // boot for console
        if ($this->app->runningInConsole()) $this->bootCli();
        // register global middlewares
        foreach ($this->globalMiddlewares as $middleware)
            // register middleware on web group
            $kernel->pushMiddleware($middleware);
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
