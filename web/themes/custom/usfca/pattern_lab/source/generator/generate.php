<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/util.php';

require_once __DIR__ . '/generate_pattern_lab.php';
require_once __DIR__ . '/generate_aem.php';

use Symfony\Component\Yaml\Yaml;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

define('SHOULD_GENERATE_AEM', FALSE);

$generate_cmd = new Commando\Command();

$generate_cmd->option()
  ->require()
  ->describedAs('The component to generate');

$generate_cmd->option('r')
  ->aka('recurse')
  ->describedAs('Recursively generate child components, e.g. fields')
  ->boolean();

$generate_cmd->option('f')
  ->aka('force')
  ->describedAs('Force generation of patterns that already exist')
  ->boolean();

$generate_cmd->option('a')
->aka('all')
->describedAs('Generate all components for specified path')
->boolean();

$generate_cmd->option('n')
->aka('no-styles')
->describedAs('Omit stylesheets')
->boolean();

$generate_cmd->option('v')
  ->aka('verbose')
  ->describedAs('Output logs')
  ->boolean();

$source_filename = $generate_cmd[0];

$recurse = $generate_cmd['recurse'];

$force = $generate_cmd['force'];

$all = $generate_cmd['all'];

$noStyles = $generate_cmd['no-styles'];

$loglevel = $generate_cmd['verbose'] ? Logger::DEBUG : Logger::INFO;

$log = new Logger('create');

$log->pushHandler(new StreamHandler('php://stdout', $loglevel));

function generate_file($source_filename, $force = FALSE) {
  global $noStyles;
  global $log;

  if (strpos($source_filename, '.yml') === FALSE) {
    $source_yaml_filename = $source_filename . '.yml';
  }
  else {
    $source_yaml_filename = $source_filename;
  }

  if (file_exists(__DIR__ . '/' . $source_yaml_filename)) {
    $source_yaml_string = file_get_contents(__DIR__ . '/' . $source_yaml_filename);

    $definition_data = Yaml::parse($source_yaml_string);

    generate_pattern_lab($definition_data, $force, $noStyles);

    if (SHOULD_GENERATE_AEM) {
      generate_aem_component($definition_data, $force);
    }
  }
  else {
    $log->err("YAML file does not exist: " . $source_yaml_filename);
  }

  $log->info("Generated " . $source_filename);
}

function generate_directory($source_directory, $force) {
  global $log;

  if (is_dir($source_directory)) {
    $files = glob($source_directory . '/*.{yml}', GLOB_BRACE);

    foreach($files as $file) {
      generate_file($file, $force);
    }
  }
  else {
    $log->error('Source is not a directory: ' . $source_directory);
  }
}

function generate($source, $force = FALSE) {
  global $all;
  global $log;

  if (!$all) {
    generate_file($source, $force);
  }
  else {
    generate_directory($source, $force);
  }
}

generate($source_filename, $force);

exit(0);
