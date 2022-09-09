<?php

namespace Drupal\usfca_alert\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Alert Banner Block' block.
 *
 * @Block(
 *  id = "alert_banner_block",
 *  admin_label = @Translation("Alert banner block"),
 * )
 */
class AlertBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'alert_banner_block';
    $build['alert_banner_block']['#markup'] = '<span>Content inserted dynamically</span>';

    return $build;
  }

}
