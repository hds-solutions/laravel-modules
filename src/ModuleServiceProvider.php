<?php declare(strict_types=1);

namespace HDSSolutions\Laravel\Modules;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider {

    protected array      $globalMiddlewares = [];

    protected string     $middlewaresGroup  = 'web';

    protected array      $middlewares       = [];

    private ?AliasLoader $loader            = null;

    /**
     * Publishes configuration file.
     *
     * @return  void
     */
    final public function boot(Router $router, Kernel $kernel): void {
        // boot common environment
        $this->bootEnv();

        // check if app is running in console
        if ($this->app->runningInConsole()) {
            // boot console environment
            $this->bootCli();
        }

        // register global middlewares
        foreach ($this->globalMiddlewares as $middleware) {
            // register middleware on web group
            $kernel->pushMiddleware($middleware);
        }
        // register middlewares
        foreach ($this->middlewares as $middleware) {
            // register middleware on web group
            $router->pushMiddlewareToGroup($this->middlewaresGroup, $middleware);
        }
    }

    protected function bootEnv(): void {}

    protected function bootCli(): void {}

    final protected function loadSeedersFrom(string | array $paths): void {
        // register paths on ModulesManager
        app()->make(ModulesManager::class)->register($paths);
    }

    final protected function alias(string $alias, string $class): void {
        // register alias
        $this->getLoader()->alias($alias, $class);
    }

    private function getLoader(): AliasLoader {
        // init / return alias loader
        return $this->loader ??= AliasLoader::getInstance();
    }

}
