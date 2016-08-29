<?php
/*
Plugin Name: Poll
 */
include_once plugin_dir_path( __FILE__ ).'/pollwidget.php';

/**
 * Classe Poll_Plugin
 * Déclare le plugin
 */
class Poll_Plugin
{
    /**
     * Constructeur
     */
    public function __construct()
	{
		add_action('widgets_init', function(){register_widget('Poll_Widget');});
		register_activation_hook(__FILE__, array('Poll_Plugin','install'));
		register_uninstall_hook(__FILE__, array('Poll_Plugin','uninstall'));
		add_action('admin_menu', array($this,'add_admin_menu'));
	}

	/** 
     * Fonction de création du menu dans l'interface d'administration
	*/
	public function add_admin_menu()
	{
		add_menu_page('Créer un sondage : poll', 'poll plugin', 'manage_options', 'poll', array($this, 'menu_html'));
		add_submenu_page('poll', 'Créer un sondage avec poll_plugin', 'Réglages', 'manage_options', 'poll', array($this, 'menu_html'));
	}

	
	 
	/**
	 * Fonction affichage du menu dans l'interface d'administration
	 */
	public function menu_html()
	{
		echo '<h1>'.get_admin_page_title().'</h1>';
		echo '<p>Bienvenue sur la page d\'accueil de poll_plugin</p>';
	}	
	 
    /**
     * Fonction d'installation
     */
    public static function install()
	{	
		global $wpbd;	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql_1 ="CREATE TABLE IF NOT EXISTS wp_poll_results(option_id INT NOT NULL, total INT NOT NULL);";
		dbDelta($sql_1);

		$sql_2 ="CREATE TABLE IF NOT EXISTS wp_poll_options (id INT AUTO_INCREMENT PRIMARY KEY, label VARCHAR(255) NOT NULL);";
		dbDelta($sql_2);
		
	/*	$wpdb->query("CREATE TABLE IF NOT EXISTS wp_poll_results(option_id INT NOT NULL, total INT NOT NULL);");
		$wpdb->query("CREATE TABLE IF NOT EXISTS wp_poll_options (id INT AUTO_INCREMENT  PRIMARY KEY, label VARCHAR(255) NOT NULL;"); */

    }

    /**
     * Fonction de désinstallation
     * Suppression des tables du sondage
     */
    public static function uninstall()
	{
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS wp_poll_options;");
		$wpdb->query("DROP TABLE IF EXISTS wp_poll_results;");
    }
}

new Poll_Plugin();
