<?php

namespace Drupal\neg_instagram_widget;

/**
 * Class Posts.
 */
class Posts {

  /**
   * Gets IG Posts.
   */
  public static function getPosts() {
    $config = Settings::config();
    $data = $config->get('posts');

    $posts = [];
    foreach ($data as $r) {
      $posts[] = new Post($r);
    }

    return $posts;
  }

}
