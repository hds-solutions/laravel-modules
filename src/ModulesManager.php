<?php

namespace HDSSolutions\Laravel\Modules;

final class ModulesManager {

    private array $seeders_paths = [];

    public function register(string|array $paths) {
        // save paths
        $this->seeders_paths += is_array($paths) ? array_values($paths) : [ $paths ];
    }

    public function getPaths():array {
        return $this->seeders_paths;
    }

}