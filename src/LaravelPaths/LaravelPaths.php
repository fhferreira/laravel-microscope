<?php

namespace Imanghafoori\LaravelMicroscope\LaravelPaths;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;
use Imanghafoori\LaravelMicroscope\Analyzers\FilePath;

class LaravelPaths
{
    public static function factoryDirs()
    {
        return app()->make(Factory::class)->loadedPaths;
    }

    public static function migrationDirs()
    {
        // normalize the migration paths
        $migrationDirs = [];

        foreach (app('migrator')->paths() as $path) {
            // Excludes the migrations within "vendor" folder:
            if (! Str::startsWith($path, [base_path('vendor')])) {
                $migrationDirs[] = FilePath::normalize($path);
            }
        }

        $migrationDirs[] = app()->databasePath().DIRECTORY_SEPARATOR.'migrations';

        return $migrationDirs;
    }

    /**
     * Check given path should be ignored.
     *
     * @param string $path
     * @return bool
     */
    public static function isIgnored($path)
    {
        $ignorePatterns = config('microscope.ignore');
        if (is_array($ignorePatterns) == false || is_string($path) == false) {
            return false;
        }

        foreach ($ignorePatterns as $ignorePattern) {
            if (fnmatch(rtrim(base_path(), '\\/').'/'.ltrim($ignorePattern, '\\/'), $path)) {
                return true;
            }
        }

        return false;
    }
}
