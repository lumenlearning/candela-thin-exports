<?php

namespace CC\Api\Export;

/**
 * Allows an admin to export Common Cartridges
 * https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
 *
 * @request array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 */
function export_flat_cc($request) {
  $params = $request->get_params();

  $cc_options = [];
  $cc_options['inline'] = true;
  $cc_options['version'] = "1.3";
  $cc_options['include_topics'] = true;
  $cc_options['include_assignments'] = true;
  $cc_options['include_guids'] = true;
  $cc_options['export_flagged_only'] = true;

  $res = [];
  $manifest = new \CC\Manifest(\PressBooks\Book::getBookStructure('', true), $cc_options);
  $manifest->build_manifest();
  $res['imsmanifest'] = $manifest->get_manifest();

  return $res;
}

add_action('rest_api_init', function () {
  register_rest_route('lumen/cc_export/v1', '/export', array(
      'methods' => 'POST',
      'callback' => 'CC\Api\Export\export_flat_cc',
      'permission_callback' => function () {
        return current_user_can( 'cu_export_cc' );
      }
  ));
});
