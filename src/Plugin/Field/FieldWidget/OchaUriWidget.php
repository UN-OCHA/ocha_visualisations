<?php

namespace Drupal\ocha_visualisations\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\UriWidget;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'uri' widget.
 */
#[FieldWidget(
  id: 'ocha_uri',
  label: new TranslatableMarkup('URI field'),
  field_types: ['uri', 'ocha_uri'],
)]
class OchaUriWidget extends UriWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => '',
      'allowed_hosts' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size of URI field'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $element['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => $this->t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];
    $element['allowed_hosts'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed hosts'),
      '#default_value' => $this->getSetting('allowed_hosts'),
      '#description' => $this->t('Specify hosts e.g. <em>drupal.org</em>, <em>www.drupal.org</em> or <em>*.drupal.org</em>. Enter one host per line.'),
      '#element_validate' => [[$this, 'validateAllowedHosts']],
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('URI field size: @size', ['@size' => $this->getSetting('size')]);

    $placeholder = $this->getSetting('placeholder');
    if (!empty($placeholder)) {
      $summary[] = $this->t('Placeholder: @placeholder', ['@placeholder' => $placeholder]);
    }

    $allowed_hosts = $this->getSetting('allowed_hosts');
    if (!empty($allowed_hosts)) {
      $allowed_hosts = implode(', ', explode("\n", $allowed_hosts));
      $summary[] = $this->t('Allowed_hosts: @allowed_hosts', ['@allowed_hosts' => $allowed_hosts]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function validateAllowedHosts($element, FormStateInterface $form_state) {
    $allowed_hosts = $form_state->getValue($element['#parents']);
    $hosts = explode("\n", $allowed_hosts);

    foreach ($hosts as $host) {
      $host_trimmed = trim($host);

      if (!$host_trimmed) {
        continue;
      }

      $host_regex = '/^(\*)$|^(\*\.)?(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/';
      if (!preg_match($host_regex, $host_trimmed)) {
        $form_state->setError($element, t('The host %host is invalid.', [
          '%host' => $host_trimmed,
        ]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = $element + [
      '#type' => 'url',
      '#default_value' => $items[$delta]->value ?? NULL,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#element_validate' => [[$this, 'validateHost']],
    ];

    return $element;
  }

  /**
   * Form element validation handler to validate the embed code.
   *
   * Display an error if the embed code doesn't have the required attributes.
   *
   * @param array $element
   *   Form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   * @param array $form
   *   Form.
   */
  public function validateHost(array &$element, FormStateInterface $form_state, array $form) {
    if ($element['#value'] == '') {
      return;
    }

    $uri = $element['#value'];
    $host = parse_url($uri, PHP_URL_HOST);
    if (empty($host)) {
      $error = t("Invalid host in the @field field. It must be start with https://.", [
        '@field' => $element['#title'],
      ]);
      $form_state->setError($element, $error);
    }

    $allowed_hosts = array_filter(array_map('trim', explode("\n", $this->getSetting('allowed_hosts'))));
    foreach ($allowed_hosts as $allowed_host) {
      if ($host == $allowed_host) {
        return;
      }
    }

    $error = t("The host @host in the @field field is forbidden. It must be one of the following: @allowed_hosts.", [
      '@field' => $element['#title'],
      '@host' => $host,
      '@allowed_hosts' => implode(', ', $allowed_hosts),
    ]);
    $form_state->setError($element, $error);
  }

}