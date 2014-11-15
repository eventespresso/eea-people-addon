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
				'PER_fname' => __('First Name', 'event_espresso'),
				'PER_lname' => __('Last Name', 'event_espresso'),
				'PER_email' => __('Email Address', 'event_espresso'),
				'PER_phone' => __('Phone', 'event_espresso'),
				'PER_address' => __('Address', 'event_espresso'),
				'PER_city' => __('City', 'event_espresso'),
				'STA_ID' => __('State/Province', 'event_espresso'),
				'CNT_ISO' => __('Country', 'event_espresso'),
			);

		$this->_sortable_columns = array(
			'PER_ID' => array( 'PER_ID' => FALSE ),
			'PER_lname' => array( 'PER_lname' => TRUE ), //true means its already sorted
			'PER_fname' => array( 'PER_fname' => FALSE ),
			'PER_email' => array( 'PER_email' => FALSE ),
			'PER_city' => array( 'PER_city' => FALSE ),
			'STA_ID' => array( 'STA_ID' => FALSE ),
			'CNT_ISO' => array( 'CNT_ISO' => FALSE )
		);

		$this->_hidden_columns = array();
	}




	protected function _get_table_filters() {
		return array();
	}




	protected function _add_view_counts() {
		$orig_status = isset( $this->_req_data['status'] ) ? $this->_req_data['status'] : null;
		$this->_req_data['status'] = 'published';
		$this->_views['published']['count'] = EE_Registry::instance()->load_model('Person')->count();
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
		return '<div>' . $item->ID() . '</div>';
	}





	function column_PER_lname($item) {

		// edit attendee link
		$edit_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'edit_person', 'post'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
		$name_link = EE_Registry::instance()->CAP->current_user_can( 'ee_edit_people', 'eea-people-addon_edit_people' ) ?  '<a href="'.$edit_lnk_url.'" title="' . __( 'Edit Person', 'event_espresso' ) . '">' . $item->lname() . '</a>' : $item->lname();
		return $name_link;

	}




	function column_PER_fname($item) {

		//Build row actions
		$actions = array();
		// edit person link
		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_edit_people', 'eea-people-addon_edit_people' ) ) {
			$edit_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'edit_person', 'post'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
			$actions['edit'] = '<a href="'.$edit_lnk_url.'" title="' . __( 'Edit Person', 'event_espresso' ) . '">' . __( 'Edit', 'event_espresso' ) . '</a>';
		}

		if ( $this->_view == 'published'  || $this->_view == 'all' || $this->_view == 'draft' ) {
			// trash person link
			if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_people', 'eea-people-addon_trash_people' ) ) {
				$trash_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'trash_person', 'PER_ID'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
				$actions['trash'] = '<a href="'.$trash_lnk_url.'" title="' . __( 'Move Person to Trash', 'event_espresso' ) . '">' . __( 'Trash', 'event_espresso' ) . '</a>';
			}
		} else {
			if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_people', 'eea-people-addon_restore_people' ) ) {
				// restore person link
				$restore_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'restore_person', 'PER_ID'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
				$actions['restore'] = '<a href="'.$restore_lnk_url.'" title="' . __( 'Restore Person', 'event_espresso' ) . '">' . __( 'Restore', 'event_espresso' ) . '</a>';
			}
		}

		$edit_lnk_url = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'edit_person', 'post'=>$item->ID() ), EEA_PEOPLE_ADDON_ADMIN_URL );
		$name_link = EE_Registry::instance()->CAP->current_user_can( 'ee_edit_people', 'eea-people-addon_edit_people' ) ?  '<a href="'.$edit_lnk_url.'" title="' . __( 'Edit Person', 'event_espresso' ) . '">' . $item->fname() . '</a>' : $item->fname();

		//Return the name contents
		return sprintf('%1$s %2$s', $name_link, $this->row_actions($actions) );
	}





	function column_PER_email($item) {
		return '<a href="mailto:' . $item->email() . '">' . $item->email() . '</a>';
	}




	function column_PER_address($item) {
		return $item->address();
	}



	function column_PER_city($item) {
		return $item->city();
	}



	function column_STA_ID($item) {
		$states = EEM_State::instance()->get_all_states();
		$state = isset( $states[ $item->state_ID() ] ) ? $states[ $item->state_ID() ]->get( 'STA_name' ) : $item->state_ID();
		return ! is_numeric( $state ) ? $state : '';
	}



	function column_CNT_ISO($item) {
		$countries = EEM_Country::instance()->get_all_countries();
		//printr( $countries, '$countries  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
		$country = isset( $countries[ $item->country_ID() ] ) ? $countries[ $item->country_ID() ]->get( 'CNT_name' ) : $item->country_ID();
		return ! is_numeric( $country ) ? $country : '';
	}



	function column_PER_phone($item) {
		return $item->phone();
	}


}
