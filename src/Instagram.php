<?php

namespace Drupal\neg_instagram_widget;

use EspressoDev\InstagramBasicDisplay\InstagramBasicDisplay;
use Drupal\Core\Url;

/**
 * Instagram Service Class.
 */
class Instagram {

  protected static $client = FALSE;
  const APP_ID = '550544569217300';
  const APP_SECRET = 'cd390208ab3bebb28a5d318bd1d56c88';
  const MEDIA_FIELDS = 'caption, id, media_url, permalink, thumbnail_url, timestamp';

  /**
   * Gets instagram service.
   */
  public static function client() {
    if (self::$client === FALSE) {

      $redirectUri = Url::fromRoute('neg_instagram_widget.auth')->setAbsolute()->toString();

      self::$client = new InstagramBasicDisplay([
        'appId' => self::APP_ID,
        'appSecret' => self::APP_SECRET,
        'redirectUri' => $redirectUri,
      ]);

      if (self::accessTokenIsValid()) {

        if (self::accessTokenNeedRefresh()) {
          self::refreshAccessToken();
        }

        self::$client->setAccessToken(self::getAccessToken());

        self::$client->setMediaFields(self::MEDIA_FIELDS);
      }

    }

    return self::$client;
  }

  /**
   * Call's instagram with error tracking.
   */
  protected static function callInstagram(string $method, array $arguments) {
    $client = self::client();
    $response = call_user_func_array([$client, $method], $arguments);

    if (isset($response->error)) {
      throw new \Exception($response->error->message);
    }

    return $response;
  }

  /**
   * Gets user profile information.
   */
  public static function getUserProfile($id = 'me') {
    return self::callInstagram('getUserProfile', [$id]);
  }

  /**
   * Gets user media from IS.
   */
  public static function getMedia($limit = 0) {
    $config = Settings::config();

    try {
      $data = self::callInstagram('getUserMedia', ['me', $limit]);
    }
    catch (\Exception $e) {
      Settings::log('Could not fetch user media %m', [
        '%m' => $e->getMessage(),
      ], 'error');
      return FALSE;
    }

    return self::objectToArray($data->data);
  }

  /**
   * Converts object to array.
   */
  protected static function objectToArray($obj) {
    if (is_object($obj)) {
      $obj = (array) $obj;
    }
    if (is_array($obj)) {
      $new = [];
      foreach ($obj as $key => $val) {
        $new[$key] = self::objectToArray($val);
      }
    }
    else {
      $new = $obj;
    }
    return $new;
  }

  /**
   * Calculates ttl.
   */
  protected static function calculateTtl($longLived = FALSE) {
    // Default token timeout = 1 hour.
    $hours = 1;

    if ($longLived === TRUE) {
      // 60 Days.
      $hours = 1440;
    }

    return time() + (60 * 60 * $hours);
  }

  /**
   * Sets instagram access token.
   */
  public static function setAccessToken($token, $longLived = FALSE) {
    $config = Settings::editableConfig();
    $config->set('access_token', $token);

    if (is_bool($longLived)) {
      $ttl = self::calculateTtl($longLived);
    }
    else {
      $ttl = $longLived + time();
      $longLived = ($longLived > (60 * 60)) ? TRUE : FALSE;
    }

    // Token valid for 60 days.
    $config->set('token_valid_ttl', $ttl);
    $config->set('token_long_lived', $longLived);

    $config->save();
  }

  /**
   * Refreshes Access Token.
   */
  protected static function refreshAccessToken() {
    $token = self::getAccessToken();

    try {
      $data = self::$client->refreshToken(self::getAccessToken(), FALSE);
    }
    catch (\Exception $e) {
      Settings::log('Could not Refresh Instagram Access Token! MSG: %m', [
        '%m' => $e->getMessage(),
      ], 'error');

      return FALSE;
    }

    self::setAccessToken($data->access_token, $data->expires_in);
    Settings::log('Refreshed Instagram Access Token');
  }

  /**
   * Checks to see if token needs to be refreshed.
   */
  public static function accessTokenNeedRefresh() {
    $config = Settings::config();
    $time = time();
    $longLived = $config->get('token_long_lived');
    $ttl = self::calculateTtl($longLived);

    if ($longLived === TRUE) {
      // Refresh at 2 days out.
      if (($ttl - $time) < (60 * 60 * 48)) {
        return TRUE;
      }
    }
    else {
      // Refresh at 15 minutes.
      if (($ttl - $time) < (60 * 15)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Checks to see if access token is valid.
   */
  public static function accessTokenIsValid() {
    $token = self::getAccessToken();

    if ($token === NULL) {
      return FALSE;
    }

    $validTill = self::getAccessTokenValidTill();

    if ($validTill === NULL || time() >= $validTill) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Gets instagram access token.
   */
  public static function getAccessToken() {
    $config = Settings::config();
    return $config->get('access_token');
  }

  /**
   * Gets instagram access token ttl.
   */
  public static function getAccessTokenValidTill() {
    $config = Settings::config();
    return $config->get('token_valid_ttl');
  }

}
