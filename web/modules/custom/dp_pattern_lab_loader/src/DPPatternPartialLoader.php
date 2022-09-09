<?php

namespace Drupal\dp_pattern_lab_loader;

use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;

// Extend the pattern lab native loader with custom overrides:
class DPPatternPartialLoader extends PatternPartialLoader {

  /**
   * The app root.
   *
   * @var string
   */
  protected $root;

  /**
   * Injected service.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * Injected service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface;
   */
  protected $logger;

  // Only intercept Timber calls having this namespace:
  private $patternLabNamespace = '@pattern_lab';

  // Do not allow 'template' or 'layout' lookups:
  private $patternLabEligiblePatternTypes = [
    'base',
    'fields',
    'components',
  ];

  /**
   * DPPatternPartialLoader constructor.
   *
   * @param string $root
   *   The app root.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $themeManager
   *   Injected themeManager service.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   Injected logging service.
   * @param string $root
   *   The app root.
   *
   * @throws \Exception
   */
  public function __construct($root, ThemeManagerInterface $themeManager, LoggerChannelInterface $logger) {
    $this->themeManager = $themeManager;
    $this->logger = $logger;
    $this->root = $root;

    $theme = $this->themeManager->getActiveTheme();

    if (!empty($theme)) {
      // Path to pattern lab subdirectory containing all Pattern Lab files:
      $patternlabDir = $theme->getPath() . '/pattern_lab';

      if (!is_dir($this->root . '/' . $patternlabDir)) {
        foreach ($theme->getBaseThemeExtensions() as $base_theme) {
          // The returned base theme array is intentionally ordered
          // to walk up the dependency chain in order.
          if (is_dir($this->root . '/' . $base_theme->getPath() . '/pattern_lab')) {
            $patternlabDir = $base_theme->getPath() . '/pattern_lab';
            break;
          }
        }
      }

      // Path to just the patterns:
      $patternsPath = $patternlabDir . '/source/_patterns';

      // Parse all of the patterns into a keyed array of [pattern_type][pattern_name][relative_path] paths:
      $pathsUtil = new DPPatternPathsUtil();

      try {
        $patternPaths = $pathsUtil->parse($patternsPath);

        parent::__construct($patternsPath, ['patternPaths' => $patternPaths]);

        // Add the paths to the loader again under the 'pattern_lab' namespace:
        $this->addPath($patternsPath, 'pattern_lab');
      }
      catch (\Exception $e) {
          if (is_dir($this->root . '/' . $patternlabDir)) {
              $message = "DPPatternPartialLoader: ";
              $message .= $e->getMessage();
              $this->logger->error($message);
          }
      }
    }
  }

  /**
   * Custom method only looks up some patterns in pattern lab:
   */
  private function shouldDoPatternLabLookup($name) {
    $shouldDoLookup = FALSE;

    if (strpos($name, $this->patternLabNamespace) === 0) {
      $shouldDoLookup = TRUE;
    }
    else {
      foreach ($this->patternLabEligiblePatternTypes as $patternType) {
        if (strpos($name, $patternType . '-') === 0) {
          $shouldDoLookup = TRUE;
        }
      }
    }

    return $shouldDoLookup;
  }

  /**
   * Custom method strips the twig namespace so pattern lab can find it:
   */
  private function normalizePatternLabName($name) {
    if (strpos($name, $this->patternLabNamespace) === 0) {
      // Strip the namespace and trailing slash, e.g. '@pattern_lab/':
      $name = substr($name, strlen($this->patternLabNamespace) + 1);
    }

    return $name;
  }

  /**
   * {@inheritdoc}
   */
  public function exists($name) {
    if ($this->shouldDoPatternLabLookup($name)) {
      $name = $this->normalizePatternLabName($name);

      return parent::exists($name);
    }
    else {
      return false;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function findTemplate($name) {
    if (strpos($name, $this->patternLabNamespace) === 0) {
      $name = substr($name, strlen($this->patternLabNamespace) + 1);
    }

    return parent::findTemplate($name);
  }

}
