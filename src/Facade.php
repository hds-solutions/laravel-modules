<?php declare(strict_types=1);

namespace HDSSolutions\Laravel\Modules;

final class Facade extends \Illuminate\Support\Facades\Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string {
        return 'modules';
    }

}
