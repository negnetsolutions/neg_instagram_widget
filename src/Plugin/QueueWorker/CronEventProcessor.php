<?php

namespace Drupal\neg_instagram_widget\Plugin\QueueWorker;

/**
 *
 * @QueueWorker(
 * id = "instagram_widget_sync",
 * title = "Syncs Instagram Images with Drupal",
 * cron = {"time" = 60}
 * )
 */
class CronEventProcessor extends SyncInstagram {
}
