<?php
/**
 * This file contains the Admin Page for People
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage admin
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * People Admin Page class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	admin
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class People_Admin_Page extends EE_Admin_Page_CPT {

	/**
	 * Used to cache a WP_Term object
	 * Will either be a espresso_people_category term OR a espresso_people_type term.
	 *
	 * @var WP_Term
	 */
	protected $_term_object;


	protected function _init_page_props() {
		$this->page_slug = EEA_PEOPLE_PG_SLUG;
		$this->page_label = EEA_PEOPLE_LABEL;
		$this->_admin_base_url = EEA_PEOPLE_ADDON_ADMIN_URL;
		$this->_admin_base_path = EEA_PEOPLE_ADDON_ADMIN;
		$this->page_label = __('Manage People', 'event_espresso' );
		$this->_cpt_routes = array(
			'create_new' => 'espresso_people',
			'edit' => 'espresso_people',
			'insert_person' => 'espresso_people',
			'update_person' => 'espresso_people'
			);
		$this->_cpt_model_names = array(
			'create_new' => 'EEM_Person',
			'edit' => 'EEM_Person'
			);
		$this->_cpt_edit_routes = array(
			'espresso_people' => 'edit'
			);
		add_action( 'edit_form_after_title', array( $this, 'after_title_form_fields'), 10 );
		add_filter( 'FHEE__EE_Admin_Page_CPT___edit_cpt_item__create_new_action', array( $this, 'map_cpt_route'), 10, 2 );
	}



	public function map_cpt_route( $route, EE_Admin_Page $adminpage ) {
		if ( $adminpage->page_slug == $this->page_slug && $route == 'create_new' ) {
			return 'create_new';
		}
		return $route;
	}


	/**
	 * add in the form fields for the person first name/last name edit
	 * @param  WP_Post $post wp post object
	 * @return string        html for new form.
	 */
	public function after_title_form_fields($post) {
		if ( $post->post_type == 'espresso_people' ) {
			$template = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'people_details_after_title_form_fields.template.php';
			$template_args['people'] = $this->_cpt_model_obj;
			EEH_Template::display_template($template, $template_args);
		}
	}




	protected function _ajax_hooks() {}





	protected function _define_page_props() {
		$this->_admin_page_title = $this->page_label;
		$this->_labels = array(
			'buttons' => array(
				'add-person' => __( 'Add Person', 'event_espresso' ),
				'edit' => __( 'Add Person', 'event_espresso' ),
				'add_category' => __('Add New Category', 'event_espresso'),
				'edit_category' => __('Edit Category', 'event_espresso'),
				'delete_category' => __('Delete_Category', 'event_espresso'),
				'add_type' => __('Add New Type', 'event_espresso'),
				'edit_type' => __('Edit Type', 'event_espresso'),
				'delete_type' => __('Delete Type', 'event_espresso')
				),
			'editor_title' => array(
				'espresso_people' => __('Enter Full Name here', 'event_espresso')
				),
			'publishbox' => array(
				'edit' => __( 'Update Person Record', 'event_espresso' ),
				'create_new' => __( 'Save Person Record', 'event_espresso' ),
				'add_category' => __('Save New Category', 'event_espresso'),
				'edit_category' => __('Update Category', 'event_espresso'),
				'add_type' => __('Save New Type', 'event_espresso'),
				'edit_type' => __('Update Type', 'event_espresso')
				)
		);

	}




	protected function _set_page_routes() {
		$ppl_id = ! empty( $this->_req_data['PER_ID'] ) && ! is_array( $this->_req_data['PER_ID'] ) ? $this->_req_data['PER_ID'] : 0;
		$ppl_id = !empty( $this->_req_data['post'] ) && ! is_array( $this->_req_data['post'] ) ? $this->_req_data['post'] : $ppl_id;
		$this->_page_routes = array(
			'default' => array(
				'func' => '_people_list_table',
				'capability' => 'ee_read_peoples'
				),
			'create_new' => array(
				'func' => '_create_new_cpt_item',
				'args' => array( 'new_person' => true ),
				'capability' => 'ee_edit_peoples'
			),
			'edit' => array(
				'func' => '_edit_cpt_item',
				'capability' => 'ee_edit_people',
				'obj_id' => $ppl_id
			),

			'trash_person' => array(
				'func' => '_trash_or_restore_people',
				'args' => array( 'trash' => true ),
				'noheader' => true,
				'capability' => 'ee_delete_people',
				'obj_id' => $ppl_id
				),

			'trash_people'	=> array(
				'func' => '_trash_or_restore_people',
				'args' => array(
					'trash' => TRUE
				),
				'noheader' => TRUE,
				'capability' => 'ee_delete_peoples'
			),

			'restore_person' => array(
				'func' => '_trash_or_restore_people',
				'args' => array( 'trash' => false ),
				'noheader' => true,
				'capability' => 'ee_delete_people',
				'obj_id' => $ppl_id
				),

			'restore_people' => array(
				'func' => '_trash_or_restore_people',
				'args' => array(
					'trash' => FALSE
				),
				'noheader' => TRUE,
				'capability' => 'ee_delete_peoples'
			),

			//delete permanently
			'delete_person' => array(
				'func' => '_delete_permanently_people',
				'noheader' => true,
				'capability' => 'ee_delete_people',
				'obj_id' => $ppl_id
				),
			'delete_people' => array(
				'func' => '_delete_permanently_people',
				'noheader' => true,
				'capability' => 'ee_delete_peoples',
				),

			//categories
			'add_category' => array(
				'func' => '_category_details',
				'capability' => 'ee_edit_people_category',
				'args' => array('add'),
				),
			'edit_category' => array(
				'func' => '_category_details',
				'capability' => 'ee_edit_people_category',
				'args' => array('edit')
				),
			'delete_categories' => array(
				'func' => '_delete_categories',
				'capability' => 'ee_delete_people_category',
				'noheader' => TRUE
				),

			'delete_category' => array(
				'func' => '_delete_categories',
				'capability' => 'ee_delete_people_category',
				'noheader' => TRUE
				),

			'insert_category' => array(
				'func' => '_insert_or_update_category',
				'args' => array('new_category' => TRUE),
				'capability' => 'ee_edit_people_category',
				'noheader' => TRUE
				),

			'update_category' => array(
				'func' => '_insert_or_update_category',
				'args' => array('new_category' => FALSE),
				'capability' => 'ee_edit_people_category',
				'noheader' => TRUE
				),
			'category_list' => array(
				'func' => '_category_list_table',
				'capability' => 'ee_manage_people_categories'
				),

			//types
			'add_type' => array(
				'func' => '_type_details',
				'capability' => 'ee_edit_people_type',
				'args' => array('add'),
				),
			'edit_type' => array(
				'func' => '_type_details',
				'capability' => 'ee_edit_people_type',
				'args' => array('edit')
				),
			'delete_types' => array(
				'func' => '_delete_types',
				'capability' => 'ee_delete_people_type',
				'noheader' => TRUE
				),

			'delete_type' => array(
				'func' => '_delete_types',
				'capability' => 'ee_delete_people_type',
				'noheader' => TRUE
				),

			'insert_type' => array(
				'func' => '_insert_or_update_type',
				'args' => array('new_type' => TRUE),
				'capability' => 'ee_edit_people_type',
				'noheader' => TRUE
				),

			'update_type' => array(
				'func' => '_insert_or_update_type',
				'args' => array('new_type' => FALSE),
				'capability' => 'ee_edit_people_type',
				'noheader' => TRUE
				),
			'type_list' => array(
				'func' => '_type_list_table',
				'capability' => 'ee_manage_people_types'
				)
		);
	}





	protected function _set_page_config() {

		$this->_page_config = array(
			'default' => array(
				'nav' => array(
					'label' => __('People List', 'event_espresso'),
					'order' => 5
					),
				'list_table' => 'EE_People_List_Table',
				'metaboxes' => array(),
				'require_nonce' => false
			),
			'create_new' => array(
				'nav' => array(
					'label' => __('Add Person', 'event_espresso'),
					'order' => 10,
					'persistent' => false
					),
				'metaboxes' => array( '_publish_post_box', 'people_editor_metaboxes' ),
				'require_nonce' => false
			),
			'edit' => array(
				'nav' => array(
					'label' => __('Edit Person', 'event_espresso'),
					'order' => 10,
					'persistent' => false,
					'url' => isset($this->_req_data['post']) ? add_query_arg(array('post' => $this->_req_data['post'] ), $this->_current_page_view_url )  : $this->_admin_base_url
					),
				'metaboxes' => array('people_editor_metaboxes', '_publish_post_box'),
				'require_nonce' => false
				),

			//people category stuff
			'add_category' => array(
				'nav' => array(
					'label' => __('Add Category', 'event_espresso'),
					'order' => 10,
					'persistent' => false,
					),
				'help_tabs' => array(
					'add_category_help_tab' => array(
						'title' => __( 'Add New People Category', 'event_espresso' ),
						'filename' => 'people_add_category'
					)
				),
				'metaboxes' => array( '_publish_post_box' )
				),
			'edit_category' => array(
				'nav' => array(
					'label' => __('Edit Category', 'event_espresso'),
					'order' => 10,
					'persistent' => FALSE,
					'url' => isset($this->_req_data['PER_CAT_ID']) ? add_query_arg(array('PER_CAT_ID' => $this->_req_data['PER_CAT_ID'] ), $this->_current_page_view_url )  : $this->_admin_base_url
					),
				'help_tabs' => array(
					'edit_category_help_tab' => array(
						'title' => __('Edit People Category', 'event_espresso'),
						'filename' => 'people_edit_category'
					)
				),
				'metaboxes' => array( '_publish_post_box' )
				),
			'category_list' => array(
				'nav' => array(
					'label' => __('Categories', 'event_espresso'),
					'order' => 15
					),
				'list_table' => 'EE_People_Categories_List_Table',
				'metaboxes' => array('_espresso_news_post_box'),
				'require_nonce' => FALSE
				),

			//people type stuff
			'add_type' => array(
				'nav' => array(
					'label' => __('Add Type', 'event_espresso'),
					'order' => 10,
					'persistent' => false
					),
				'help_tabs' => array(
					'add_people_type_help_tab' => array(
						'title' => __( 'Add People Type', 'event_espresso' ),
						'filename' => 'people_add_type'
					)
				),
				'metaboxes' => array( '_publish_post_box' )
				),
			'edit_type' => array(
				'nav' => array(
					'label' => __('Edit Type', 'event_espresso'),
					'order' => 10,
					'persistent' => FALSE,
					'url' => isset($this->_req_data['PER_TYPE_ID']) ? add_query_arg(array('PER_TYPE_ID' => $this->_req_data['PER_TYPE_ID'] ), $this->_current_page_view_url )  : $this->_admin_base_url
					),
				'help_tabs' => array(
					'edit_people_type_help_tab' => array(
						'title' => __( 'Edit People Type', 'event_espresso' ),
						'filename' => 'people_edit_type'
					)
				),
				'metaboxes' => array( '_publish_post_box' )
				),
			'type_list' => array(
				'nav' => array(
					'label' => __('Types', 'event_espresso'),
					'order' => 15
					),
				'list_table' => 'EE_People_Types_List_Table',
				'metaboxes' => array('_espresso_news_post_box'),
				'require_nonce' => FALSE
				)
		);
	}


	protected function _add_screen_options() {}
	protected function _add_screen_options_default() {
		$this->_per_page_screen_option();
	}

	protected function _add_feature_pointers() {}
	public function load_scripts_styles() {}

	public function load_scripts_styles_create_new() {
		$this->load_scripts_styles_edit();
	}

	public function load_scripts_styles_edit() {
		wp_register_style( 'eea-person-admin-css', EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-person-admin.css', array('ee-admin-css'), EEA_PEOPLE_ADDON_VERSION );
		wp_register_script( 'eea-person-admin-js', EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-person-admin.js', array( 'post' ), EEA_PEOPLE_ADDON_VERSION );
		wp_enqueue_style( 'eea-person-admin-css' );
		wp_enqueue_script( 'eea-person-admin-js' );
	}

	public function load_scripts_styles_default() {
		wp_register_style( 'eea-person-admin-list-table-css', EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-person-admin-list-table.css', array( 'ee-admin-css' ), EEA_PEOPLE_ADDON_VERSION );
		wp_enqueue_style( 'eea-person-admin-list-table-css' );
	}

	public function admin_init() {}
	public function admin_notices() {}
	public function admin_footer_scripts() {}


	protected function _set_list_table_views_default() {
		$this->_views = array(
			'all' => array(
				'slug' => 'all',
				'label' => __('All', 'event_espresso'),
				'count' => 0,
				'bulk_action' => array()
				),
			'publish' => array(
				'slug' => 'publish',
				'label' => __('Published', 'event_espresso'),
				'count' => 0,
				'bulk_action' => array()
				),
			'draft' => array(
				'slug' => 'draft',
				'label' => __('Draft', 'event_espresso'),
				'count' => 0,
				'bulk_action' => array()
				)
			);

		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_peoples', 'eea-people-addon_trash_people' ) ) {
			$this->_views['trash'] = array(
				'slug' => 'trash',
				'label' => __('Trash', 'event_espresso'),
				'count' => 0,
				'bulk_action' => array(
					'restore_people' => __( 'Restore from Trash', 'event_espresso' ),
					'delete_people' => __('Delete Permanently', 'event_espresso' )
					)
				);
			$this->_views['all']['bulk_action'] = array(
					'trash_people' => __( 'Move to Trash', 'event_espresso' )
				);
			$this->_views['publish']['bulk_action'] = array(
					'trash_people' => __( 'Move to Trash', 'event_espresso' )
				);
			$this->_views['draft']['bulk_action'] = array(
					'trash_people' => __( 'Move to Trash', 'event_espresso' )
				);
		}
	}



	protected function _set_list_table_views_category_list() {
		$this->_views = array(
			'all' => array(
				'slug' => 'all',
				'label' => __('All', 'event_espresso'),
				'count' => 0,
				'bulk_action' => array()
				)
		);

		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_people_category', 'eea-people-addon-delete_category' ) ) {
			$this->_views['all']['bulk_action'] = array( 'delete_categories' => __('Delete Permanently', 'event_espresso') );
		}
	}



	protected function _set_list_table_views_type_list() {
		$this->_views = array(
			'all' => array(
				'slug' => 'all',
				'label' => __('All', 'event_espresso'),
				'count' => 0,
				'bulk_action' => array()
				)
		);

		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_people_type', 'eea-people-addon-delete_type' ) ) {
			$this->_views['all']['bulk_action'] = array( 'delete_types' => __('Delete Permanently', 'event_espresso') );
		}
	}


	/**
	 * Set up page for people list table
	 *
	 * @return string
	 */
	protected function _people_list_table() {
		$this->_search_btn_label = __('People', 'event_espresso');
		$this->_admin_page_title .= ' ' . $this->get_action_link_or_button(
		    'create_new',
            'add-person',
            array(),
            'add-new-h2'
        );
		if ( ! empty( $this->_req_data['EVT_ID'] ) ) {
			$event = EEM_Event::instance()->get_one_by_ID( $this->_req_data['EVT_ID'] );
			if ( $event instanceof EE_Event ) {
				$this->_template_args['before_list_table'] = '<h2>' . sprintf( __( 'Showing people assigned to the event: %s', 'event_espresso' ), $event->name() ) . '</h2>';
			}
		}
		$this->_template_args['after_list_table'] = EEH_Template::get_button_or_link( get_post_type_archive_link('espresso_people'), __("View People Archive Page", "event_espresso"), 'button' );
		$this->display_admin_list_table_page_with_no_sidebar();
	}



	/**
	 * get people.
	 *
	 * @param int     $per_page number of people per page
	 * @param int     $count    whether to return count or data.
	 * @param bool   $trash    whether to just return trashed or not.
	 *
	 * @return EE_People[]
	 */
	public function get_people( $per_page, $count = false, $trash = false ) {
		$people = array();
		$PPLM = EE_Registry::instance()->load_model( 'Person' );

		$orderby = ! empty( $this->_req_data['orderby'] ) ? $this->_req_data['orderby'] : '';
		$orderby = empty( $orderby ) ? 'PER_lname' : $orderby;

		$sort = ! empty( $this->_req_data['order'] ) ? $this->_req_data['order'] : 'ASC';

		$current_page = ! empty( $this->_req_data['paged'] ) ? $this->_req_data['paged'] : 1;
		$per_page = ! empty( $per_page ) ? $per_page : 10;
		$per_page = ! empty( $this->_req_data['perpage'] ) ? $this->_req_data['perpage'] : $per_page;

		$_where = array();

		$status = isset( $this->_req_data['status'] ) ? $this->_req_data['status'] : NULL;

		//determine what post status our condition will have for the query.
		$status = isset( $this->_req_data['status'] ) ? $this->_req_data['status'] : NULL;
		//determine what post_status our condition will have for the query.
		switch ( $status ) {
			case NULL :
			case 'all' :
				break;

			case 'draft' :
				$_where['status'] = array( 'IN', array('draft', 'auto-draft') );
				break;

			default :
				$_where['status'] = $status;
		}

		//possible conditions for capability checks
		if ( ! EE_Registry::instance()->CAP->current_user_can( 'ee_read_private_peoples', 'get_people') ) {
			$_where['status**'] = array( '!=', 'private' );
		}

		if ( ! EE_Registry::instance()->CAP->current_user_can( 'ee_read_others_peoples', 'get_people' ) ) {
			$_where['PER_wp_user'] =  get_current_user_id();
		}

		if ( ! empty( $this->_req_data['EVT_ID'] ) ) {
			$_where['Person_Post.OBJ_ID'] = $this->_req_data['EVT_ID'];
		}

		if ( ! empty( $this->_req_data['s'] ) ) {
			$sstr = '%' . $this->_req_data['s'] . '%';
			$_where['OR'] = array(
				'Event.EVT_name' => array( 'LIKE', $sstr),
				'Event.EVT_desc' => array( 'LIKE', $sstr ),
				'Event.EVT_short_desc' => array( 'LIKE' , $sstr ),
				'PER_fname' => array( 'LIKE', $sstr ),
				'PER_lname' => array( 'LIKE', $sstr ),
				'PER_short_bio' => array( 'LIKE', $sstr ),
				'PER_email' => array('LIKE', $sstr ),
				'PER_address' => array( 'LIKE', $sstr ),
				'PER_address2' => array( 'LIKE', $sstr ),
				'PER_city' => array( 'LIKE', $sstr ),
				'Country.CNT_name' => array( 'LIKE', $sstr ),
				'State.STA_name' => array('LIKE', $sstr ),
				'PER_phone' => array( 'LIKE', $sstr )
				);
		}

		$offset = ($current_page-1)*$per_page;
		$limit = $count ? NULL : array( $offset, $per_page );

		if ( $trash )
			$people = $count ? $PPLM->count_deleted( array($_where,'order_by'=>array($orderby=>$sort), 'limit'=>$limit)): $PPLM->get_all_deleted( array($_where,'order_by'=>array($orderby=>$sort), 'limit'=>$limit));
		else
			$people = $count ? $PPLM->count( array($_where, 'order_by'=>array($orderby=>$sort),'limit'=>$limit)) : $PPLM->get_all( array($_where, 'order_by'=>array($orderby=>$sort), 'limit'=>$limit ) );

		return $people;
	}


	/**
	 * Callback for cpt route insert/updates.  Runs on the "save_post" hook.
	 *
	 * @since  1.0.0
	 *
	 * @param int      $post_id Post id of item
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	protected function _insert_update_cpt_item( $post_id, $post ) {
		$success = true;
		$person = EEM_Person::instance()->get_one_by_ID( $post_id );

		//for people updates
		if ( $post->post_type != 'espresso_people' || ! $person instanceof EE_Person ) {
			return;
		}

		$updated_fields = array(
			'PER_fname' => $this->_req_data['PER_fname'],
			'PER_lname' => $this->_req_data['PER_lname'],
			'PER_full_name'=> ! empty( $this->_req_data['post_title'] ) ? $this->_req_data['post_title'] : $this->_req_data['PER_fname'] . ' ' . $this->_req_data['PER_lname'],
			'PER_address' => isset($this->_req_data['PER_address']) ? $this->_req_data['PER_address'] : '',
			'PER_address2' => isset($this->_req_data['PER_address2']) ? $this->_req_data['PER_address2'] : '',
			'PER_city' => isset( $this->_req_data['PER_city'] ) ? $this->_req_data['PER_city'] : '',
			'STA_ID' => isset( $this->_req_data['STA_ID'] ) ? $this->_req_data['STA_ID'] : '',
			'CNT_ISO' => isset( $this->_req_data['CNT_ISO'] ) ? $this->_req_data['CNT_ISO'] : '',
			'PER_zip' => isset( $this->_req_data['PER_zip'] ) ? $this->_req_data['PER_zip'] : '',
			'PER_email' => isset( $this->_req_data['PER_email'] ) ? $this->_req_data['PER_email'] : '',
			'PER_phone' => isset( $this->_req_data['PER_phone'] ) ? $this->_req_data['PER_phone'] : ''
			);

		foreach ( $updated_fields as $field => $value ) {
				$person->set($field, $value);
			}

		$success = $person->save();

		$people_update_callbacks = apply_filters( 'FHEE__People_Admin_Page__insert_update_cpt_item__people_update', array() );
		foreach ( $people_update_callbacks as $a_callback ) {
			if ( FALSE === call_user_func_array( $a_callback, array($person, $this->_req_data ) ) ) {
				throw new EE_Error( sprintf( __('The %s callback given for the "FHEE__People_Admin_Page__insert_update_cpt_item__people_update" filter is not a valid callback.  Please check the spelling.', 'event_espresso'), $a_callback ) );
			}
		}

		if ( $success === FALSE ) {
			EE_Error::add_error(__('Something went wrong with updating the meta table data for the person.', 'event_espresso'), __FILE__, __FUNCTION__, __LINE__ );
		}
	}


	//unused cpt route callbacks
	public function trash_cpt_item($post_id) {}
	public function delete_cpt_item($post_id) {}
	public function restore_cpt_item($post_id) {}
	protected function _restore_cpt_item($post_id, $revision_id) {}




	/**
	 * People Editor metabox registration
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function people_editor_metaboxes() {
		$this->verify_cpt_object();

		remove_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', $this->_cpt_routes[$this->_req_action], 'normal', 'core');
		remove_meta_box('commentstatusdiv', $this->_cpt_routes[$this->_req_action], 'normal', 'core');

		if ( post_type_supports( 'espresso_people', 'excerpt') ) {
			add_meta_box('postexcerpt', __('Short Biography', 'event_espresso'), 'post_excerpt_meta_box', $this->_cpt_routes[$this->_req_action], 'normal' );
		}

		if ( post_type_supports( 'espresso_people', 'comments') ) {
			add_meta_box('commentsdiv', __('Notes on the Person', 'event_espresso'), 'post_comment_meta_box', $this->_cpt_routes[$this->_req_action], 'normal', 'core');
		}

		add_meta_box('person_contact_info', __('Contact Info', 'event_espresso'), array( $this, 'person_contact_info'), $this->_cpt_routes[$this->_req_action], 'side', 'core' );
		add_meta_box('person_details_address', __('Address Details', 'event_espresso'), array($this, 'person_address_details'), $this->_cpt_routes[$this->_req_action], 'normal', 'core' );

		//add event editor relationship
		add_meta_box('person_to_cpt_relationship', __('Where is this person assigned?', 'event_espresso'), array( $this, 'person_to_cpt_details' ), $this->_cpt_routes[$this->_req_action], 'normal', 'core' );
	}



	/**
	 * Metabox for person contact info
	 * @param  WP_Post $post wp post object
	 * @return string        person contact info ( and form )
	 */
	public function person_contact_info( $post ) {
		//get attendee object ( should already have it )
		$this->_template_args['person'] = $this->_cpt_model_obj;
		$template = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'person_contact_info_metabox_content.template.php';
		EEH_Template::display_template($template, $this->_template_args);
	}



	/**
	 * Metabox for person details
	 * @param  WP_Post $post wp post object
	 * @return string        person address details (and form)
	 */
	public function person_address_details($post) {
		//get people object (should already have it)
		$this->_template_args['person'] = $this->_cpt_model_obj;
		$this->_template_args['state_html'] = EEH_Form_Fields::generate_form_input(
				new EE_Question_Form_Input(
				EE_Question::new_instance( array(
					'QST_ID' => 0,
					'QST_display_text' => __('State/Province', 'event_espresso'),
					'QST_system' => 'admin-state'
					)),
				EE_Answer::new_instance( array(
					'ANS_ID' => 0,
					'ANS_value' => $this->_cpt_model_obj->state_ID()
					)),
				array(
					'input_id' => 'STA_ID',
					'input_name' => 'STA_ID',
					'input_prefix' => '',
					'append_qstn_id' => FALSE
					)
			));
		$this->_template_args['country_html'] = EEH_Form_Fields::generate_form_input(
				new EE_Question_Form_Input(
				EE_Question::new_instance( array(
					'QST_ID' => 0,
					'QST_display_text' => __('Country', 'event_espresso'),
					'QST_system' => 'admin-country'
					)),
				EE_Answer::new_instance( array(
					'ANS_ID' => 0,
					'ANS_value' => $this->_cpt_model_obj->country_ID()
					)),
				array(
					'input_id' => 'CNT_ISO',
					'input_name' => 'CNT_ISO',
					'input_prefix' => '',
					'append_qstn_id' => FALSE
					)
				));
		$template = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'people_address_details_metabox_content.template.php';
		EEH_Template::display_template($template, $this->_template_args );

	}



	/**
	 * Displays al lthe person relationships for this user.
	 *
	 * @since 1.0.0
	 * @todo   Add paging.
	 * @todo   Add filters.
	 *
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function person_to_cpt_details( $post ) {
		//get all relationships for the given person
		$person_relationships = EEM_Person_Post::instance()->get_all( array( array( 'PER_ID' => $post->ID ) ) );

		//let's setup the row data for the rows.
		$row_data = array();
		foreach ( $person_relationships as $person_relationship ) {
			$cpt_obj = EE_Registry::instance()->load_model( $person_relationship->get('OBJ_type') )->get_one_by_ID( $person_relationship->get( 'OBJ_ID' ) );
			if ( $cpt_obj instanceof EE_Base_Class ) {
				if ( ! isset( $row_data[$cpt_obj->ID()] ) ) {
					switch( get_class( $cpt_obj ) ) {
						case 'EE_Event' :
							$css_class = 'dashicons dashicons-calendar-alt';
							$edit_link = add_query_arg( array(
								'page' => 'espresso_events',
								'action' => 'edit',
								'post' => $cpt_obj->ID()
							), admin_url( 'admin.php' ) );
							break;
						case 'EE_Venue' :
							$css_class = 'ee-icon ee-icon-venue';
							$edit_link = add_query_arg( array(
								'page' => 'espresso_venues',
								'action' => 'edit',
								'post' => $cpt_obj->ID()
							), admin_url( 'admin.php' ) );
							break;
						case 'EE_Attendee' :
							$css_class = 'dashicons dashicons-admin-users';
							$edit_link = add_query_arg( array(
								'page' => 'espresso_registrations',
								'action' => 'edit_attendee',
								'post' => $cpt_obj->ID()
							), admin_url( 'admin.php' ) );
							break;
						default :
							$css_class = '';
							break;
					}
					$row_data[$cpt_obj->ID()] = array(
						'css_class' => $css_class,
						'cpt_type' => strtolower( $person_relationship->get('OBJ_type') ),
						'cpt_obj' => $cpt_obj,
						'edit_link' => $edit_link,
						'ct_obj' => array( EEM_Term_Taxonomy::instance()->get_one_by_ID( $person_relationship->get('PT_ID' ) ) )
					);
				} else {
					//add other person types.
					$row_data[$cpt_obj->ID()]['ct_obj'][] = EEM_Term_Taxonomy::instance()->get_one_by_ID( $person_relationship->get('PT_ID' ) );
				}
			}
		}

		//now we have row data so we can send that to the template
		$template_args = array( 'row_data' => $row_data );
		$template = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'person_to_cpt_details_metabox_content.template.php';
		EEH_Template::display_template( $template, $template_args );
	}




	/**
	 * Trashing or restoring people.
	 *
	 * @since  1.0.0
	 *
	 * @param bool $trash true if trashing otherwise restoring
	 *
	 * @return void
	 */
	protected function _trash_or_restore_people( $trash = true ) {
		$PERM = EE_Registry::instance()->load_model( 'Person' );
		$success = 1;

		//Checkboxes
		if (!empty($this->_req_data['checkbox']) && is_array($this->_req_data['checkbox'])) {
			// if array has more than one element than success message should be plural
			$success = count( $this->_req_data['checkbox'] ) > 1 ? 2 : 1;
			// cycle thru checkboxes
			while (list( $PER_ID, $value ) = each($this->_req_data['checkbox'])) {
				$updated = $trash ? $PERM->delete_by_ID($PER_ID) : $PERM->restore_by_ID($PER_ID);
				if ( !$updated ) {
					$success = 0;
				}
			}

		} else {
			// grab single id and delete
			$PER_ID = absint($this->_req_data['PER_ID']);
			//get person
			$person = $PERM->get_one_by_ID( $PER_ID );
			$updated = $trash ? $person->delete() : $person->restore();
			$updated = $person->save();
			if ( ! $updated ) {
				$success = 0;
			}

		}

		$what = $success > 1 ? __( 'People', 'event_espresso' ) : __( 'Person', 'event_espresso' );
		$action_desc = $trash ? __( 'moved to the trash', 'event_espresso' ) : __( 'restored', 'event_espresso' );
		$this->_redirect_after_action( $success, $what, $action_desc, array( 'action' => 'default' ) );
	}



	/**
	 * Delete's permanently people (or person).
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function _delete_permanently_people() {
		$PERM = EE_Registry::instance()->load_model( 'Person' );
		$PPST = EE_Registry::instance()->load_Model( 'Person_Post' );
		$total_deleted = 0;
		$total_not_deleted = 0;

		if ( !empty( $this->_req_data['checkbox'] ) && is_array( $this->_req_data['checkbox'] ) ) {
			$count = count( $this->_req_data['checkbox'] );
			while ( list( $PER_ID, $value ) = each( $this->_req_data['checkbox'] ) ) {
				//first delete any relationships with other posts for this id.
				$PPST->delete( array( array( 'PER_ID' => $PER_ID ) ) );

				//delete any term_taxonomy_relationships (gonna use wp core functions cause it's likely a bit faster)
				wp_delete_object_term_relationships( $PER_ID, array( 'espresso_people_type', 'espresso_people_categories' ) );

				//now should be able to delete permanently with no issues.
				$deleted = $PERM->delete_permanently_by_ID( $PER_ID, false );
				if ( $deleted ) {
					$total_deleted++;
				} else {
					$total_not_deleted++;
				}
			}
		} else {
			$PER_ID = isset( $this->_req_data['PER_ID'] ) ? absint( $this->_req_data['PER_ID'] ) : 0;
			if ( empty( $PER_ID ) ) {
				EE_Error::add_error( __('Unable to delete permanently the selected Person because no ID was given.', 'event_espresso' ), __FILE__, __FUNCTION__, __LINE__ );
				$total_not_deleted++;
			}

			//first delete any relationships with other posts for this id.
			$PPST->delete( array( array( 'PER_ID' => $PER_ID ) ) );

			//delete any term_taxonomy_relationships (gonna use wp core functions cause it's likely a bit faster)
			wp_delete_object_term_relationships( $PER_ID, array( 'espresso_people_type', 'espresso_people_categories' ) );

			$deleted = $PERM->delete_permanently_by_ID( $PER_ID, false );
			if ( $deleted ) {
				$total_deleted++;
			} else {
				$total_not_deleted++;
			}
		}

		if ( $total_deleted > 0 ) {
			EE_Error::add_success( sprintf( _n( '1 Person successfully deleted.', '%s People successfully deleted.', $total_deleted, 'event_espresso' ), $total_deleted ) );
		}

		if ( $total_not_deleted > 0 ) {
			EE_Error::add_error( sprintf( _n( '1 Person not deleted.', '%d People not deleted', $total_not_deleted, 'event_espresso' ), $total_not_deleted ), __FILE__, __FUNCTION__, __LINE__ );
		}
		$this->_redirect_after_action( FALSE, '', '', array( 'action' => 'default' ), TRUE );
	}




	####################
	# PEOPLE CATEGORY AND TYPE STUFF
	# ##################


	/**
	 * set the _term_object property with the term object for the loaded page.
	 *
	 * @return void
	 */
	private function _set_term_object( $taxonomy = 'espresso_people_categories' ) {
		if ( isset( $this->_term_object->id ) && !empty( $this->_term_object->id ) )
			return; //already have the term object so get out.

		//set default term object
		$this->_set_empty_term_object();

		if ( $taxonomy == 'espresso_people_categories' ) {
			$id = ! empty( $this->_req_data['PER_CAT_ID'] ) ? $this->_req_data['PER_CAT_ID'] : 0;
		} else {
			$id = ! empty( $this->_req_data['PER_TYPE_ID'] ) ? $this->_req_data['PER_TYPE_ID'] : 0;
		}

		//only set if we've got an id
		if ( empty( $id )) {
			return;
		}

		$term_id = absint($id);

		$term = get_term( $term_id, $taxonomy );

		if ( !empty( $term ) ) {
			$this->_term_object->category_name = $term->name;
			$this->_term_object->category_identifier = $term->slug;
			$this->_term_object->category_desc = $term->description;
			$this->_term_object->id = $term->term_id;
			$this->_term_object->parent = $term->parent;
		}
	}




	private function _set_empty_term_object() {
		$this->_term_object = new stdClass();
		$this->_term_object->category_name = $this->_term_object->category_identifier = $this->_term_object->category_desc  = '';
		$this->_term_object->id = $this->_term_object->parent = 0;
	}


	protected function _category_list_table() {
		do_action( 'AHEE_log', __FILE__, __FUNCTION__, '' );
		$this->_search_btn_label = __('Categories', 'event_espresso');
		$this->_admin_page_title .= ' ' . $this->get_action_link_or_button(
		    'add_category',
            'add_category',
            array(),
            'add-new-h2'
        );
		$this->display_admin_list_table_page_with_sidebar();
	}


	protected function _type_list_table() {
		do_action( 'AHEE_log', __FILE__, __FUNCTION__, '' );
		$this->_search_btn_label = __('Types', 'event_espresso');
		$this->_admin_page_title .= ' ' . $this->get_action_link_or_button(
		    'add_type',
            'add_type',
            array(),
            'add-new-h2'
        );
		$this->display_admin_list_table_page_with_sidebar();
	}


	protected function _output_details( $view, $taxonomy = 'espresso_people_categories' ) {
		//load formatter helper
		EE_Registry::instance()->load_helper( 'Formatter' );
		//load field generator helper
		EE_Registry::instance()->load_helper( 'Form_Fields' );

		$slug = $taxonomy == 'espresso_people_categories' ? 'category' : 'type';

		$route = $view == 'edit' ? 'update_' . $slug : 'insert_' . $slug;
		$this->_set_add_edit_form_tags($route);

		$this->_set_term_object( $taxonomy );
		$id = !empty($this->_term_object->id) ? $this->_term_object->id : '';

		$delete_action = 'delete_' . $slug;

		//custom redirect
		$redirect = EE_Admin_Page::add_query_args_and_nonce( array('action' => $slug . '_list'), $this->_admin_base_url );

		$id_ident = $taxonomy == 'espresso_people_categories' ? 'PER_CAT_ID' : 'PER_TYPE_ID';

		$this->_set_publish_post_box_vars( $id_ident, $id, $delete_action, $redirect );

		//take care of contents
		$this->_template_args['admin_page_content'] = $this->_term_object_details_content( $taxonomy );
		$this->display_admin_page_with_sidebar();
	}


	protected function _category_details($view) {
		$this->_output_details( $view );
	}

	protected function _type_details( $view ) {
		$this->_output_details( $view, 'espresso_people_type' );
	}


	protected function _term_object_details_content( $taxonomy = 'espresso_people_categories') {
		$editor_args['category_desc'] = array(
			'type' => 'wp_editor',
			'value' => EEH_Formatter::admin_format_content($this->_term_object->category_desc),
			'class' => 'my_editor_custom',
			'wpeditor_args' => array('media_buttons' => FALSE )
		);
		$_wp_editor = $this->_generate_admin_form_fields( $editor_args, 'array' );

		$all_terms = get_terms( array( $taxonomy ), array( 'hide_empty' => 0, 'exclude' => array( $this->_term_object->id ) ) );

		//setup category select for term parents.
		$category_select_values[] = array(
			'text' => __('No Parent', 'event_espresso'),
			'id' => 0
			);
		foreach ( $all_terms as $term ) {
			$category_select_values[] = array(
				'text' => $term->name,
				'id' => $term->term_id
				);
		}

		$category_select = EEH_Form_Fields::select_input( 'category_parent', $category_select_values, $this->_term_object->parent );

		$template_args = array(
			'category' => $this->_term_object,
			'category_select' => $category_select,
			'unique_id_info_help_link' => $this->_get_help_tab_link('unique_id_info'),
			'category_desc_editor' =>  $_wp_editor['category_desc']['field'],
			'disable' => '',
			'disabled_message' => FALSE,
			'term_name_label' => $taxonomy == 'espresso_people_categories' ? __('Category Name', 'event_espresso') : __('Type Name', 'event_espresso'),
			'term_id_description' => $taxonomy == 'espresso_people_categories' ? __('This is a default category so you can edit the label and the description but not the slug', 'event_espresso') : __('This is a default type so you can edit the label and the description but not the slug', 'event_espresso'),
			'term_parent_label' => $taxonomy == 'espresso_people_categories'  ? __('Category Parent', 'event_espresso' ) : __( 'Type Parent', 'event_espresso' ),
			'term_parent_description' => $taxonomy == 'espresso_people_categories' ? __('Categories are hierarchical.  You can change the parent for this category here.', 'event_espresso') : __('People Types are hierarchical.  You can change the parent for this type here.', 'event_espresso'),
			'term_description_label' => $taxonomy == 'espresso_people_categories' ? __('Category Description', 'event_espresso' ) : __( 'Type Description' )
			);
		$template = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'people_term_details.template.php';
		return EEH_Template::display_template($template, $template_args, TRUE );
	}


	protected function _delete_terms( $taxonomy = 'espresso_people_categories' ) {
		if ( $taxonomy == 'espresso_people_categories' ) {
			$term_ids = isset( $this->_req_data['PER_CAT_ID'] ) ? (array) $this->_req_data['PER_CAT_ID'] : (array) $this->_req_data['category_id'];
		} else {
			$term_ids = isset( $this->_req_data['PER_TYPE_ID'] ) ? (array) $this->_req_data['PER_TYPE_ID'] : (array) $this->_req_data['type_id'];
		}

		foreach ( $term_ids as $term_id ) {
			$this->_delete_term( $taxonomy, $term_id);
		}

		//doesn't matter what page we're coming from... we're going to the same place after delete.
		$query_args = array(
			'action' => $taxonomy == 'espresso_people_categories' ? 'category_list' : 'type_list'
			);
		$this->_redirect_after_action(0,'','',$query_args);
	}


	protected function _delete_categories() {
		$this->_delete_terms();
	}


	protected function _delete_types() {
		$this->_delete_terms( 'espresso_people_type' );
	}



	protected function _delete_term( $taxonomy, $term_id) {
		global $wpdb;
		$term_id = absint( $term_id );
		wp_delete_term( $term_id, $taxonomy );
	}




	protected function _insert_or_update_term( $new_term, $taxonomy = 'espresso_people_categories' ) {
		$term_id = $new_term ? $this->_insert_term( FALSE, $taxonomy ) : $this->_insert_term( TRUE, $taxonomy );
		$success = 0; //we already have a success message so lets not send another.
		$id_ident = $taxonomy == 'espresso_people_categories' ? 'PER_CAT_ID' : 'PER_TYPE_ID';
		$slug = $taxonomy == 'espresso_people_categories' ? 'category' : 'type';
		$query_args = array(
			'action' => 'edit_' . $slug,
			$id_ident => $term_id
		);
		$this->_redirect_after_action( $success, '','', $query_args, TRUE );
	}



	protected function _insert_or_update_category($new_category) {
		$this->_insert_or_update_term( $new_category );
	}


	protected function _insert_or_update_type( $new_type ) {
		$this->_insert_or_update_term( $new_type, 'espresso_people_type' );
	}



	private function _insert_term( $update = FALSE, $taxonomy = 'espresso_people_categories' ) {
		if ( $taxonomy == 'espresso_people_categories' ) {
			$term_id = $update ? $this->_req_data['PER_CAT_ID'] : '';
		} else {
			$term_id = $update ? $this->_req_data['PER_TYPE_ID'] : '';
		}
		$category_name= isset( $this->_req_data['category_name'] ) ? $this->_req_data['category_name'] : '';
		$category_desc= isset( $this->_req_data['category_desc'] ) ? $this->_req_data['category_desc'] : '';
		$category_parent = isset( $this->_req_data['category_parent'] ) ? $this->_req_data['category_parent'] : 0;

		$term_args=array(
			'name'=>$category_name,
			'description'=>$category_desc,
			'parent'=>$category_parent
		);
		//was the category_identifier input disabled?
		if(isset($this->_req_data['category_identifier'])){
			$term_args['slug'] = $this->_req_data['category_identifier'];
		}

		$insert_ids = $update ? wp_update_term( $term_id, $taxonomy, $term_args ) : wp_insert_term( $category_name, $taxonomy, $term_args );

		if ( !is_array( $insert_ids ) ) {
			$msg = $taxonomy == 'espresso_people_categories' ? __( 'An error occurred and the category has not been saved to the database.', 'event_espresso' ) : __( 'An error occurred and the people type has not been saved to the database.', 'event_espresso' );
			EE_Error::add_error( $msg, __FILE__, __FUNCTION__, __LINE__ );
		} else {
			$term_id = $insert_ids['term_id'];
			$msg = $taxonomy == 'espresso_people_categories' ? sprintf ( __('The category %s was successfuly saved', 'event_espresso'), $category_name ) : sprintf ( __('The people type %s was successfuly saved', 'event_espresso'), $category_name );
			EE_Error::add_success( $msg );
		}

		return $term_id;
	}




	public function get_terms( $taxonomy = 'espresso_people_categories', $per_page = 10, $current_page = 1, $count = FALSE ) {
		global $wpdb;

		//testing term stuff
		$orderby = isset( $this->_req_data['orderby'] ) ? $this->_req_data['orderby'] : 'Term.term_id';
		$order = isset( $this->_req_data['order'] ) ? $this->_req_data['order'] : 'DESC';
		$limit = ($current_page-1)*$per_page;

		$where = array( 'taxonomy' => $taxonomy );

		if ( isset( $this->_req_data['s'] ) ) {
			$sstr = '%' . $this->_req_data['s'] . '%';
			$where['OR'] = array(
				'Term.name' => array( 'LIKE', $sstr),
				'description' => array( 'LIKE', $sstr )
				);
		}

		$query_params = array(
			$where ,
			'order_by' => array( $orderby => $order ),
			'limit' => $limit . ',' . $per_page,
			'force_join' => array('Term')
			);

		$terms = $count ? EEM_Term_Taxonomy::instance()->count( $query_params, 'term_id' ) :EEM_Term_Taxonomy::instance()->get_all( $query_params );

		return $terms;
	}


}
// End of file People_Admin_Page.core.php
// Location: /wp-content/plugins/eea-people-addon/admin/eea-people-addon/People_Admin_Page.core.php
