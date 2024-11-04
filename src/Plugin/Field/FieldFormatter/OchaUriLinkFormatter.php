<?php

namespace Drupal\ocha_visualisations\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\UriLinkFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'uri_link' formatter.
 */
#[FieldFormatter(
  id: 'ocha_uri_link',
  label: new TranslatableMarkup('Link to URI (OCHA)'),
  field_types: [
    'ocha_uri',
  ],
)]
class OchaUriLinkFormatter extends UriLinkFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      if (!$item->isEmpty()) {
        $elements[$delta] = [
          '#type' => 'inline_template',
          '#template' => '{{ value|nl2br }}',
          '#context' => ['value' => $item->value],
        ];
      }
    }

    return $elements;
  }

}
