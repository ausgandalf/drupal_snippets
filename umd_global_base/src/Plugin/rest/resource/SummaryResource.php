<?php

namespace Drupal\umd_global_base\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a Summary Resource
 *
 * @RestResource(
 *   id = "summary_resource",
 *   label = @Translation("Summary Resource"),
 *   uri_paths = {
 *     "canonical" = "/umd_global_base/summary_resource",
 *     "create" = "/umd_global_base/summary_resource"
 *   }
 * )
 */
class SummaryResource extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get(Request $request) {
    // TODO
    $response = ['message' => 0];
    return new ResourceResponse($response);
  }

  /**
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response objects
   */
  public function post(Request $request) {
    // TODO
    $response = ['message' => '1'];
    return  new ResourceResponse($response);
  }

}