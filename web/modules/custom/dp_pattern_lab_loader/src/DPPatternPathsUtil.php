<?php

namespace Drupal\dp_pattern_lab_loader;

// Custom class assembles local directories to be parsed by pattern lab loader:
class DPPatternPathsUtil {

  // Strips leading 'numeric' directory prefixes:
  private function normalizeDirectoryName($dir) {
    $dir_exploded = explode('-', $dir);

    if (is_numeric($dir_exploded[0])) {
      array_shift($dir_exploded);
    }

    return implode('-', $dir_exploded);
  }

  public static function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (
      glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir
    ) {
      $files = array_merge($files,
        self::rglob($dir . '/' . basename($pattern), $flags));
    }
    return $files;
  }

  // Parse a given root path and return a nested array of *relative* .twig file paths:
  public function parse($patternsPath) {
    $parsed = [];

    $pattern_type_directories = glob($patternsPath . '/*' , GLOB_ONLYDIR);

    foreach ($pattern_type_directories as $pattern_type_directory) {
      $pattern_type = basename($pattern_type_directory);
      $pattern_type = $this->normalizeDirectoryName($pattern_type);

      $patterns = [];

      $pattern_directories = self::rglob($pattern_type_directory . '/*', GLOB_ONLYDIR);

      foreach ($pattern_directories as $pattern_directory) {
        $pattern_twig_files = glob($pattern_directory . '/*.twig');

        foreach ($pattern_twig_files as $pattern_twig_file) {
          $pathinfo = pathinfo($pattern_twig_file);

          $pattern_name = $pathinfo['filename'];
          $pattern_name = $this->normalizeDirectoryName($pattern_name);

          $pattern_directory_relative = substr($pattern_directory, strlen($patternsPath) + 1);

          $parsed[$pattern_type][$pattern_name] = $pattern_directory_relative . '/' . $pathinfo['basename'];
        }
      }
    }

    return $parsed;
  }

}
