<?php
namespace SAMSPlugin\Base\SAMSHostConfig;

class CPT {
	public function __construct() {
		add_action('init', [$this, 'register_cpt']);
	}

	public function register_cpt() {
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
}
