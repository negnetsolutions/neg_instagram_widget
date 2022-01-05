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
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = $this->get('summary');
    $bl = str_replace(' ', '', $summary);

    if (strlen($bl) === 0) {
      return 'Untitled Post';
    }

    return $summary;
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
