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
		 add_action( 'AHEE__EED_Event_Single__use_filterable_display_order__after_add_filters', array( 'EED_People_Single', 'add_event_single_filters' ), 10 );
		 add_filter( 'FHEE__EED_Event_Single__set_config__template_parts', array( 'EED_People_Single', 'filter_event_single_template_parts' ), 10 );
	 }

	 /**
	  * 	set_hooks_admin - for hooking into EE Admin Core, other modules, etc
	  *
	  *  @access 	public
	  *  @return 	void
	  */
	 public static function set_hooks_admin() {
		 // EED_Event_Single
		 add_filter( 'FHEE__EED_Event_Single__template_settings_form__event_single_order_array', array( 'EED_People_Single', 'event_single_order_array' ), 10 );
		 add_filter( 'FHEE__EED_Event_Single__template_settings_form__templates', array( 'EED_People_Single', 'event_single_template_settings_form' ), 10 );
		 add_action( 'AHEE__EED_Event_Single__update_event_single_order__display_order_people', array( 'EED_People_Single', 'update_event_single_display_order_people' ), 10 );
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
	 *    event_single_order_array
	 *
	 * @access    public
	 * @param  array $event_single_order_array
	 * @return    array
	 */
	public static function event_single_order_array( $event_single_order_array ) {
		$event_single_order_array[ EED_People_Single::instance()->set_config()->event_single_display_order_people ] = 'people';
		return $event_single_order_array;
	}



	/**
	 *    event_single_template_settings_form
	 *
	 * @access    public
	 * @param  array $template_settings_form
	 * @return    array
	 */
	public static function event_single_template_settings_form( $template_settings_form ) {
		$template_settings_form[ 'people' ] = __( "People", "event_espresso" );
		return $template_settings_form;
	}



	/**
	 *    update_event_single_template_settings_form
	 *
	 * @access 	public
	 * @param 	int $priority
	 * @return 	array
	 */
	public static function update_event_single_display_order_people( $priority ) {
		EED_People_Single::instance()->set_config()->event_single_display_order_people = $priority;
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
			$content .= EEH_Template::locate_template( EED_People_Single::$templates_path . 'content-espresso_people-details.php' );
		}
		return $content;
	}



	/*********************************** following moved over from /public/template_hooks.php ***********************************/



	/**
	 * add_event_single_filters
	 *
	 * @return void
	 */
	public static function add_event_single_filters() {
		//hook into event details right before event details content
		add_filter( 'the_content', array( __CLASS__, 'people_event_details' ), 100 );
	}



	/**
	 * Registers the folder for core people templates to be included with the template path locator.
	 * Note: To customize, just copy the template from /public/templates/* and put in your theme folder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $template_paths incoming paths
	 * @return array
	 */
	public static function add_template_folder_to_paths( $template_paths ) {
		$template_paths[] = EEA_PEOPLE_ADDON_PATH . 'public/templates/';
		return $template_paths;
	}



	/**
	 * filter_event_single_template_parts
	 *
	 * @param EE_Template_Part_Manager $template_parts
	 * @return array
	 */
	public static function filter_event_single_template_parts( EE_Template_Part_Manager $template_parts ) {
		EED_People_Single::instance()->set_config();
		$config = EED_People_Single::instance()->config();
		if ( $config instanceof EE_People_Config ) {
			$config->event_single_display_order_people = isset( $config->event_single_display_order_people ) ? $config->event_single_display_order_people : 115;
			$template_parts->add_template_part(
				'people',
				EED_People_Single::$templates_path . 'content-espresso_events-people.php',
				$config->event_single_display_order_people
			);
		}
		return $template_parts;
	}



	/**
	 * This is added right before event content is displayed for an event
	 *
	 * @since 1.0.0
	 *
	 * @param string $content (the content so far).
	 *
	 * @return string show people attached to an event.
	 */
	public static function people_event_details( $content ) {
		return $content . EEH_Template::locate_template( EED_People_Single::$templates_path . 'content-espresso_events-people.php' );
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
