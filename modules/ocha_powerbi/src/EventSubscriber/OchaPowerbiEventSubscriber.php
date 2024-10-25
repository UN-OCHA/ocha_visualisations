<?php

namespace Drupal\ocha_powerbi\EventSubscriber;

use Drupal\ocha_visualizations\BaseRequestEventSubscriber;

/**
 * Subscribing an event.
 */
class OchaPowerbiEventSubscriber extends BaseRequestEventSubscriber {

  /**
   * {@inheritdoc}
   */
  protected $frame_url = 'https://app.powerbi.com';

}
