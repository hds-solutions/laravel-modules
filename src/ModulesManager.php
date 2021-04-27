<?php

namespace HDSSolutions\Laravel\Modules;

use Illuminate\Support\Collection;

final class ModulesManager {

    public function __construct(
        private Collection|null $seeders_paths = null
    ) {
        $this->seeders_paths = collect();
    }

    public function register(string|array $paths) {
        // foreach paths
        collect(is_array($paths) ? array_values($paths) : [ $paths ])
            // push path to collection
            ->each(fn($path) => $this->seeders_paths->push( $path ));
    }

    public function getPaths():array {
        return $this->seeders_paths->toArray();
    }

}
