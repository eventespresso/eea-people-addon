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
 * @author 				Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
class EED_People_Single extends EED_Module {


	/**
	 * @return EED_People
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
	 *
	 * @return string new template
	 */
	public function template_include( $template ) {
		global $post;

		// not a custom template?
		if ( EE_Front_Controller::instance()->get_selected_template() != 'single-espresso_people.php' && ! post_password_required( $post ) ) {
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
	 *		@ override magic methods
	 *		@ return void
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