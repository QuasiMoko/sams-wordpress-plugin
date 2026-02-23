<?php
/**
 * Plugin Name:       SAMS Integration
 * Description:       Displays fixtures and rankings from a SAMS results system
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Version:           1.2.0
 * Author:            René Siemer
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sams-integration
 *
 * @package SAMSPlugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( file_exists( __DIR__ . '/build/lib/autoload.php' ) ) {

    require_once __DIR__ . '/build/lib/autoload.php';
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function samsintegration_initialize_blocktypes() {
	register_block_type( __DIR__ . '/build/blocks/sams-ranking' );
	register_block_type( __DIR__ . '/build/blocks/sams-fixtures' );
}
add_action( 'init', 'samsintegration_initialize_blocktypes' );

function sams_integration_get_template( $template_name ) {
    $theme_template = locate_template( 'sams-integration/' . $template_name );
    
    if ( ! empty( $theme_template ) ) {
        // if exists: use template from theme
        return $theme_template;
    } else {
        // fallback on default template
        return plugin_dir_path( __FILE__ ) . 'build/php/templates/' . $template_name;
    }
}

function sams_integration_register_sams_host_config_cpt() {
	$labels = [
		'name'               => 'SAMS Server Konfigurationen',
		'singular_name'      => 'SAMS Server Konfiguration',
		'menu_name'          => 'SAMS Server',
		'add_new'            => 'Server Konfiguration hinzufügen ',
		'add_new_item'       => 'Server Konfiguration hinzufügen',
		'edit_item'          => 'Server Konfiguration bearbeiten',
		'new_item'           => 'Server Konfiguration hinzufügen',
		'view_item'          => 'Server Konfiguration anzeigen',
		'search_items'       => 'Server Konfigurationen durchsuchen',
		'not_found'          => 'Keine SAMS Server Konfiguration gefunden',
		'not_found_in_trash' => 'Keine SAMS Server Konfigurationen im Papierkorb gefunden',
	];

	$args = [
		'labels'              => $labels,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-admin-site',
		'supports'            => ['title'], // Nur der Titel wird standardmäßig benötigt
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true, // Optional: Für Gutenberg/Block-Editor
	];

	register_post_type('sams_host_config', $args);
}

add_action('init', 'sams_integration_register_sams_host_config_cpt');

function sams_integration_register_sams_host_config_metaboxes() {
	add_meta_box(
		'sams_host_config_details',
		'Host-Details',
		'sams_integration_render_sams_host_config_metabox',
		'sams_host_config',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'sams_integration_register_sams_host_config_metaboxes');

function sams_integration_render_sams_host_config_metabox($post) {
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

function sams_integration_save_sams_host_config_metabox($post_id) {
	// Überprüfe Nonce und Berechtigungen
	if (!isset($_POST['sams_host_config_nonce']) || !wp_verify_nonce($_POST['sams_host_config_nonce'], 'sams_integration_save_sams_host_config_metabox')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	// Speichere URL
	if (isset($_POST['sams_host_config_url'])) {
		update_post_meta($post_id, '_sams_host_config_url', sanitize_text_field($_POST['sams_host_config_url']));
	}

	// Speichere API-Key
	if (isset($_POST['sams_host_config_api_key'])) {
		update_post_meta($post_id, '_sams_host_config_api_key', sanitize_text_field($_POST['sams_host_config_api_key']));
	}
}
add_action('save_post', 'sams_integration_save_sams_host_config_metabox');

// Spalten hinzufügen
function sams_integration_add_sams_host_config_columns($columns) {
	$columns['sams_host_config_url'] = 'URL';
	$columns['sams_host_config_api_key'] = 'API-Key';
	return $columns;
}
add_filter('manage_sams_host_config_posts_columns', 'sams_integration_add_sams_host_config_columns');

// Spalteninhalt füllen
function sams_integration_fill_sams_host_config_columns($column, $post_id) {
	if ($column === 'sams_host_config_url') {
		echo esc_html(get_post_meta($post_id, '_sams_host_config_url', true));
	}
	if ($column === 'sams_host_config_api_key') {
		echo esc_html(substr(get_post_meta($post_id, '_sams_host_config_api_key', true), 0, 10) . '...'); // Nur ein Ausschnitt des API-Keys
	}
}
add_action('manage_sams_host_config_posts_custom_column', 'sams_integration_fill_sams_host_config_columns', 10, 2);


function sams_integration_register_sams_host_config_rest_fields() {
	register_rest_field('sams_host_config', 'url', [
		'get_callback' => function($post) {
			return get_post_meta($post['id'], '_sams_host_config', true);
		},
	]);
	register_rest_field('sams_host_config', 'api_key', [
		'get_callback' => function($post) {
			return get_post_meta($post['id'], '_sams_host_config_api_key', true);
		},
	]);
}
add_action('rest_api_init', 'sams_integration_register_sams_host_config_rest_fields');




