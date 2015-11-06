<?php
/**
 * This file contains the Admin List Table for People
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage admin
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * People Admin List Table class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	admin
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_People_List_Table extends EE_Admin_List_Table {


	protected function _setup_data() {
		$this->_data = $this->_view != 'trash' ? $this->_admin_page->get_people( $this->_per_page ) : $this->_admin_page->get_people( $this->_per_page, FALSE, TRUE );
		$this->_all_data_count = $this->_view != 'trash' ? $this->_admin_page->get_people( $this->_per_page, TRUE ) : $this->_admin_page->get_people( $this->_per_page,TRUE, TRUE );
	}




	protected function _set_properties() {
		$this->_wp_list_args = array(
			'singular' => __('person', 'event_espresso'),
			'plural' => __('people', 'event_espresso'),
			'ajax' => TRUE,
			'screen' => $this->_admin_page->get_current_screen()->id
			);

		$this->_columns = array(
				'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
				'PER_ID' => __('ID', 'event_espresso'),
				'FET_Image' => __( 'Picture', 'event_espresso' ),
				'PER_lname' => __('Name', 'event_espresso'),
				'PER_address' => __('Address', 'event_espresso'),
				'PER_event_types' => __( 'Assigned As On Events', 'event_espresso' )
			);

		$this->_sortable_columns = array(
			'PER_ID' => array( 'PER_ID' => FALSE ),
			'PER_lname' => array( 'PER_lname' => TRUE ), //true means its already sorted
		);

		$this->_hidden_columns = array();
	}




	protected function _get_table_filters() {
		return array();
	}




	protected function _add_view_counts() {
		$orig_status = isset( $this->_req_data['status'] ) ? $this->_req_data['status'] : null;
		$this->_req_data['status'] = 'publish';
		$this->_views['publish']['count'] = EE_Registry::instance()->load_model('Person')->count( array( array( 'status' => 'publish' ) ) );
		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_peoples', 'eea-people-addon_delete_people' ) ) {
			$this->_views['trash']['count'] = EE_Registry::instance()->load_model('Person')->count_deleted();
		}
		$this->_req_data['status'] = 'draft';
		$this->_views['draft']['count'] = EE_Registry::instance()->load_model('Person')->count( array( array( 'status' => 'draft' ) ) );
		$this->_req_data['status'] = 'all';
		$this->_views['all']['count'] = EE_Registry::instance()->load_model('Person')->count( array( array( 'status' => array( 'NOT_IN', array( 'trash' ) ) ) ) );
		$this->_req_data['status'] = $orig_status;
	}



	function column_cb($item) {
		return sprintf( '<input type="checkbox" name="checkbox[%1$s]" value="%1$s" />', /* $1%s */ $item->ID() );
	}





	function column_PER_ID($item) {
		$content =  $item->ID();
		$content .= '<span class="show-on-mobile-view-only">' . ' ' . $item->feature_image( array( 36, 36 ) ) . ' ' . $item->full_name() . '</span>';
		return $content;
	}



	function column_FET_Image( $item ) {
		return $item->feature_image( array( 56, 56 ) );
	}


	function column_PER_lname($item) {

		//Build row actions
		$actions = array();
		// edit person link
		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_edit_people', 'eea-people-addon_edit_people', $item->ID() ) ) {
			$edit_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'edit', 'post'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
			$actions['edit'] = '<a href="'.$edit_lnk_url.'" title="' . __( 'Edit Person', 'event_espresso' ) . '">' . __( 'Edit', 'event_espresso' ) . '</a>';
		}

		if ( $this->_view == 'publish'  || $this->_view == 'all' || $this->_view == 'draft' ) {
			// trash person link
			if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_people', 'eea-people-addon_trash_people', $item->ID() ) ) {
				$trash_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'trash_person', 'PER_ID'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
				$actions['trash'] = '<a href="'.$trash_lnk_url.'" title="' . __( 'Move Person to Trash', 'event_espresso' ) . '">' . __( 'Trash', 'event_espresso' ) . '</a>';
			}
		} else {
			if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_people', 'eea-people-addon_restore_people', $item->ID()) ) {
				// restore person link
				$restore_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'restore_person', 'PER_ID'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
				$actions['restore'] = '<a href="'.$restore_lnk_url.'" title="' . __( 'Restore Person', 'event_espresso' ) . '">' . __( 'Restore', 'event_espresso' ) . '</a>';
				$delete_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'delete_person', 'PER_ID'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
				$actions['delete'] =  '<a href="'.$delete_lnk_url.'" title="' . __( 'Delete Permanently This Person.', 'event_espresso' ) . '">' . __( 'Delete Permanently', 'event_espresso' ) . '</a>';

			}
		}

		$edit_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'edit', 'post'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
		$name_link = EE_Registry::instance()->CAP->current_user_can( 'ee_edit_people', 'eea-people-addon_edit_people', $item->ID() ) ?  '<a href="'.$edit_lnk_url.'" title="' . __( 'Edit Person', 'event_espresso' ) . '">' . $item->full_name() . '</a>' : $item->full_name();
		$name_link .= '<br>' . '<a href="mailto:' . $item->email() .'">' . $item->email() . '</a>';
		$name_link .= $item->phone() ? '<br>' . sprintf( __( 'Phone: %s', 'event_espresso' ), $item->phone() ) : '';

		//Return the name contents
		return sprintf('%1$s %2$s', $name_link, $this->row_actions($actions) );
	}



	function column_PER_address($item) {
		EE_Registry::instance()->load_helper('Formatter');
		$content = EEH_Address::format( $item );
		return $content;
	}



	public function column_PER_event_types( $item ) {
		EE_Registry::instance()->load_helper('URL');
		//first do a query to get all the types for this user for where they are assigned to an event.
		$event_type_IDs = EEM_Person_Post::instance()->get_col(
			array(
				array(
					'PER_ID' => $item->ID(),
					'OBJ_type' => 'Event',
				),
			),
			'PT_ID'
		);

		$event_types = EEM_Term_Taxonomy::instance()->get_all(
			array(
				array(
					'term_taxonomy_id' => array( 'IN', $event_type_IDs )
				)
			)
		);

		//loop through the types and setup the pills and the links
		$content = '<ul class="person-to-cpt-people-type-list">';
		foreach ( $event_types as $type ) {
			$name = $type->get_first_related( 'Term' )->get('name');
			$count = EEM_Person_Post::instance()->count(
				array(
					array(
						'PER_ID' => $item->ID(),
						'OBJ_type' => 'Event',
						'PT_ID' => $type->ID()
					)
				)
			);
			$event_filter_link =  EEH_URL::add_query_args_and_nonce(
				array(
					'page' => 'espresso_events',
					'action' => 'default',
					'PER_ID' => $item->ID(),
					'PT_ID' => $type->ID()
				),
				admin_url( 'admin.php' )
			);
			$content .= '<li><a href="' . $event_filter_link . '">' . $name . '<span class="person-type-count">' . $count . '</span></a></li>';
		}
		$content .= '</ul>';
		echo $content;
	}

}
