<?php
/**
 * This file contains the Admin Page Init for People
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage admin
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * People Admin Page init class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	admin
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class People_Admin_Page_Init extends EE_Admin_Page_CPT_Init  {

	/**
	 *  constructor
	 * @since 1.0.0
	 */
	public function __construct() {
		define( 'EEA_PEOPLE_PG_SLUG', 'espresso_people' );
		define( 'EEA_PEOPLE_LABEL', __( 'People Admin', 'event_espresso' ));
		define( 'EEA_PEOPLE_ADDON_ADMIN_URL', admin_url( 'admin.php?page=' . EEA_PEOPLE_PG_SLUG ));
		define( 'EEA_PEOPLE_ADDON_ADMIN_ASSETS_PATH', EEA_PEOPLE_ADDON_ADMIN . 'assets' . DS );
		define( 'EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL', EEA_PEOPLE_ADDON_URL . 'admin' . DS . 'people' . DS . 'assets' . DS );
		define( 'EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH', EEA_PEOPLE_ADDON_ADMIN . 'templates' . DS );
		define( 'EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_URL', EEA_PEOPLE_ADDON_URL . 'admin' . DS . 'people' . DS . 'templates' . DS );
		parent::__construct();
		$this->_folder_path = EEA_PEOPLE_ADDON_ADMIN;
	}





	protected function _set_init_properties() {
		$this->label = EEA_PEOPLE_LABEL;

		//setup default term if it does not exist
		$default_people_type_term_id = get_option( 'ee_people_addon_default_type_id', false );
		if ( ! $default_people_type_term_id && ! term_exists( 'staff', 'espresso_people_type' ) ) {
			$term = wp_insert_term( 'staff', 'espresso_people_type' );
			update_option( 'ee_people_addon_default_type_id', $term['term_id'] );
		}
	}



	/**
	*		_set_menu_map
	*
	*		@access 		protected
	*		@return 		void
	*/
	protected function _set_menu_map() {
		$this->_menu_map = new EE_Admin_Page_Sub_Menu( array(
			'menu_group' => 'addons',
			'menu_order' => 25,
			'show_on_menu' => EE_Admin_Page_Menu_Map::BLOG_ADMIN_ONLY,
			'parent_slug' => 'espresso_events',
			'menu_slug' => EEA_PEOPLE_PG_SLUG,
			'menu_label' => EEA_PEOPLE_LABEL,
			'capability' => 'ee_read_peoples',
			'admin_init_page' => $this
		));
	}



}
// End of file People_Admin_Page_Init.core.php
// Location: /wp-content/plugins/eea-people-addon/admin/people/People_Admin_Page_Init.core.php
