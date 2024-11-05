<?php

namespace Drupal\ocha_powerbi\EventSubscriber;

use Drupal\ocha_visualisations\BaseRequestEventSubscriber;

/**
 * Subscribing an event.
 */
class OchaPowerbiEventSubscriber extends BaseRequestEventSubscriber {

  /**
   * {@inheritdoc}
   */
  protected $frame_urls = [
    'https://app.powerbi.com',
  ];

}
