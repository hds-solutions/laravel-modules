<?php declare(strict_types=1);

namespace HDSSolutions\Laravel\Modules;

use Illuminate\Support\Collection;

final class ModulesManager {

    public function __construct(
        private readonly ?Collection $seeders_paths = new Collection(),
    ) {}

    public function register(string | array $paths): void {
        // foreach paths
        foreach ((array) $paths as $path) {
            // push path to collection
            $this->seeders_paths->push($path);
        }
    }

    public function getPaths(): array {
        return $this->seeders_paths->toArray();
    }

}
