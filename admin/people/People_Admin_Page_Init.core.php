<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
* Event Espresso
*
* Event Registration and Management Plugin for WordPress
*
* @ package 		Event Espresso
* @ author			Seth Shoultes
* @ copyright 	(c) 2008-2011 Event Espresso  All Rights Reserved.
* @ license 		{@link http://eventespresso.com/support/terms-conditions/}   * see Plugin Licensing *
* @ link 				{@link http://www.eventespresso.com}
* @ since		 	$VID:$
*
* ------------------------------------------------------------------------
*
* People_Admin_Page_Init class
*
* This is the init for the eea-people-addon Addon Admin Pages.  See EE_Admin_Page_Init for method inline docs.
*
* @package			Event Espresso (eea-people-addon addon)
* @subpackage		admin/People_Admin_Page_Init.core.php
* @author				Darren Ethier
*
* ------------------------------------------------------------------------
*/
class People_Admin_Page_Init extends EE_Admin_Page_Init  {

	/**
	 * 	constructor
	 *
	 * @access public
	 * @return \People_Admin_Page_Init
	 */
	public function __construct() {

		do_action( 'AHEE_log', __FILE__, __FUNCTION__, '' );

		define( 'EEA-PEOPLE-ADDON_PG_SLUG', 'eea-people-addon' );
		define( 'EEA-PEOPLE-ADDON_LABEL', __( 'eea-people-addon', 'event_espresso' ));
		define( 'EEA_PEOPLE_ADDON_ADMIN_URL', admin_url( 'admin.php?page=' . EEA-PEOPLE-ADDON_PG_SLUG ));
		define( 'EEA_PEOPLE_ADDON_ADMIN_ASSETS_PATH', EEA_PEOPLE_ADDON_ADMIN . 'assets' . DS );
		define( 'EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL', EEA_PEOPLE_ADDON_URL . 'admin' . DS . 'eea-people-addon' . DS . 'assets' . DS );
		define( 'EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH', EEA_PEOPLE_ADDON_ADMIN . 'templates' . DS );
		define( 'EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_URL', EEA_PEOPLE_ADDON_URL . 'admin' . DS . 'eea-people-addon' . DS . 'templates' . DS );

		parent::__construct();
		$this->_folder_path = EEA_PEOPLE_ADDON_ADMIN;

	}





	protected function _set_init_properties() {
		$this->label = EEA-PEOPLE-ADDON_LABEL;
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
			'menu_slug' => EEA-PEOPLE-ADDON_PG_SLUG,
			'menu_label' => EEA-PEOPLE-ADDON_LABEL,
			'capability' => 'administrator',
			'admin_init_page' => $this
		));
	}



}
// End of file People_Admin_Page_Init.core.php
// Location: /wp-content/plugins/eea-people-addon/admin/eea-people-addon/People_Admin_Page_Init.core.php
