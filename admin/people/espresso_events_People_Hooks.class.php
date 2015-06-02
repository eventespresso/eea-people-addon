<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) { exit('NO direct script access allowed'); }
/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for Wordpress
 *
 * @package		Event Espresso
 * @author			Seth Shoultes
 * @copyright		(c)2009-2012 Event Espresso All Rights Reserved.
 * @license			http://eventespresso.com/support/terms-conditions/  ** see Plugin Licensing **
 * @link				http://www.eventespresso.com
 * @version			4.0
 *
 * ------------------------------------------------------------------------
 *
 * espresso_events_Registration_Form_Hooks
 * Hooks various messages logic so that it runs on indicated Events Admin Pages.
 * Commenting/docs common to all children classes is found in the EE_Admin_Hooks parent.
 *
 *
 * @package			espresso_events_eea-people-addon_Hooks
 * @subpackage		wp-content/plugins/eea-people-addon/admin/eea-people-addon/espresso_events_eea-people-addon_Hooks.class.php
 * @author				Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class espresso_events_People_Hooks extends EE_Admin_Hooks {

	/**
	 * Just a property for caching all EE_Person objects retrieved from the DB
	 *
	 * @var EE_Person[]
	 */
	protected $_all_people = array();

	protected function _set_hooks_properties() {
		$this->_name = 'people';

		//capability check
		if ( ! EE_Registry::instance()->CAP->current_user_can( 'ee_assign_people_type', 'people_type_meta_boxes_in_event_editor' ) ) {
			return;
		}


		//there is a metabox PER peopel type.  So first thing we need to do is get all people types.
		$people_types = EE_Registry::instance()->load_model( 'Term_Taxonomy' )->get_all( array( array( 'taxonomy' => 'espresso_people_type' ) ) );

		//setup metabox args
		foreach ( $people_types as $people_type ) {
			$term_name = $people_type->get_first_related( 'Term' )->get( 'name' );
			$this->_metaboxes[] = array(
				'page_route' => array( 'edit', 'create_new' ),
				'callback_args' => array( 'people_type' => $people_type ),
				'func' => 'people_type_metabox',
				'label' => sprintf( __( 'Assign %s', 'event_espresso'), $term_name ),
				'priority' => 'default',
				'context' => 'side',
				'id' => 'people_type_metabox_' . $people_type->get('term_taxonomy_id')
				);
		}

		$this->_scripts_styles = array(
			'registers' => array(
				'ee-cpt-people-css' => array(
					'url' => EEA_PEOPLE_ADDON_URL . 'admin/people/assets/cpt-to-people.css',
					'type' => 'css'
					)
				),
			'enqueues' => array(
				'ee-cpt-people-css' => array( 'edit', 'create_new' )
				)
			);

		add_filter( 'FHEE__Events_Admin_Page___insert_update_cpt_item__event_update_callbacks', array( $this, 'people_to_event_callback' ), 10 );

	}



	public function people_to_event_callback( $update_callbacks ) {
		$update_callbacks[] = array( $this, 'people_to_event_updates' );
		return $update_callbacks;
	}


	/**
	 * Handles attaching people to event relationships
	 *
	 * @param EE_Event $evtobj
	 * @param array $data   incoming data
	 *
	 * @return bool
	 */
	public function people_to_event_updates( $evtobj, $data ) {
		$saved_people = array();
		if ( ! isset( $data['people_to_cpt'] ) ) {
			//no people in system yet or doing from a context where the people ui isn't present.
			return true;
		}
		//loop through data and set things up for save.
		foreach ( $data['people_to_cpt'] as $type_id => $people_values ) {
			$existing_people = EE_Registry::instance()->load_model( 'Person_Post' )->get_all_people_ids_for_post_and_type( $evtobj->ID(), $type_id );
			$order_count = count( $people_values ) + 1;
			foreach( $people_values as $person_value ) {
				if ( ! isset( $person_value['PER_ID'] ) ) {
					continue;
				}
				$person_order = isset( $person_value['PER_order'] ) && $person_value['PER_order'] !== ''   ? $person_value['PER_order'] : $order_count;
				if ( in_array( $person_value['PER_ID'], $existing_people ) ) {
					$existing_person = EEM_Person_Post::instance()->get_one( array( array( 'PER_ID' => $person_value['PER_ID'], 'PT_ID' => $type_id, 'OBJ_ID' => $evtobj->ID() ) ) );
					$existing_person->set( 'PER_OBJ_order', $person_order );
					$existing_person->save();
					$saved_people[$type_id][] = (int) $person_value['PER_ID'];
					continue;
				}
				$values_to_save = array(
					'PER_ID' => $person_value['PER_ID'],
					'OBJ_ID' => $evtobj->ID(),
					'OBJ_type' => str_replace( 'EE_', '', get_class( $evtobj ) ),
					'PT_ID' => $type_id,
					'PER_OBJ_order' => $person_order
					);
				$new_rel = EE_Person_Post::new_instance( $values_to_save );
				$new_rel->save();
				$saved_people[$type_id][] = (int) $person_value['PER_ID'];
				$order_count ++;
			}

			//now let's grab the changes between the tow and we'll know that's what got removed.
			$rel_to_remove = empty( $saved_people[$type_id] ) ? $existing_people : array_diff( $existing_people, $saved_people[$type_id] );
			foreach( $rel_to_remove as $rel_per_id ) {
				$remove_where = array(
					'OBJ_ID' => $evtobj->ID(),
					'PT_ID' => $type_id,
					'PER_ID' => $rel_per_id
					);
				EE_Registry::instance()->load_model('Person_Post')->delete( array( $remove_where ) );
			}
		}
		//its entirely possible that all the people_event relationships for a particular type were removed.  So we need to account for that
		$types_for_evt = EE_Registry::instance()->load_model( 'Person_Post' )->get_all( array( array( 'OBJ_ID' => $evtobj->ID() ) ) );
		foreach ( $types_for_evt as $type_for_evt ) {
			if ( ! isset( $saved_people[$type_for_evt->get('PT_ID')] ) ) {
				$type_for_evt->delete_permanently();
			}
		}
		return true;
	}





	protected function _get_people( $query_args = array() ) {
		if ( ! empty( $this->_all_people ) && empty( $array ) ) {
			return $this->_all_people;
		}

		$query = array_merge( $query_args, array( 'status' => 'publish' ) );
		$this->_all_people = EE_Registry::instance()->load_model( 'Person' )->get_all(  $query_args );
		return $this->_all_people;
	}




	/**
	 * Callback for setting up the people type metaboxes.
	 *
	 * @param WP_Post $post
	 * @param array       $metabox_args
	 *
	 * @return string Metabox Content
	 */
	public function people_type_metabox( $post, $metabox_args ) {

		$incoming_args = $metabox_args['args'];
		$people_type = $incoming_args['people_type'];
		//if we don't have a valid people type then get out early!
		if ( ! $people_type instanceof EE_Term_Taxonomy ) {
			return;
		}

		EE_Registry::instance()->load_helper( 'Template' );

		$type_order_query= array( 'order_by' => 'Person_Post.PER_OBJ_order' );

		$template_args = array(
			'people_type' => $people_type,
			'type' => $people_type->get_first_related( 'Term' ),
			'people' => $this->_get_people( $type_order_query ),
			'assigned_people' => EE_Registry::instance()->load_model('Person')->get_people_for_event_and_type( $post->ID, $people_type->get('term_taxonomy_id') )
			);
		$template = EEA_PEOPLE_ADDON_PATH . 'admin/people/templates/people_type_event_metabox_details.template.php';
		EEH_Template::display_template( $template, $template_args );
	}

}
// End of file espresso_events_eea-people-addon_Hooks.class.php
// Location: /wp-content/plugins/eea-people-addon/admin/eea-people-addon/espresso_events_eea-people-addon_Hooks.class.php
