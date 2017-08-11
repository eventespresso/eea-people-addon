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




	/**
	 * This returns all the people attached to an event grouped by the people type.
	 *
	 * @param int $EVT_ID   If provided then return for specific event.  If not provided, attempt to use the $post
	 *                      	     global for the event.
	 *
	 * Array is returned in this format:
	 * @type array {
	 *       'people_type_name' => EE_Person[]
	 * }
	 *
	 * @return array
	 */
	public static function get_people_for_event( $EVT_ID = 0 ) {
		return self::_get_rel_objects( $EVT_ID );
	}




	/**
	 * This returns all the events attached to a person grouped by the people type.
	 *
	 * @param int $PER_ID   If provided then return for specific person.  If not provided, attempt to use the $post
	 *                      	     global for the person.
	 *
	 * Array is returned in this format:
	 * @type array {
	 *       'people_type_name' => EE_Event[]
	 * }
	 *
	 * @return array
	 */
	public static function get_events_for_person( $PER_ID = 0 ) {
		return self::_get_rel_objects( $PER_ID, 'Event');
	}




	/**
	 * Utility function used to get various items from the Person_Post model depending on the given params.
	 * When no obj_id is provided, we use what is set as the $post id if present.
	 *
	 * @param int         $obj_id              What the obj_id is for comparing against.
	 * @param string    $primary_obj_type What it is being retrieved.
	 *
	 * @return EE_Base_Class[]  (what is returned depends on what the primary_obj_type is)
	 */
	protected static function _get_rel_objects( $obj_id = 0,  $primary_obj_type = 'Person' ) {
		$objects = array();
		if ( empty( $obj_id ) ) {
			global $post;
			$obj_id = $post instanceof WP_Post ? $post->ID : $obj_id;
		}

		//still empty? return empty array
		if ( empty( $obj_id ) ) return array();

		if ( $primary_obj_type !=  'Person' ) {
			$where = array( 'PER_ID' => $obj_id, 'OBJ_type' => $primary_obj_type );
			$query = array( $where );
		} else {
			$where = array( 'OBJ_ID' => $obj_id );
			$query = array( $where, 'order_by' => array( 'PER_OBJ_order' => 'ASC' ) );
		}

		$object_items = EEM_Person_Post::instance()->get_all( $query );
		$term_name_cache = array();
		if ( method_exists( EEM_Event::instance(), 'public_event_stati' ) ) {
			$public_event_stati = EEM_Event::instance()->public_event_stati();
		} else {
			$public_event_stati = get_post_stati( array( 'public' => TRUE ));
			foreach ( EEM_Event::instance()->get_custom_post_statuses() as $custom_post_status ) {
				$public_event_stati[] = strtolower( str_replace( ' ', '_', $custom_post_status ) );
			}
		}

		foreach ( $object_items as $object_item ) {
			if ( ! isset( $term_name_cache[$object_item->get('PT_ID')] )  || ! isset( $objects[$term_name][$object_item->ID()] ) ) {
				$term_name =  EEM_Term_Taxonomy::instance()->get_one_by_ID( $object_item->get( 'PT_ID' ) )->get_first_related( 'Term' )->get( 'name' );
				$related_object = $object_item->get_first_related( $primary_obj_type, array( array( 'status' => array( 
					'IN', apply_filters( 'FHEE__EEH_People_View__get_rel_objects__public_event_stati', $public_event_stati ) 
				) ) ) );
				if ( $related_object instanceof EE_Base_Class ) {
					$objects[$term_name][$object_item->ID()] = $related_object;
					$term_name_cache[$object_item->get('PT_ID')] = $term_name;
				}
			}
		}
		return $objects;
	}
} //end EEH_People_View
