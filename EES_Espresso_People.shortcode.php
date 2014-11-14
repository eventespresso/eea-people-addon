<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) { exit(); }
/*
 * ------------------------------------------------------------------------
 *
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author				Seth Shoultes
 * @ copyright		(c) 2008-2014 Event Espresso  All Rights Reserved.
 * @ license			http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link					http://www.eventespresso.com
 * @ version		 	EE4
 *
 * ------------------------------------------------------------------------
 *
 * EES_Espresso_People
 *
 * @package			Event Espresso
 * @subpackage		eea-people-addon
 * @author 				Brent Christensen
 * @ version		 	$VID:$
 *
 * ------------------------------------------------------------------------
 */
class EES_Espresso_People  extends EES_Shortcode {



	/**
	 * 	set_hooks - for hooking into EE Core, modules, etc
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_hooks() {
	}



	/**
	 * 	set_hooks_admin - for hooking into EE Admin Core, modules, etc
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_hooks_admin() {
	}



	/**
	 * 	set_definitions
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_definitions() {
	}



	/**
	 * 	run - initial shortcode module setup called during "wp_loaded" hook
	 * 	this method is primarily used for loading resources that will be required by the shortcode when it is actually processed
	 *
	 *  @access 	public
	 *  @param 	 WP $WP
	 *  @return 	void
	 */
	public function run( WP $WP ) {
		// this will trigger the EED_People module's run() method during the pre_get_posts hook point,
		// this allows us to initialize things, enqueue assets, etc,
		// as well, this saves an instantiation of the module in an array, using 'eea-people-addon' as the key, so that we can retrieve it
		EE_Registry::instance()->REQ->set( 'ee', 'eea-people-addon' );
		EED_People::$shortcode_active = TRUE;
	}



	/**
	 *    process_shortcode
	 *
	 *    [eea-people-addon]
	 *
	 * @access 	public
	 * @param 	array $attributes
	 * @return 	void
	 */
	public function process_shortcode( $attributes = array() ) {
		// make sure $attributes is an array
		$attributes = array_merge(
			// defaults
			array(),
			(array)$attributes
		);
		return EE_Registry::instance()->modules['eea-people-addon']->display_eea-people-addon( $attributes );
	}


}
// End of file EES_Espresso_People.shortcode.php
// Location: /wp-content/plugins/eea-people-addon/EES_Espresso_People.shortcode.php
