<?php

namespace Drupal\ocha_vimeo\EventSubscriber;

use Drupal\ocha_visualisations\BaseRequestEventSubscriber;

/**
 * Subscribing an event.
 */
class OchavimeoEventSubscriber extends BaseRequestEventSubscriber {

  /**
   * {@inheritdoc}
   */
  protected $frame_urls = [
    'https://vimeo.com',
    'https://player.vimeo.com',
  ];

}
