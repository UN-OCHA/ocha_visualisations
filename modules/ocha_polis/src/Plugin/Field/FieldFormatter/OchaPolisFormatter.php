<?php

namespace Drupal\ocha_polis\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Template\Attribute;

/**
 * Plugin implementations for 'ocha_polis' formatter.
 *
 * @FieldFormatter(
 *   id = "ocha_polis",
 *   label = @Translation("OCHA Pol.is formatter"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class OchaPolisFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $id = static::extractPolisId($item->value);
      if ($id !== NULL) {
        $attributes = [
          'src' => "https://pol.is/$id",
          'id' => "polis_$id",
          'data-conversation_id' => $id,
          'data-testid' => 'polis-iframe',
          'width' => '100%',
          'height' => '500px',
        ];
        $element[$delta] = [
          '#attributes' => new Attribute($attributes),
          '#theme' => 'ocha_polis_formatter',
        ];
      }
    }

    return $element;
  }

  /**
   * Parse the pol.is embed code and extract the id.
   *
   * @param string $code
   *   Embed code that should be some HTML string with an iframe.
   *
   * @return string|null
   *   The extracted id or NULL.
   */
  public static function extractPolisId($code) {
    if (empty($code)) {
      return NULL;
    }

    $dom = Html::load($code);

    $div = $dom->getElementsByTagName('div')->item(0);
    if (!isset($div)) {
      return NULL;
    }

    // Extract id.
    if ($div->hasAttribute('data-conversation_id')) {
      return $div->getAttribute('data-conversation_id');
    }

    return NULL;
  }

}
