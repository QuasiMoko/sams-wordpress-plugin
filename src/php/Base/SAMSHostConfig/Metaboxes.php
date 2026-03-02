<?php
namespace SAMSPlugin\Base\SAMSHostConfig;

class Metaboxes
{
	public function __construct()
	{
		add_action('add_meta_boxes', [$this, 'add_metaboxes']);
		add_action('save_post', [$this, 'save_sams_host_config_metabox']);
		// Spalten hinzufügen
		add_filter('manage_sams_host_config_posts_columns', [$this, 'add_sams_host_config_columns']);
		add_action('manage_sams_host_config_posts_custom_column', [$this, 'fill_sams_host_config_columns'], 10, 2);
	}

	public function add_metaboxes()
	{
		add_meta_box(
			'sams_host_config_details',
			'Host-Details',
			[$this, 'render_sams_host_config_metabox'],
			'sams_host_config',
			'normal',
			'default'
		);
	}


	public function render_sams_host_config_metabox($post)
	{
		// Aktuelle Werte laden
		$url = get_post_meta($post->ID, '_sams_host_config_url', true);
		$api_key = get_post_meta($post->ID, '_sams_host_config_api_key', true);

		// Nonce-Feld für Sicherheit
		wp_nonce_field('sams_integration_save_sams_host_config_metabox', 'sams_host_config_nonce');

		// HTML für die Metabox
		echo '<div style="display: flex; flex-direction: column; gap: 10px;">';
		echo '<label for="sams_host_config_url">URL:</label>';
		echo '<input type="url" name="sams_host_config_url" id="sams_host_config_url" value="' . esc_attr($url) . '" style="width: 100%;" />';

		echo '<label for="sams_host_config_api_key">API-Key:</label>';
		echo '<input type="text" name="sams_host_config_api_key" id="sams_host_config_api_key" value="' . esc_attr($api_key) . '" style="width: 100%;" />';
		echo '</div>';
	}

	public function save_sams_host_config_metabox($post_id)
	{
		// Überprüfe Nonce und Berechtigungen
		if (!isset($_POST['sams_host_config_nonce']) || !wp_verify_nonce($_POST['sams_host_config_nonce'], 'sams_integration_save_sams_host_config_metabox')) {
			return;
		}
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;
		if (!current_user_can('edit_post', $post_id))
			return;

		// Speichere URL
		if (isset($_POST['sams_host_config_url'])) {
			update_post_meta($post_id, '_sams_host_config_url', sanitize_text_field($_POST['sams_host_config_url']));
		}

		// Speichere API-Key
		if (isset($_POST['sams_host_config_api_key'])) {
			update_post_meta($post_id, '_sams_host_config_api_key', sanitize_text_field($_POST['sams_host_config_api_key']));
		}
	}

	public function add_sams_host_config_columns($columns)
	{
		$columns['sams_host_config_url'] = 'URL';
		$columns['sams_host_config_api_key'] = 'API-Key';
		return $columns;
	}

	// Spalteninhalt füllen
	public function fill_sams_host_config_columns($column, $post_id)
	{
		if ($column === 'sams_host_config_url') {
			echo esc_html(get_post_meta($post_id, '_sams_host_config_url', true));
		}
		if ($column === 'sams_host_config_api_key') {
			echo esc_html(substr(get_post_meta($post_id, '_sams_host_config_api_key', true), 0, 10) . '...'); // Nur ein Ausschnitt des API-Keys
		}
	}
}

