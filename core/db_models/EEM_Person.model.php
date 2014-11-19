<?php
/**
 * This file contains the model class for the People CPT
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * People Model class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEM_Person extends EEM_CPT_Base{
	// private instance of the EEM_Person object
	private static $_instance = NULL;

	public function __construct($timezone = NULL) {
		$this->_tables = array(
			'Person_CPT'=>new EE_Primary_Table( 'posts', 'ID'),
			'Person_Meta' => new EE_Secondary_Table( 'esp_attendee_meta', 'ATTM_ID', 'ATT_ID' )
		);
		$this->_fields = array(
			'Person_CPT'=>array(
				'PER_ID'=>new EE_Primary_Key_Int_Field('ID', __("Person ID", 'event_espresso')),
				'PER_full_name'=>new EE_Plain_Text_Field('post_title', __("Person's Full Name", "event_espresso"), false, __("Unknown", "event_espresso")),
				'PER_bio'=>new EE_Post_Content_Field('post_content', __("Person's Biography", "event_espresso"), false, __("No Biography Provided", "event_espresso")),
				'PER_slug'=>new EE_Slug_Field('post_name', __("Person's URL Slug", "event_espresso"), false),
				'PER_created'=>new EE_Datetime_Field('post_date', __("Time Person Created", "event_espresso"), false, current_time('timestamp')),
				'PER_short_bio'=>new EE_Simple_HTML_Field('post_excerpt', __("Person's Short Biography", "event_espresso"), true, __("No Biography Provided", "event_espresso")),
				'PER_modified'=>new EE_Datetime_Field('post_modified', __("Time Person Last Modified", "event_espresso"), true, current_time('timestamp')),
				'PER_wp_user'=>new EE_Integer_Field('post_author', __("WP User that Created this Person", "event_espresso"), false, get_current_user_id() ),
				'PER_parent'=>new EE_Integer_Field('post_parent', __("Parent Person", "event_espresso"), true),
				'post_type'=>new EE_WP_Post_Type_Field('espresso_people'),
				'status' => new EE_WP_Post_Status_Field('post_status', __('Person\'s Status', 'event_espresso'), false, 'publish')
			),
			'Person_Meta'=>array(
				'PERM_ID'=> new EE_DB_Only_Int_Field('ATTM_ID', __('Person Meta Row ID','event_espresso'), false),
				'PER_ID_fk'=>new EE_DB_Only_Int_Field('ATT_ID', __("Foreign Key to People in Post Table", "event_espresso"), false),
				'PER_fname'=>new EE_Plain_Text_Field('ATT_fname', __('First Name','event_espresso'), true, ''),
				'PER_lname'=>new EE_Plain_Text_Field('ATT_lname', __('Last Name','event_espresso'), true, ''),
				'PER_address'=>new EE_Plain_Text_Field('ATT_address', __('Address Part 1','event_espresso'), true, ''),
				'PER_address2'=>new EE_Plain_Text_Field('ATT_address2', __('Address Part 2','event_espresso'), true, ''),
				'PER_city'=>new EE_Plain_Text_Field('ATT_city', __('City','event_espresso'), true, ''),
				'STA_ID'=>new EE_Foreign_Key_Int_Field('STA_ID', __('State','event_espresso'), true,0,'State'),
				'CNT_ISO'=>new EE_Foreign_Key_String_Field('CNT_ISO', __('Country','event_espresso'), true,'','Country'),
				'PER_zip'=>new EE_Plain_Text_Field('ATT_zip', __('ZIP/Postal Code','event_espresso'), true, ''),
				'PER_email'=>new EE_Email_Field('ATT_email', __('Email Address','event_espresso'), true, ''),
				'PER_phone'=>new EE_Plain_Text_Field('ATT_phone', __('Phone','event_espresso'), true, '')
			));
		$this->_model_relations = array(
			'Event' => new EE_HABTM_Relation('Person_Post'), //note this will use the same people_to_post table that will eventually be shared with People_To_Venue, and People_To_Attendee relations.
			'Person_Post' => new EE_Has_Many_Relation(),
			'State' => new EE_Belongs_To_Relation(),
			'Country' => new EE_Belongs_To_Relation()
		);
		$this->_default_where_conditions_strategy = new EE_CPT_Where_Conditions( 'espresso_people', 'PERM_ID' );
		parent::__construct($timezone);
	}




	/**
	 * This function is a singleton method used to instantiate the EEM_Person object
	 *
	 * @since 1.0.0
	 * @return EEM_Person instance
	 */
	public static function instance( $timezone = null ){

		// check if instance of EEM_Person already exists
		if ( self::$_instance === NULL ) {
			// instantiate Espresso_model
			self::$_instance = new self( $timezone );
		}

		//we might have a timezone set, let set_timezone decide what to do with it
		self::$_instance->set_timezone( $timezone );

		// EEM_Person object
		return self::$_instance;
	}

	/**
	 * resets the model and returns it
	 * @return EEM_Person
	 */
	public static function reset( $timezone = NULL ){
		self::$_instance = NULL;
		return self::instance( $timezone );
	}





	/**
	 * This returns an array of EE_Person objects that are attached to the given post and people type ordered by
	 * the relationship order field.
	 *
	 * @param int $post_id   CPT post id.
	 * @param int $type_id Term_Taxonomy id.
	 *
	 * @return EE_Person[]
	 */
	public function get_people_for_event_and_type( $post_id, $type_id ) {
		$where['Person_Post.OBJ_ID'] = $post_id;
		$where['Person_Post.PT_ID'] = $type_id;

		$query = array( $where, 'order_by' => array( 'Person_Post.PER_OBJ_order' => 'ASC' ) );
		return $this->get_all( $query);
	}
}

// End of file EEM_Person.model.php
