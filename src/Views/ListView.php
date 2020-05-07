<?php

namespace Drupal\neg_instagram_widget\Views;

use Drupal\neg_instagram_widget\Posts;

/**
 * Class ListView.
 */
class ListView {

  protected $variables;

  /**
   * Implements constructor.
   */
  public function __construct(array &$variables) {
    $this->variables = &$variables;
  }

  /**
   * Fetches Posts.
   */
  protected function fetchPosts() {
    return Posts::getPosts();
  }

  /**
   * Renders the view.
   */
  public function render() {
    $posts = $this->fetchPosts();

    $this->variables['view'] = [
      '#theme' => 'neg_instagram_widget_list_view',
      '#posts' => $posts,
      '#cache' => [
        'tags' => ['instagram_widget'],
      ],
    ];
  }

}
