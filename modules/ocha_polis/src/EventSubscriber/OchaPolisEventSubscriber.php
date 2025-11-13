<?php

namespace Drupal\ocha_polis\EventSubscriber;

use Drupal\ocha_visualisations\BaseRequestEventSubscriber;

/**
 * Subscribing an event.
 */
class OchaPolisEventSubscriber extends BaseRequestEventSubscriber {

  /**
   * {@inheritdoc}
   */
  protected $frame_urls = [
    'https://pol.is',
  ];

  /**
   * {@inheritdoc}
   */
  protected $script_urls = [
    'https://pol.is/embed.js',
  ];

}
