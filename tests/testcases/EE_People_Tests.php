<?php
/**
 * Contains test class for testing the EE_People class
 *
 * @since  		1.0.0
 * @package 		EE People Addon
 * @subpackage 	tests
 */


/**
 * Test class for EE_People
 *
 * @since 		1.0.0
 * @package 		EE People Addon
 * @subpackage 	tests
 */
class EE_People_Tests extends EE_UnitTestCase {


	/**
	 * Tests stuff that happened in the constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function test_construct() {

		$addon = EE_Registry::instance()->addons->EE_People;
		//filters should be present on load
		$this->assertEquals( has_action('FHEE__EE_Registry__load_core__core_paths', array( $addon, 'add_extra_core_paths') ), 10 );
		$this->assertEquals( has_action('FHEE__EE_Registry__load_helper__helper_paths', array( $addon, 'add_extra_helper_paths') ), 10 );

		//verify template_hooks file is loaded
		$this->assertTrue( class_exists( 'EE_People_Template_Hooks' ) );
	}



	/**
	 * Test that things executed in register_addon got setup correctly.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function test_register_addon() {

		// test that the espresso_people cpt got loaded okay
		$post_types = get_post_types();
		$this->assertContains( 'espresso_people', $post_types );
	}


} //end EE_People_Tests
