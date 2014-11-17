<?php
/**
 * Contains test class for eea-people-addon.php
 *
 * @since  		1.0.0
 * @package 		EE People Addon
 * @subpackage 	tests
 */


/**
 * Test class for eea-people-addon.php
 *
 * @since 		1.0.0
 * @package 		EE4 Addon Skeleton
 * @subpackage 	tests
 */
class eea_people_addon_tests extends EE_UnitTestCase {

	/**
	 * Tests the loading of the main file
	 *
	 * @since 1.0.0
	 */
	function test_loading_ee_people() {
		$this->assertEquals( has_action('AHEE__EE_System__load_espresso_addons', 'load_espresso_eea_people_addon'), 10 );
		$this->assertTrue( class_exists( 'EE_People' ) );
	}
}
