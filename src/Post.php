<?php

namespace Drupal\neg_instagram_widget;

/**
 * Class Post.
 */
class Post {

  protected $data;

  /**
   * Implements construct.
   */
  public function __construct($data) {
    $this->data = $data;
  }

  /**
   * Getter.
   */
  public function get($property) {
    if (isset($this->data[$property])) {
      return $this->data[$property];
    }
    return FALSE;
  }

  /**
   * Getter.
   */
  public function __get($property) {
    return $this->get($property);
  }

}
