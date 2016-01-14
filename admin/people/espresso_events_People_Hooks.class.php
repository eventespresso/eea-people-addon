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


		//there is a metabox PER people type.  So first thing we need to do is get all people types.
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
				'ee-cpt-people-css' => array( 'edit', 'create_new', 'default' )
				)
			);

		add_filter( 'FHEE__Events_Admin_Page___insert_update_cpt_item__event_update_callbacks', array( $this, 'people_to_event_callback' ), 10 );

		//add filter for event list table queries
		add_filter( 'FHEE__Events_Admin_Page__get_events__where', array( $this, 'filter_events_list_table_where' ), 10, 2 );
		add_filter( 'FHEE__EE_Admin_Page___display_admin_list_table_page__before_list_table__template_arg', array( $this, 'filtered_events_list_table_title' ), 10, 4 );

		//add filter for adding to people assigned column to event list table and the legend!
		add_filter( 'FHEE_manage_toplevel_page_espresso_events_columns', array( $this, 'add_people_column' ), 10, 2 );
		add_action( 'AHEE__EE_Admin_List_Table__column_people_on_event__toplevel_page_espresso_events', array( $this, 'display_people_column' ), 10, 2 );
		add_filter( 'FHEE__Events_Admin_Page___event_legend_items__items', array( $this, 'additional_legend_items' ), 11 );

		//hook into when events are deleted to remove the people relations for those events.
		add_action( 'AHEE__EE_Base_Class__delete_permanently__before', array( $this, 'delete_people_relations_on_related_delete' ) );

	}





	/**
	 * Callback for FHEE__manage_toplevel_page_espresso_events_columns.
	 * Add an additional people on event column for the event list table.
	 *
	 * @param array $columns incoming array of columns already on event.
	 * @param WP_Screen $screen
	 *
	 * @return array
	 */
	public function add_people_column( $columns, $screen ) {
		//want to insert right before actions
		$new_columns = array();

		foreach ( $columns as $column_name => $column_text ) {
			if ( $column_name == 'actions' ) {
				$new_columns['people_on_event'] = '<span class="dashicons dashicons-businessman"></span>';
			}
			$new_columns[ $column_name ] = $column_text;
		}

		return ! empty( $new_columns ) ? $new_columns : $columns;
	}





	/**
	 * Callback for AHEE__EE_Admin_List_Table__column_people_on_event__toplevel_page_espresso_events action.
	 * Displays the content for the people column added to the event list table.
	 *
	 * @param EE_Event $event
	 * @param $screen
	 * @return string
	 */
	public function display_people_column( $event, $screen ) {
		if ( ! $event instanceof EE_Event ) {
			return '';
		}


		//get count of people on the event.
		$count_people = EEM_Person::instance()->count(
			array(
				array(
					'Person_Post.OBJ_type' => 'Event',
					'Person_Post.OBJ_ID' => $event->ID()
				),
			),
			null,
			true
		);

		//link to people list table filtered by the event.
		$link = add_query_arg( array(
			'page' => 'espresso_people',
			'action' => 'default',
			'EVT_ID' => $event->ID()
			),
			admin_url( 'admin.php' )
		);
		echo '<a href="' . $link . '">' . $count_people . '</a>';
	}




	public function people_to_event_callback( $update_callbacks ) {
		$update_callbacks[] = array( $this, 'people_to_event_updates' );
		return $update_callbacks;
	}




	/**
	 * Callback for FHEE__Events_Admin_Page__get_events__where to filter the where conditions for events being queried for
	 * event list table.
	 * @param $original_where
	 * @param $req_data
	 */
	public function filter_events_list_table_where( $original_where, $req_data ) {
		if ( isset( $req_data['PER_ID'] ) ) {
			$original_where['AND*Person'] = array(
				'Person.PER_ID' => $req_data['PER_ID']
			);
		}

		if ( isset( $req_data['PT_ID'] ) ) {
			$original_where['AND*Person_Post'] = array(
				'Person.Person_Post.PT_ID' => $req_data['PT_ID']
			);
		}
		return $original_where;
	}





	/**
	 * Callback for FHEE__EE_Admin_Page___display_admin_list_table_page__before_list_table__template_arg used to add
	 * context title for when the event list table is filtered by person and person type.
	 * @param $original_content
	 * @param $page_slug
	 * @param $request_data
	 * @param $request_action
	 */
	public function filtered_events_list_table_title( $original_content, $page_slug, $request_data, $request_action ) {
		if ( $page_slug === 'espresso_events' && $request_action === 'default' ) {
			$person = $person_type = null;
			if ( isset( $request_data['PER_ID'] ) ) {
				$person = EEM_Person::instance()->get_one_by_ID( $request_data['PER_ID'] );
			}

			if ( isset( $request_data['PT_ID'] ) ) {
				$person_type = EEM_Term_Taxonomy::instance()->get_one_by_ID( $request_data['PT_ID'] );
				$person_type = $person_type instanceof EE_Term_Taxonomy ? $person_type->get_first_related( 'Term' ) : null;
			}

			if ( $person instanceof EE_Person && $person_type instanceof EE_Term ) {
				$title = sprintf( __( 'Viewing the events that %s is assigned to as %s', 'event_espresso' ), $person->full_name(), $person_type->name() );
			} elseif ( $person instanceof EE_Person && ! $person_type instanceof EE_Term ) {
				$title = sprintf( __( 'Viewing the events that %s is assigned to', 'event_espresso' ), $person->full_name() );
			} elseif ( ! $person instanceof EE_Person && $person_type instanceof EE_Term ) {
				$title = sprintf( __( 'Viewing the events that has at least one person assigned as %s.', 'event_espresso' ), $person_type->name() );
			} else {
				$title = '';
			}
			if ( $title ) {
				return '<h2>' . $title . '</h2>';
			}
		}
		return $original_content;
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
		EE_Registry::instance()->load_helper( 'URL' );

		$type_order_query= array( 'order_by' => 'Person_Post.PER_OBJ_order' );

		$template_args = array(
			'people_type' => $people_type,
			'type' => $people_type->get_first_related( 'Term' ),
			'people' => $this->_get_people( $type_order_query ),
			'assigned_people' => EE_Registry::instance()->load_model('Person')->get_people_for_event_and_type( $post->ID, $people_type->get('term_taxonomy_id') ),
			'create_person_link' => EEH_URL::add_query_args_and_nonce( array( 'action' => 'create_new' ), EEA_PEOPLE_ADDON_ADMIN_URL )
			);
		$template = EEA_PEOPLE_ADDON_PATH . 'admin/people/templates/people_type_event_metabox_details.template.php';
		EEH_Template::display_template( $template, $template_args );
	}


	/**
	 * Callback for FHEE__Events_Admin_Page___event_legend_items__items to add the people icon to the event list table legend.
	 * @param  array $items
	 * @return array
	 */
	public function additional_legend_items( $items ) {
		$items['people'] = array(
			'class' => 'dashicons dashicons-businessman',
			'desc' => __( 'People assigned to Event', 'event_espresso' )
		);
		unset( $items['empty'] );
		return $items;
	}


	/**
	 * Callback for AHEE__EE_Base_Class__delete_before hook so we can ensure any person relationships for an item being deleted
	 * are also handled.
	 *
	 * @param EE_Base_Class $model_object
	 */
	public function delete_people_relations_on_related_delete( EE_Base_Class $model_object ) {
		if ( $model_object instanceof EE_Event ) {
			$remove_where = array(
				'OBJ_ID' => $model_object->ID(),
				'OBJ_type' => 'Event',
			);
			EEM_Person_Post::instance()->delete( array( $remove_where ) );
		}
	}

}
// End of file espresso_events_eea-people-addon_Hooks.class.php
// Location: /wp-content/plugins/eea-people-addon/admin/eea-people-addon/espresso_events_eea-people-addon_Hooks.class.php
