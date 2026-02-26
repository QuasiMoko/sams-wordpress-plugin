<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

use SAMSPlugin\RankingFetcher;
?>

<div <?php echo get_block_wrapper_attributes(); ?>>

<?php

if (isset($attributes)
	&& isset($attributes['samsConfigId'])
	&& isset($attributes['matchSeriesId'])) {
	// Hole die SAMSHostConfig anhand der ID
	$config_post = get_post($attributes['samsConfigId']);

	if ($config_post) {
		$associationUrl = get_post_meta($config_post->ID, '_sams_host_config_url', true);
		$apiKey = get_post_meta($config_post->ID, '_sams_host_config_api_key', true);
		$matchSeriesId = $attributes['matchSeriesId'];

		// Fetch the ranking data using the fetched configuration
		$fetcher = new RankingFetcher();
		$ranking = $fetcher->fetch($associationUrl, $apiKey, $matchSeriesId, noCache: true);

		$template_path = sams_integration_get_template( 'ranking-template.php' );

		if (file_exists($template_path)) {
			$sams_integration_ranking = $ranking;
			include $template_path;
		}
	} else {
		esc_html_e('Error in SAMS Ranking: Configuration not found', 'sams-integration');
	}

} else {
	// Display error message on invalid configuration
	esc_html_e('Error in SAMS Ranking: Configuration missing or incomplete', 'sams-integration');
}
?>

</div>
