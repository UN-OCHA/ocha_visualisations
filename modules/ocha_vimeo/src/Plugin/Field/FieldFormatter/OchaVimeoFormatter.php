<?php

namespace Drupal\ocha_vimeo\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

/**
 * Plugin implementations for 'ocha_vimeo' formatter.
 *
 * @FieldFormatter(
 *   id = "ocha_vimeo",
 *   label = @Translation("OCHA vimeo formatter"),
 *   field_types = {
 *     "link",
 *     "ocha_uri",
 *     "uri"
 *   }
 * )
 */
class OchaVimeoFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $item */
    foreach ($items as $delta => $item) {
      if ($item instanceof LinkItem) {
        $element[$delta] = [
          '#theme' => 'ocha_vimeo_formatter',
          '#embed_url' => $item->getUrl(),
          '#width' => 1140,
          '#height' => 540,
        ];
      }
      else {
        $element[$delta] = [
          '#theme' => 'ocha_vimeo_formatter',
          '#embed_url' => $item->value,
          '#width' => 1140,
          '#height' => 540,
        ];

      }
    }

    return $element;
  }

}
