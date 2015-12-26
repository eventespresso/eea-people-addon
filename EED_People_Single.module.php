<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/*
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package		Event Espresso
 * @ author			Event Espresso
 * @ copyright	(c) 2008-2014 Event Espresso  All Rights Reserved.
 * @ license		http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link				http://www.eventespresso.com
 * @ version		$VID:$
 *
 * ------------------------------------------------------------------------
 */
/**
 * Class  EED_People
 *
 * @package			Event Espresso
 * @subpackage		eea-people-addon
 * @author 				Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EED_People_Single extends EED_Module {

	public static $templates_path;



	/**
	 * @return EED_People_Single
	 */
	public static function instance() {
		return parent::get_instance( __CLASS__ );
	}



	 /**
	  * 	set_hooks - for hooking into EE Core, other modules, etc
	  *
	  *  @access 	public
	  *  @return 	void
	  */
	 public static function set_hooks() {
		 EE_Config::register_route( 'person', 'People_Single', 'run' );
		 EED_People_Single::$templates_path = EEA_PEOPLE_ADDON_PATH . 'public' . DS . 'templates' . DS;
	 }

	 /**
	  * 	set_hooks_admin - for hooking into EE Admin Core, other modules, etc
	  *
	  *  @access 	public
	  *  @return 	void
	  */
	 public static function set_hooks_admin() {
	 }



	/**
	 *    set_config
	 *
	 * @return \EE_People_Config
	 */
	public function set_config() {
		$this->set_config_section( 'template_settings' );
		$this->set_config_class( 'EE_People_Config' );
		$this->set_config_name( 'EED_People_Single' );
		return $this->config();
	}



	/**
	  *    run - initial module setup
	  *
	  * @access    public
	  * @param  WP $WP
	  * @return    void
	  */
	 public function run( $WP ) {
		 add_filter( 'template_include', array( $this, 'template_include' ), 999 );
	 	EE_Registry::instance()->load_helper( 'People_View' );
	 }






	/**
	 * 	enqueue_scripts - Load the scripts and css
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function enqueue_scripts() {
	}



	/**
	 * deciding what template to include for person details.
	 *
	 * @param string $template Template being included
	 * @return string new template
	 * @throws \EE_Error
	 */
	public function template_include( $template ) {
		global $post;
		// not a custom template?
		if ( EE_Registry::instance()->load_core( 'Front_Controller', array(), false )->get_selected_template() != 'single-espresso_people.php' && ! post_password_required( $post ) ) {
			EEH_Template::load_espresso_theme_functions();
			//add extra people data
			add_filter( 'the_content', array( 'EED_People_Single', 'person_details' ), 100 );
		}
		return $template;
	}



	/**
	 * hooking into the person details content of a template
	 *
	 * @since 1.0.0
	 *
	 * @param string $content
	 * @return string
	 */
	public static function person_details( $content ) {
		global $post;
		if ( is_single() && $post->post_type == 'espresso_people' && ! post_password_required() ) {
			$content .= EEH_Template::locate_template( 'content-espresso_people-details.php' );
		}
		return $content;
	}



	/**
	 *        @ override magic methods
	 *        @ return void
	 * @param $a
	 * @param $b
	 * @return bool
	 */
	public function __set($a,$b) { return FALSE; }
	public function __get($a) { return FALSE; }
	public function __isset($a) { return FALSE; }
	public function __unset($a) { return FALSE; }
	public function __clone() { return FALSE; }
	public function __wakeup() { return FALSE; }
	public function __destruct() { return FALSE; }



}
// End of file EED_People.module.php
// Location: /wp-content/plugins/eea-people-addon/EED_People.module.php
