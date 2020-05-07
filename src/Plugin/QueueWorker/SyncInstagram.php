<?php

namespace Drupal\neg_instagram_widget\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\neg_instagram_widget\Plugin\Sync;

/**
 * Class SyncInstagram.
 */
class SyncInstagram extends QueueWorkerBase {

  /**
   * Processes a queue item.
   */
  public function processItem($data) {

    switch ($data['op']) {
      case 'sync':
        $sync = new Sync();
        $sync->sync();
        break;
    }
  }

}
