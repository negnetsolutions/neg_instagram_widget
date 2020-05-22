<?php

namespace Drupal\neg_instagram_widget\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

use Drupal\neg_instagram_widget\Instagram;
use Drupal\neg_instagram_widget\Settings;

/**
 * Instagram Controller.
 */
class InstagramController extends ControllerBase {

  /**
   * Handle oauth queries.
   */
  public function auth() {
    $client = Instagram::client();
    $code = \Drupal::request()->query->get('code');

    if ($code === NULL) {
      throw new NotFoundHttpException();
    }

    // Get the short lived access token (valid for 1 hour)
    try {
      $token = $client->getOAuthToken($code, TRUE);
    }
    catch (\Exception $e) {
      Settings::log('Could not obtain access token from Instagram! MSG: %m', [
        '%m' => $e->getMessage(),
      ], 'error');
      throw new NotFoundHttpException();
    }

    // Save first access token.
    Instagram::setAccessToken($token, FALSE);

    // Exchange this token for a long lived token (valid for 60 days)
    try {
      $token = $client->getLongLivedToken($token, TRUE);
    }
    catch (\Exception $e) {
      Settings::log('Could not obtain long access token from Instagram! MSG: %m', [
        '%m' => $e->getMessage(),
      ]);
    }

    // Save first access token.
    Instagram::setAccessToken($token, TRUE);

    $path = Url::fromRoute('neg_instagram_widget.settings')->setAbsolute()->toString();
    $response = new RedirectResponse($path);
    return $response;
  }

}
