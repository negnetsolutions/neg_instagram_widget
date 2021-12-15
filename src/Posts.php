<?php

namespace Drupal\neg_instagram_widget;

/**
 * Class Posts.
 */
class Posts {

  /**
   * Fetches synced post count.
   */
  public static function postCount() {
    $posts = self::getPosts();
    return count($posts);
  }

  /**
   * Gets IG Posts.
   */
  public static function getPosts(int $maxPosts = 0) {
    $data = \Drupal::state()->get('neg_instagram.posts', NULL);

    $posts = [];

    if (!is_array($data)) {
      return $posts;
    }

    foreach ($data as $i => $r) {

      if (isset($r['thumbnail_url'])) {
        $r['media_url'] = $r['thumbnail_url'];
        unset($r['thumbnail_url']);
      }

      if (!isset($r['caption'])) {
        $r['caption'] = '';
      }

      $r['summary'] = self::summarize($r['caption'], 15);

      $posts[] = new Post($r);

      if ($maxPosts > 0 && $i >= $maxPosts) {
        break;
      }
    }

    return $posts;
  }

  /**
   * Summarizes string.
   */
  public static function summarize($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
      $words = str_word_count($text, 2);
      $pos = array_keys($words);
      $text = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
  }

}
