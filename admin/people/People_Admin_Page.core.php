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


	protected function _init_page_props() {
		$this->page_slug = EEA_PEOPLE_PG_SLUG;
		$this->page_label = EEA_PEOPLE_LABEL;
		$this->_admin_base_url = EEA_PEOPLE_ADDON_ADMIN_URL;
		$this->_admin_base_path = EEA_PEOPLE_ADDON_ADMIN;
		$this->page_label = __('Manage People', 'event_espresso' );
		$this->_cpt_routes = array(
			'add_new_person' => 'espresso_people',
			'edit_person' => 'espresso_people',
			'insert_person' => 'espresso_people',
			'update_person' => 'espresso_people'
			);
		$this->_cpt_model_names = array(
			'add_new_person' => 'EEM_Person',
			'edit_person' => 'EEM_Person'
			);
		$this->_cpt_edit_routes = array(
			'espresso_people' => 'edit_person'
			);
		add_action( 'edit_form_after_title', array( $this, 'after_title_form_fields'), 10 );
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
				'edit' => __( 'Add Person', 'event_espresso' )
				),
			'publishbox' => array(
				'edit_person' => __( 'Update Person Record', 'event_espresso' ),
				'add_new_person' => __( 'Save Person Record', 'event_espresso' )
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
			'add_new_person' => array(
				'func' => '_create_new_cpt_item',
				'args' => array( 'new_person' => true ),
				'capability' => 'ee_edit_peoples'
			),
			'edit_person' => array(
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
				'args' => array( 'trash', false ),
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
			)
		);
	}





	protected function _set_page_config() {

		$this->_page_config = array(
			'default' => array(
				'nav' => array(
					'label' => __('People List', 'event_espresso'),
					'order' => 10
					),
				'list_table' => 'EE_People_List_Table',
				'metaboxes' => array(),
				'require_nonce' => false
			),
			'add_new_person' => array(
				'nav' => array(
					'label' => __('Add Person', 'event_espresso'),
					'order' => 15,
					'persistent' => false
					),
				'metaboxes' => array( '_publish_post_box', 'people_editor_metaboxes' ),
				'require_nonce' => false
			),
			'edit_person' => array(
				'nav' => array(
					'label' => __('Edit Person', 'event_espresso'),
					'order' => 20,
					'persistent' => false,
					'url' => isset($this->_req_data['post']) ? add_query_arg(array('post' => $this->_req_data['post'] ), $this->_current_page_view_url )  : $this->_admin_base_url
					),
				'metaboxes' => array('people_editor_metaboxes', '_publish_post_box'),
				'require_nonce' => false
				)
		);
	}


	protected function _add_screen_options() {}
	protected function _add_screen_options_default() {
		$this->_per_page_screen_option();
	}

	protected function _add_feature_pointers() {}
	public function load_scripts_styles() {}

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
			'published' => array(
				'slug' => 'published',
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
					'restore_people' => __( 'Restore from Trash', 'event_espresso' )
					)
				);
			$this->_views['all']['bulk_action'] = array(
					'trash_people' => __( 'Move to Trash', 'event_espresso' )
				);
			$this->_views['published']['bulk_action'] = array(
					'trash_people' => __( 'Move to Trash', 'event_espresso' )
				);
			$this->_views['draft']['bulk_action'] = array(
					'trash_people' => __( 'Move to Trash', 'event_espresso' )
				);
		}
	}


	/**
	 * Set up page for people list table
	 *
	 * @return string
	 */
	protected function _people_list_table() {
		$this->_search_btn_label = __('People', 'event_espresso');
		$this->_admin_page_title .= $this->get_action_link_or_button('add_new_person', 'add-person', array(), 'add-new-h2');
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
				$status = array();
				break;

			case 'draft' :
				$status = array( 'draft', 'auto-draft' );
				$where['status'] = array( 'IN', array('draft', 'auto-draft') );
				break;

			default :
				$status = array( $status );
				$where['status'] = $status;
		}

		//possible conditions for capability checks
		if ( ! EE_Registry::instance()->CAP->current_user_can( 'ee_read_private_peoples', 'get_people') ) {
			$where['status**'] = array( '!=', 'private' );
		}

		if ( ! EE_Registry::instance()->CAP->current_user_can( 'ee_read_others_peoples', 'get_people' ) ) {
			$where['PER_wp_user'] =  get_current_user_id();
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
			$people = $count ? $PPLM->count( array($_where, 'order_by'=>array($orderby=>$sort),'limit'=>$limit)) : $PPLM->get_all( array($_where, 'order_by'=>array($orderby=>$sort), 'limit'=>$limit) );

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
			'PER_full_name'=> $this->_req_data['PER_fname'] . ' ' . $this->_req_data['PER_lname'],
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

}
// End of file People_Admin_Page.core.php
// Location: /wp-content/plugins/eea-people-addon/admin/eea-people-addon/People_Admin_Page.core.php
