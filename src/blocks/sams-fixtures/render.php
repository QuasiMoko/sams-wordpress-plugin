<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

use SAMSPlugin\FixturesFetcher;
?>

<div <?php echo get_block_wrapper_attributes(); ?>>

<?php

if (isset($attributes)
	&& isset($attributes['samsConfigId'])
	&& isset($attributes['matchSeriesId'])
	&& isset($attributes['teamId'])) {

	$config_post = get_post($attributes['samsConfigId']);

	if ($config_post) {
		$associationUrl = get_post_meta($config_post->ID, '_sams_host_config_url', true);
		$apiKey = get_post_meta($config_post->ID, '_sams_host_config_api_key', true);	

		$fetcher = new FixturesFetcher();
		$fixtures = $fetcher->fetch(
			$associationUrl,
			$apiKey,
			$attributes['matchSeriesId'],
			$attributes['teamId']);
		
		$template_path = sams_integration_get_template( 'fixtures-template.php' );

		if (file_exists($template_path)) {
			$sams_integration_fixtures = $fixtures;
			include $template_path;
		}
	}

} else {
	// Display error message on invalid configuration
	esc_html_e('Error in SAMS Fixtures: Configuration missing or incomplete', 'sams-integration');
}

?>

</div>

