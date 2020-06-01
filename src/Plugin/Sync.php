<?php

namespace Drupal\neg_instagram_widget\Plugin;

use Drupal\neg_instagram_widget\Instagram;
use Drupal\neg_instagram_widget\Settings;

/**
 * Class Sync.
 */
class Sync {

  /**
   * Implements Constructor.
   */
  public function __construct() {
  }

  /**
   * Syncs the calendar.
   */
  public function sync() {

    $config = Settings::editableConfig();

    $items = Instagram::getMedia();

    // Set reviews.
    \Drupal::state()->set('neg_instagram.posts', $items);

    Settings::log('Fetched %c posts from Instagram', [
      '%c' => count($items),
    ], 'notice');

    // Set last_full_sync.
    $config->set('last_sync', time());

    $config->save();

    // Invalidate Cache Tags.
    Settings::invalidateCache();
    return TRUE;
  }

}
