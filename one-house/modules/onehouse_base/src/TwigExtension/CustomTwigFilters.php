<?php

namespace Drupal\onehouse_base\TwigExtension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;

/**
 * Extra Utility Class to provide custom Twig Filters.
 */
class CustomTwigFilters extends AbstractExtension implements ExtensionInterface {

  /**
   * Environment variable used in twig.
   *
   * @var Twig\Environment
   */
  private $environment = NULL;

  /**
   * Init runtime process.
   */
  public function initRuntime(Environment $environment) {
    $this->environment = $environment;
  }

  /**
   * Generates a list of all Twig filters that this extension defines.
   */
  public function getFilters() {
    return [
      new TwigFilter('image_url', [$this, 'imageUrl']),
      new TwigFilter('image_alt', [$this, 'imageAlt']),
      new TwigFilter('attr', [$this, 'getAttr']),
    ];
  }

  /**
   * Gets a unique identifier for this Twig extension.
   */
  public function getName() {
    return 'onehouse_base.customtwigfilters_extension';
  }

  /**
   * Get image url from twig variable.
   */
  public static function imageUrl($element) {
    if (is_null($element) || !is_array($element)) {
      return '';
    }

    $formatter = $element['#formatter'] ?? '';
    if ($formatter == 'media_thumbnail') {
      // In case of [Thumbnail] display format.
      if (isset($element[0]['#item'])) {
        $item = $element[0]['#item'];

        $file_url_generator = \Drupal::service('file_url_generator');
        return $file_url_generator->generateString($item->entity->uri->value);
      }
    }
    elseif ($formatter == 'entity_reference_entity_view') {
      // In case of [Rendered Entity] display format.
      if (isset($element[0]['#media'])) {
        $item = $element[0]['#media']->field_media_image;

        $file_url_generator = \Drupal::service('file_url_generator');
        return $file_url_generator->generateString($item->entity->uri->value);
      }
    }

    return '';
  }

  /**
   * Get image alt value.
   */
  public static function imageAlt($element) {
    if (is_null($element) || !is_array($element)) {
      return '';
    }

    $formatter = $element['#formatter'] ?? '';
    if ($formatter == 'media_thumbnail') {
      // In case of [Thumbnail] display format.
      if (isset($element[0]['#item'])) {
        $item = $element[0]['#item'];
        return $item->alt;
      }
    }
    elseif ($formatter == 'entity_reference_entity_view') {
      // In case of [Rendered Entity] display format.
      if (isset($element[0]['#media'])) {
        $item = $element[0]['#media']->field_media_image;
        return $item->alt;
      }
    }

    return '';
  }

  /**
   * Get html attribute.
   */
  public static function getAttr($src, $attr) {
    if (is_null($src)) {
      return '';
    }

    $attrv = '';

    try {
      $use_errors = libxml_use_internal_errors(TRUE);

      $xmlEl = simplexml_load_string($src);
      if ($xmlEl) {
        $attrv = $xmlEl->attributes()->{$attr};
      }

      libxml_clear_errors();
      libxml_use_internal_errors($use_errors);
    }
    catch (\Exception $e) {
    }

    return is_string($attrv) ? $attrv : (is_null($attrv) ? '' : $attrv->__toString());
  }

}
