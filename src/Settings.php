<?php

namespace Drupal\neg_instagram_widget;

use Drupal\Core\Cache\Cache;

/**
 * Class Settings.
 */
class Settings {

  const CONFIGNAME = 'neg_instagram_widget.settings';

  /**
   * Invalidates review cache.
   */
  public static function invalidateCache() {
    Cache::invalidateTags(['instagram_widget']);
  }

  /**
   * Logs a message.
   */
  public static function log($message, $params = [], $log_level = 'notice') {
    \Drupal::logger('neg_instagram_widget')->$log_level($message, $params);
  }

  /**
   * Gets a config object.
   */
  public static function config() {
    return \Drupal::config(self::CONFIGNAME);
  }

  /**
   * Gets an editable config object.
   */
  public static function editableConfig() {
    return \Drupal::service('config.factory')->getEditable(self::CONFIGNAME);
  }

}
