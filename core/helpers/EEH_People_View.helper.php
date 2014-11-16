<?php
/**
 * This file contains the CPT strategy class for persons
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage view, helper
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
 /**
 *
 * EEH_People_View Helper class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	view, helper
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEH_People_View extends EEH_Base {

	/**
	 * caches EE_Person object
	 *
	 * @var EE_Person
	 */
	protected static $_person = null;



	/**
	 * Attempts to retrieve an EE_Person any way it can.
	 *
	 * @param int|WP_Post $PER_ID person ID or WP_Post object (optional) If not included try to obtain it via request.
	 *
	 * @return EE_Person
	 */
	public static function get_person( $PER_ID = 0 ) {
		$PER_ID = $PER_ID instanceof WP_Post ? $PER_ID->ID : absint( $PER_ID );
		// do we already have the Person  you are looking for?
		if ( EEH_People_View::$_person instanceof EE_Person && $PER_ID && EEH_People_View::$_person->ID() === $PER_ID ) {
			return EEH_People_View::$_person;
		}
		EEH_People_View::$_person = NULL;
		// international newspaper?
		global $post;
		// if this is being called from an EE_Person post, then we can just grab the attached EE_Person object
		 if ( isset( $post->post_type ) && $post->post_type == 'espresso_people' || $PER_ID ) {
			// grab the person we're looking for
			if ( isset( $post->EE_Person ) && ( $PER_ID == 0 || ( $PER_ID == $post->ID ))) {
				EEH_People_View::$_person = $post->EE_Person;
			}
			// now if we STILL do NOT have an EE_Person model object, BUT we have an Person ID...
			if ( ! EEH_People_View::$_person instanceof EE_Person && $PER_ID ) {
				// sigh... pull it from the db
				EEH_People_View::$_person = EEM_Person::instance()->get_one_by_ID( $PER_ID );
			}
		}
		return EEH_People_View::$_person;
	}
} //end EEH_People_View
