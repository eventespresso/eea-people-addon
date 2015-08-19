<?php
/**
 * This file contains the template hooks class for setting up out of the box templating for the people addon
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage template
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * Template Hook utility class.
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	template
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_People_Template_Hooks {


	public static function set_hooks() {

		//add our templates folder to the EEH_Template::locate_template() paths checked.
		add_filter( 'FHEE__EEH_Template__locate_template__template_folder_paths', array( __CLASS__, 'add_template_folder_to_paths' ), 10 );

		//hook into event details right before event deteails content
		add_action( 'the_content', array( __CLASS__, 'people_event_details' ), 90 );

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

} //end class EE_People_Template_Hooks

EE_People_Template_Hooks::set_hooks();
