<?php

namespace Drupal\ocha_datawrapper\EventSubscriber;

use Drupal\ocha_visualisations\BaseRequestEventSubscriber;

/**
 * Subscribing an event.
 */
class OchaDatawrapperEventSubscriber extends BaseRequestEventSubscriber {

  /**
   * {@inheritdoc}
   */
  protected $frame_url = 'https://datawrapper.dwcdn.net';

}
