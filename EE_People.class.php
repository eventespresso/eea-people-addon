<?php
/**
 * This file contains the main class for the people addon
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage plugin api
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * Main class setting up addon that hooks into EE_Plugin_API (EE_Addon)
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	plugin api
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
// define the plugin directory path and URL
define( 'EEA_PEOPLE_ADDON_BASENAME', plugin_basename( EEA_PEOPLE_ADDON_PLUGIN_FILE ));
define( 'EEA_PEOPLE_ADDON_PATH', plugin_dir_path( __FILE__ ));
define( 'EEA_PEOPLE_ADDON_URL', plugin_dir_url( __FILE__ ));
define( 'EEA_PEOPLE_ADDON_ADMIN', EEA_PEOPLE_ADDON_PATH . 'admin' . DS . 'people' . DS );
Class  EE_People extends EE_Addon {


	public function __construct() {
		//filter extra paths
		add_filter( 'FHEE__EE_Registry__load_core__core_paths', array( $this, 'add_extra_core_paths' ), 10  );
		add_filter( 'FHEE__EE_Registry__load_helper__helper_paths', array( $this, 'add_extra_helper_paths' ), 10 );

		//include our public "templates" file.
		require_once EEA_PEOPLE_ADDON_PATH . 'public/template_hooks.php';
	}


	public static function register_addon() {
		// register addon via Plugin API
		EE_Register_Addon::register(
			'People',
			array(
				'version' 			=> EEA_PEOPLE_ADDON_VERSION,
				'min_core_version' => '4.5.0.dev.000',
				'main_file_path' 		=> EEA_PEOPLE_ADDON_PLUGIN_FILE,
				'admin_path' 			=> EEA_PEOPLE_ADDON_ADMIN,
				'admin_callback'		=> 'additional_admin_hooks',
				'config_class' 			=> 'EE_People_Config',
				'config_name' 		=> 'EE_People',
				'autoloader_paths' => array(
					'EE_People'  => EEA_PEOPLE_ADDON_PATH . 'EE_People.class.php',
					'EE_People_Config'  => EEA_PEOPLE_ADDON_PATH . 'EE_People_Config.php',
					'People_Admin_Page'  => EEA_PEOPLE_ADDON_ADMIN . 'People_Admin_Page.core.php',
					'People_Admin_Page_Init' => EEA_PEOPLE_ADDON_ADMIN . 'People_Admin_Page_Init.core.php',
				),
				'dms_paths' 			=> array( EEA_PEOPLE_ADDON_PATH . 'core' . DS . 'data_migration_scripts' . DS ),
				'module_paths' 		=> array( EEA_PEOPLE_ADDON_PATH . 'EED_People_Single.module.php' ),
				'shortcode_paths' 	=> array( EEA_PEOPLE_ADDON_PATH . 'EES_Espresso_People.shortcode.php' ),
				//'widget_paths' 		=> array( EEA_PEOPLE_ADDON_PATH . 'EEW_eea-people-addon.widget.php' ),
				// if plugin update engine is being used for auto-updates. not needed if PUE is not being used.
				'pue_options'			=> array(
					'pue_plugin_slug' => 'eea-people-addon',
					'plugin_basename' => EEA_PEOPLE_ADDON_BASENAME,
					'checkPeriod' => '24',
					'use_wp_update' => FALSE,
					),
				'capabilities' => array(
					'administrator' => array(
						'ee_edit_people', 'ee_read_people', 'ee_read_peoples', 'ee_delete_people', 'ee_edit_peoples', 'ee_edit_others_peoples', 'ee_publish_peoples', 'ee_read_private_peoples', 'ee_delete_peoples', 'ee_delete_private_peoples', 'ee_delete_published_peoples', 'ee_delete_others_peoples', 'ee_edit_private_peoples', 'ee_edit_published_peoples', 'ee_manage_people_types', 'ee_edit_people_type', 'ee_delete_people_type', 'ee_assign_people_type', 'ee_manage_people_categories', 'ee_edit_people_category', 'ee_delete_people_category', 'ee_assign_people_category'
						),
					),
				'capability_maps' => array(
					'EE_Meta_Capability_Map_Edit' => array( 'ee_edit_people', array( 'Person', 'ee_edit_published_peoples', 'ee_edit_others_peoples', 'ee_edit_private_peoples' ) ),
					'EE_Meta_Capability_Map_Read' => array( 'ee_read_people', array( 'Person', '', 'ee_read_others_peoples', 'ee_read_private_peoples' ) ),
					'EE_Meta_Capability_Map_Delete' => array( 'ee_delete_people', array( 'Person', 'ee_delete_published_peoples', 'ee_delete_others_peoples', 'ee_delete_private_peoples' ) ),
					),
				'class_paths' => EEA_PEOPLE_ADDON_PATH . 'core' . DS . 'db_classes',
				'model_paths' => EEA_PEOPLE_ADDON_PATH . 'core' . DS . 'db_models',
				'model_extension_paths' => EEA_PEOPLE_ADDON_PATH . 'core' . DS . 'db_model_extensions',
				'custom_post_types' => array(
					'espresso_people' => array(
						'singular_name' => __('Person', 'event_espresso'),
						'plural_name' => __('People', 'event_espresso' ),
						'singular_slug' => __('person', 'event_espresso' ),
						'plural_slug' => __('people', 'event_espresso' ),
						'class_name'  => 'EE_Person',
						'args' => array(
							'public' => true,
							'show_in_nav_menus' => true,
							'capability_type' => 'people',
							'capabilities' => array(
								'edit_post' => 'ee_edit_people',
								'read_post' => 'ee_read_people',
								'delete_post' => 'ee_delete_people',
								'edit_posts' => 'ee_edit_peoples',
								'edit_others_posts' => 'ee_edit_others_peoples',
								'publish_posts' => 'ee_publish_peoples',
								'read_private_posts' => 'ee_read_private_peoples',
								'delete_posts' => 'ee_delete_peoples',
								'delete_private_posts' => 'ee_delete_private_peoples',
								'delete_published_posts' => 'ee_delete_published_peoples',
								'delete_others_posts' => 'ee_delete_others_peoples',
								'edit_private_posts' => 'ee_edit_private_peoples',
								'edit_published_posts' => 'ee_edit_published_peoples'
								),
							'taxonomies' => array(
								'espresso_people_type',
								'espresso_people_categories'
								),
							'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
							),
						)
					),
				'custom_taxonomies' => array(
					'espresso_people_type' => array(
						'singular_name' => __('People Type', 'event_espresso'),
						'plural_name'  => __('People Types', 'event_espresso' ),
						'args' => array(
							'public' => true,
							'show_in_nav_menus' => true,
							'capabilities' => array(
								'manage_terms' => 'ee_manage_people_types',
								'edit_terms' => 'ee_edit_people_type',
								'delete_terms' => 'ee_delete_people_type',
								'assign_terms' => 'ee_assign_people_type'
								),
							'rewrite' => array( 'slug' => __('people-type', 'event_espresso' ) )
							)
						),
					'espresso_people_categories' => array(
						'singular_name' => __('People Category', 'event_espresso'),
						'plural_name' => __('People Categories', 'event_espresso' ),
						'args' => array(
							'public' => true,
							'show_in_nav_menus' => true,
							'capabilities' => array(
								'manage_terms' => 'ee_manage_people_categories',
								'edit_terms' => 'ee_edit_people_category',
								'delete_terms' => 'ee_delete_people_category',
								'assign_terms' => 'ee_assign_people_category'
								),
							'rewrite' => array( 'slug' => __('people-type', 'event_espresso' ) )
							)
						)
					),
				'default_terms' => array(
						'espresso_people_type' => array(
							'staff' => array( 'espresso_people' )
							)
					)
			)
		);
	}



	/**
	 * 	additional_admin_hooks
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function additional_admin_hooks() {
		// is admin and not in M-Mode ?
		if ( is_admin() && ! EE_Maintenance_Mode::instance()->level() ) {
			add_filter( 'plugin_action_links', array( $this, 'plugin_actions' ), 10, 2 );
		}
	}



	/**
	 * plugin_actions
	 *
	 * Add a settings link to the Plugins page, so people can go straight from the plugin page to the settings page.
	 * @param $links
	 * @param $file
	 * @return array
	 */
	public function plugin_actions( $links, $file ) {
		if ( $file == EEA_PEOPLE_ADDON_BASENAME ) {
			// before other links
			array_unshift( $links, '<a href="admin.php?page=eea-people-addon">' . __('Settings') . '</a>' );
		}
		return $links;
	}



	/**
	 * Add our cpt strategy path to the core paths array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $core_paths incoming array of paths
	 */
	public function add_extra_core_paths( $core_paths ) {
		$core_paths[] = EEA_PEOPLE_ADDON_PATH . 'core/CPTs/';
		return $core_paths;
	}



	/**
	 * Adds extra helper paths for when EE_Registry::instance()->load_helper() is called.
	 *
	 * @since 1.0.0
	 *
	 * @param array $helper_paths
	 */
	public function add_extra_helper_paths( $helper_paths ) {
		$helper_paths[] = EEA_PEOPLE_ADDON_PATH . 'core/helpers/';
		return $helper_paths;
	}




}
// End of file EE_People_Addon.class.php
// Location: wp-content/plugins/eea-people-addon/EE_People_Addon.class.php
