<?php

namespace Drupal\ocha_visualisations;

use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Base event for response.
 */
class BaseRequestEventSubscriber implements EventSubscriberInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Request object.
   *
   * @var Request
   */
  protected $request;

  /**
   * Response object.
   *
   * @var \Symfony\Component\HttpFoundation\Response
   */
  protected $response;

  /**
   * Logger instance.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * URL for iframe.
   *
   * @var array
   */
  protected $frame_urls = [];

  /**
   * Constructs an SecKitEventSubscriber object.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The Seckit logger channel.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(LoggerInterface $logger, ConfigFactoryInterface $config_factory) {
    $this->logger = $logger;
    $this->config = $config_factory->get('seckit.settings');
  }

  /**
   * Executes actions on the response event.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   Filter Response Event object.
   */
  public function onKernelResponse(ResponseEvent $event) {
    if (empty($this->frame_urls)) {
      return;
    }

    $this->response = $event->getResponse();

    // Get default/set options.
    $csp_vendor_prefix_x = $this->config->get('seckit_xss.csp.vendor-prefix.x');
    $csp_vendor_prefix_webkit = $this->config->get('seckit_xss.csp.vendor-prefix.webkit');
    $csp_report_only = $this->config->get('seckit_xss.csp.report-only');

    // Prepare directives.
    $directives = '';

    if ($csp_report_only) {
      $directives = $this->response->headers->get('Content-Security-Policy-Report-Only', '');
    }
    else {
      $directives = $this->response->headers->get('Content-Security-Policy', '');
    }

    if (empty($directives)) {
      return;
    }

    $directives = explode('; ', $directives);

    foreach ($this->frame_urls as $frame_url) {
      $found = FALSE;
      foreach ($directives as &$directive) {
        if (strpos($directive, 'frame-src') === 0) {
          $found = TRUE;
          if (strpos($directive, $frame_url) === FALSE) {
            $directive .= ' ' . $frame_url;
          }
        }
      }

      if (!$found) {
        $directives[] = 'frame-src ' . $frame_url;
      }
    }

    // Merge directives.
    $directives = implode('; ', $directives);

    if ($csp_report_only) {
      // Use report-only mode.
      $this->response->headers->set('Content-Security-Policy-Report-Only', $directives);
      if ($csp_vendor_prefix_x) {
        $this->response->headers->set('X-Content-Security-Policy-Report-Only', $directives);
      }
      if ($csp_vendor_prefix_webkit) {
        $this->response->headers->set('X-WebKit-CSP-Report-Only', $directives);
      }
    }
    else {
      $this->response->headers->set('Content-Security-Policy', $directives);
      if ($csp_vendor_prefix_x) {
        $this->response->headers->set('X-Content-Security-Policy', $directives);
      }
      if ($csp_vendor_prefix_webkit) {
        $this->response->headers->set('X-WebKit-CSP', $directives);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events[KernelEvents::RESPONSE][] = ['onKernelResponse', -500];

    return $events;
  }

}
