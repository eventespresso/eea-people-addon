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

		//verify config class
		$this->assertInstanceOf( 'EE_People_Config', EE_Registry::instance()->CFG->addons->EE_People );

		//verify module has been setup correctly
		$this->assertTrue( isset( EE_Registry::instance()->modules->EED_People_Single ) );

		//verify administrator has correct caps.
		$mapped_caps_to_verify = array(
			'ee_edit_people', 'ee_read_people', 'ee_delete_people',
			);
		$non_mapped_caps_to_verify = array(
			'ee_read_peoples', 'ee_edit_peoples', 'ee_edit_others_peoples', 'ee_publish_peoples', 'ee_read_private_peoples', 'ee_delete_peoples', 'ee_delete_private_peoples', 'ee_delete_published_peoples', 'ee_delete_others_peoples', 'ee_edit_private_peoples', 'ee_edit_published_peoples', 'ee_manage_people_types', 'ee_edit_people_type', 'ee_delete_people_type', 'ee_assign_people_type', 'ee_manage_people_categories', 'ee_edit_people_category', 'ee_delete_people_category', 'ee_assign_people_category'
			);
		$user = $this->factory->user->create_and_get();
		$user->add_role( 'administrator' );

		foreach( $non_mapped_caps_to_verify as $cap ) {
			$can_user = EE_Capabilities::instance()->user_can( $user->ID, $cap, "testing_users", 1 );
			$msg =  sprintf( 'User should have %s but they do not.', $cap);
			$this->assertTrue( $can_user,$msg );
		}

		//note cannot test mapped_caps until https://core.trac.wordpress.org/ticket/16956 gets dealt with.

		// test that the espresso_people cpt got loaded okay
		$post_types = get_post_types();
		$this->assertContains( 'espresso_people', $post_types );

		//test that custom taxonomies got setup okay.
		$this->assertTrue( taxonomy_exists( 'espresso_people_type' ) );
		$this->assertTrue( taxonomy_exists( 'espresso_people_categories' ) );

	}


} //end EE_People_Tests
