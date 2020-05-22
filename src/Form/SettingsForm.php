<?php

namespace Drupal\neg_instagram_widget\Form;

use Drupal\neg_instagram_widget\Settings;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\neg_instagram_widget\Instagram;
use Drupal\neg_instagram_widget\Plugin\Sync;

/**
 * Settings for Instagram.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'instagram_widget_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      Settings::CONFIGNAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(Settings::CONFIGNAME);

    if (Instagram::accessTokenIsValid() === FALSE) {
      $form['connect'] = [
        '#markup' => '<p>Authorization required to access Instagram. Click <a href="' . Instagram::client()->getLoginUrl() . '">here</a> to authorize the application.</p>',
      ];
    }
    else {

      try {
        $profile_details = Instagram::getUserProfile();
        $postsCount = count($config->get('posts'));

        $form['details'] = [
          '#markup' => '<p>Currently Syncing with Instagram Username: <strong>' . $profile_details->username . '</strong><br />' . ucwords($profile_details->username) . ' has ' . $profile_details->media_count . ' IG posts. <strong>' . $postsCount . '</strong> posts have been synced.</p>',
        ];
      }
      catch (\Exception $e) {
        \Drupal::messenger()->addError('Instagram Error: ' . $e->getMessage(), TRUE);
      }

      $form['frequency'] = [
        '#type' => 'select',
        '#title' => t('Sync Frequency'),
        '#default_value' => $config->get('frequency'),
        '#options' => [
          '0' => 'Every Cron Run',
          '900' => 'Every 15 Minutes',
          '1800' => 'Every 30 Minutes',
          '3600' => 'Every Hour',
          '10800' => 'Every 3 Hours',
          '21600' => 'Every 6 Hours',
          '86400' => 'Every 24 Hours',
        ],
        '#required' => TRUE,
      ];

      $form['last_sync'] = [
        '#markup' => '<p>Last Sync: ' . date('r', $config->get('last_sync')) . '</p>',
      ];

      $form['force_sync'] = [
        '#type' => 'submit',
        '#value' => t('Force Sync Now'),
        '#submit' => ['::forceSync'],
      ];

      $form['disconnect'] = [
        '#type' => 'submit',
        '#value' => t('Disconnect from Instagram'),
        '#submit' => ['::disconnect'],
      ];

    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * Disconnects from IG.
   */
  public function disconnect(array &$form, FormStateInterface $form_state) {
    $config = Settings::editableConfig();
    $config->clear('access_token');
    $config->clear('token_long_lived');
    $config->clear('token_valid_ttl');
    $config->save();

    \Drupal::messenger()->addStatus('Disconnected successfully from Instagram.');
  }

  /**
   * Forces a resync.
   */
  public function forceSync(array &$form, FormStateInterface $form_state) {
    $sync = new Sync();

    try {
      $sync->sync();
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addError($e->getMessage(), TRUE);
      return;
    }

    \Drupal::messenger()->addStatus('Successfully Synced with Instagram!');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $config = Settings::editableConfig();

    $config->set('frequency', $form_state->getValue('frequency'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
