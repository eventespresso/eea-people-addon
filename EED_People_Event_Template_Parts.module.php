<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class EED_People_Event_Template_Parts
 *
 * @package			Event Espresso
 * @subpackage		eea-people-addon
 * @author 			Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
class EED_People_Event_Template_Parts extends EED_Module {

	public static $templates_path;



	/**
	 * @return EED_People_Event_Template_Parts
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
         add_action(
            'AHEE__EED_Event_Single__use_filterable_display_order__after_add_filters',
            array( 'EED_People_Event_Template_Parts', 'add_people_event_details_filters' ),
            10
         );
         add_action(
            'AHEE__EED_Events_Archive__use_filterable_display_order__after_add_filters',
            array( 'EED_People_Event_Template_Parts', 'add_people_event_details_filters' ),
            10
         );
         add_action(
            'AHEE__EED_Events_Archive__use_filterable_display_order__after_remove_filters',
            array( 'EED_People_Event_Template_Parts', 'remove_people_event_details_filters' ),
            10
         );
         add_action(
			 'AHEE__EED_Event_Single__initialize_template_parts',
			 array( 'EED_People_Event_Template_Parts', 'add_event_single_template_parts' ),
			 10
		 );
         add_action(
			 'AHEE__EED_Event_Archive__initialize_template_parts',
			 array( 'EED_People_Event_Template_Parts', 'add_event_archive_template_parts' ),
			 10
		 );
	 }

	 /**
	  * 	set_hooks_admin - for hooking into EE Admin Core, other modules, etc
	  *
	  *  @access 	public
	  *  @return 	void
	  */
	 public static function set_hooks_admin() {
		 add_action(
			 'AHEE__EED_Event_Single__initialize_template_parts',
			 array( 'EED_People_Event_Template_Parts', 'add_event_single_template_parts' ),
			 10
		 );
		 add_action(
			 'AHEE__EED_Event_Archive__initialize_template_parts',
			 array( 'EED_People_Event_Template_Parts', 'add_event_archive_template_parts' ),
			 10
		 );
		 // this is for a dynamic hook in \EED_Event_Single_Caff::update_event_single_order()
		 add_action(
			 'AHEE__EED_Event_Single__update_event_single_order__display_order_people',
			 array( 'EED_People_Event_Template_Parts', 'update_event_single_display_order_people' ),
			 10
		 );
		 // this is for a dynamic hook in \EED_Events_Archive_Caff::update_event_archive_order()
		 add_action(
			 'AHEE__EED_Events_Archive__update_event_archive_order__display_order_people',
			 array( 'EED_People_Event_Template_Parts', 'update_event_archive_display_order_people' ),
			 10
		 );
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
	 *    update_event_single_template_settings_form
	 *
	 * @access 	public
	 * @param 	int $priority
	 * @return 	array
	 */
	public static function update_event_single_display_order_people( $priority ) {
		EED_People_Event_Template_Parts::instance()->set_config()->event_single_display_order_people = $priority;
		EED_People_Event_Template_Parts::instance()->_update_config(
			EED_People_Event_Template_Parts::instance()->config()
		);
	}



	/**
	 *    update_event_single_template_settings_form
	 *
	 * @access 	public
	 * @param 	int $priority
	 * @return 	array
	 */
	public static function update_event_archive_display_order_people( $priority ) {
		EED_People_Event_Template_Parts::instance()->set_config()->event_archive_display_order_people = $priority;
		EED_People_Event_Template_Parts::instance()->_update_config(
			EED_People_Event_Template_Parts::instance()->config()
		);
	}



	/**
	  *    run - initial module setup
	  *
	  * @access    public
	  * @param  WP $WP
	  * @return    void
	  */
	 public function run( $WP ) {
	 }



	/**
	 * get_event_display_order_people
	 *
	 * @access    protected
	 * @param bool $archive
	 * @return int
	 */
	 protected static function get_event_display_order_people( $archive = true ) {
		 $event_display_order_people = 125;
		 EED_People_Event_Template_Parts::instance()->set_config();
		 $config = EED_People_Event_Template_Parts::instance()->config();
		 if ( $config instanceof EE_People_Config ) {
			 if ( ! $archive ) {
				 $config->event_single_display_order_people = isset( $config->event_single_display_order_people )
					 ? $config->event_single_display_order_people : 125;
				 $event_display_order_people = $config->event_single_display_order_people;
			 } else {
				 $config->event_archive_display_order_people = isset( $config->event_archive_display_order_people )
					 ? $config->event_archive_display_order_people : 125;
				 $event_display_order_people = $config->event_archive_display_order_people;
			 }
		 }
		 return $event_display_order_people;
	 }



    /**
     * add_people_event_details_filters
     *
     * @return void
     */
    public static function add_people_event_details_filters() {
		//hook into event details right before event details content
        add_filter( 'the_content', array( 'EED_People_Event_Template_Parts', 'people_event_details' ), 90 );
    }



    /**
     * remove_people_event_details_filters
     *
     * @return void
     */
    public static function remove_people_event_details_filters() {
		remove_filter( 'the_content', array( 'EED_People_Event_Template_Parts', 'people_event_details' ), 90 );
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
		if ( function_exists( 'is_espresso_event' ) && is_espresso_event() ) {
			EE_Registry::instance()->load_helper( 'Template' );
			$content = EEH_Template::locate_template( 'content-espresso_events-people.php' ) . $content;
		}
		return $content;
    }



    /**
	 * add_event_archive_template_parts
	 *
	 * @param EE_Template_Part_Manager $template_parts
	 * @return array
	 */
	public static function add_event_archive_template_parts( EE_Template_Part_Manager $template_parts ) {
		return EED_People_Event_Template_Parts::add_event_template_parts( $template_parts, true );
	}



    /**
	 * add_event_single_template_parts
	 *
	 * @param EE_Template_Part_Manager $template_parts
	 * @return array
	 */
	public static function add_event_single_template_parts( EE_Template_Part_Manager $template_parts ) {
		return EED_People_Event_Template_Parts::add_event_template_parts( $template_parts, false );
	}



	/**
	 * add_event_template_parts
	 *
	 * @param EE_Template_Part_Manager $template_parts
	 * @param bool                     $archive
	 * @return array
	 */
	public static function add_event_template_parts( EE_Template_Part_Manager $template_parts, $archive = true ) {
		EED_People_Event_Template_Parts::$templates_path = EEA_PEOPLE_ADDON_PATH . 'public' . DS . 'templates' . DS;
		$template_parts->add_template_part(
			'people',
			__( "People", "event_espresso" ),
			EED_People_Event_Template_Parts::$templates_path . 'content-espresso_events-people.php',
			EED_People_Event_Template_Parts::get_event_display_order_people( $archive )
		);
		return $template_parts;
	}


	/**
	 * @ override magic methods
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
// End of file EED_People_Event_Template_Parts.module.php
// Location: /wp-content/plugins/eea-people-addon/EED_People_Event_Template_Parts.module.php
