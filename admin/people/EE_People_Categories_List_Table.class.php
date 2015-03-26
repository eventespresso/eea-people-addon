<?php
/**
 * This file contains the Admin list table class for people categories
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage admin
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * People Category List table class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	admin
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_People_Categories_List_Table extends EE_Admin_List_Table {


	protected function _setup_data() {
		$this->_data = $this->_admin_page->get_terms( 'espresso_people_categories', $this->_per_page, $this->_current_page);
		$this->_all_data_count = EEM_Term_Taxonomy::instance()->count( array( array( 'taxonomy' => 'espresso_people_categories' ) ) );
	}





	protected function _set_properties() {
		$this->_wp_list_args = array(
			'singular' => __('people category', 'event_espresso' ),
			'plural' => __('people categories', 'event_espresso' ),
			'ajax' => TRUE, //for now,
			'screen' => $this->_admin_page->get_current_screen()->id
			);

		$this->_columns = array(
			'cb' => '<input type="checkbox" />',
			'id' => __('ID', 'event_espresso'),
			'name' => __('Name', 'event_espresso'),
			//'shortcode' => __('Shortcode', 'event_espresso'),
			'count' => __('People', 'event_espresso')
			);

		$this->_sortable_columns = array(
			'id' => array( 'Term.term_id' => true ),
			'name' => array( 'Term.slug' => false ),
			'count' => array( 'term_count' => false )
			);

		$this->_hidden_columns = array();
	}






	//not needed
	protected function _get_table_filters() {
		return array();
	}





	protected function _add_view_counts() {
		$this->_views['all']['count'] = $this->_all_data_count;
	}






	public function column_cb($item) {
		return sprintf( '<input type="checkbox" name="PER_CAT_ID[]" value="%s" />', $item->get('term_id') );
	}





	public function column_id($item) {
		return $item->get('term_id');
	}






	public function column_name($item) {
		$edit_query_args = array(
			'action' => 'edit_category',
			'PER_CAT_ID' => $item->get('term_id')
		);

		$delete_query_args = array(
			'action' => 'delete_category',
			'PER_CAT_ID' => $item->get('term_id')
		);

		$edit_link = EE_Admin_Page::add_query_args_and_nonce( $edit_query_args, EEA_PEOPLE_ADDON_ADMIN_URL );
		$delete_link = EE_Admin_Page::add_query_args_and_nonce( $delete_query_args, EEA_PEOPLE_ADDON_ADMIN_URL );
		$view_link = get_term_link( $item->get('term_id'), 'espresso_people_categories' );

		$actions = array(
			'edit' => '<a href="' . $edit_link . '" title="' . __('Edit Category', 'event_espresso') . '">' . __('Edit', 'event_espresso') . '</a>',
			'view' => '<a href="' . $view_link . '" title="' . esc_attr__('View Category Archive', 'event_espresso') . '">' . __('View', 'event_espresso') . '</a>'
			);


		$actions['delete'] = '<a href="' . $delete_link . '" title="' . __('Delete Category', 'event_espresso') . '">' . __('Delete', 'event_espresso') . '</a>';

		$content = '<strong><a class="row-title" href="' . $edit_link . '">' . $item->get_first_related('Term')->get('name') . '</a></strong>';
		$content .= $this->row_actions($actions);
		return $content;
	}




	public function column_shortcode($item) {
		$content = '[ESPRESSO_PEOPLE category_slug="' . $item->get_first_related('Term')->get('slug') . '"]';
		return $content;
	}




	public function column_count( $item ) {
		$e_args = array(
			'action' => 'default',
			'category' => $item->get_first_related('Term')->get('slug')
			);
		$e_link = EE_Admin_Page::add_query_args_and_nonce( $e_args, EEA_PEOPLE_ADDON_ADMIN_URL );
		$content = '<a href="' . $e_link . '">' . $item->get('term_count') . '</a>';
		return $content;
	}
}
