<?php

namespace Drupal\neg_instagram_widget;

/**
 * Class Posts.
 */
class Posts {

  /**
   * Gets IG Posts.
   */
  public static function getPosts(int $maxPosts = 0) {
    $config = Settings::config();
    $data = $config->get('posts');

    $posts = [];
    foreach ($data as $i => $r) {
      $posts[] = new Post($r);

      if ($maxPosts > 0 && $i >= $maxPosts) {
        break;
      }
    }

    return $posts;
  }

}
