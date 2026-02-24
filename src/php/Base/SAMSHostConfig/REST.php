<?php
namespace SAMSPlugin\Base\SAMSHostConfig;

class REST
{
	public function __construct()
	{
		add_action('rest_api_init', [$this, 'register_rest_fields']);
	}

	public function register_rest_fields() {
		register_rest_field('sams_host_config', 'url', [
			'get_callback' => function($post) {
				return get_post_meta($post['id'], '_sams_host_config_url', true);
			},
		]);
		register_rest_field('sams_host_config', 'api_key', [
			'get_callback' => function($post) {
				return get_post_meta($post['id'], '_sams_host_config_api_key', true);
			},
		]);
	}
}