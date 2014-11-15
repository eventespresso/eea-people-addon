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
class EEM_People extends EEM_CPT_Base{
	// private instance of the EEM_People object
	private static $_instance = NULL;

	public function __construct($timezone = NULL) {
		$this->_tables = array(
			'People_CPT'=>new EE_Primary_Table( 'posts', 'ID'),
			'People_Meta' => new EE_Secondary_Table( 'esp_attendee_meta', 'ATTM_ID', 'ATT_ID' )
		);
		$this->_fields = array(
			'People_CPT'=>array(
				'PPL_ID'=>new EE_Primary_Key_Int_Field('ID', __("People ID", 'event_espresso')),
				'PPL_full_name'=>new EE_Plain_Text_Field('post_title', __("Person's Full Name", "event_espresso"), false, __("Unknown", "event_espresso")),
				'PPL_bio'=>new EE_Post_Content_Field('post_content', __("Person's Biography", "event_espresso"), false, __("No Biography Provided", "event_espresso")),
				'PPL_slug'=>new EE_Slug_Field('post_name', __("Person's URL Slug", "event_espresso"), false),
				'PPL_created'=>new EE_Datetime_Field('post_date', __("Time Person Created", "event_espresso"), false, current_time('timestamp')),
				'PPL_short_bio'=>new EE_Simple_HTML_Field('post_excerpt', __("Person's Short Biography", "event_espresso"), true, __("No Biography Provided", "event_espresso")),
				'PPL_modified'=>new EE_Datetime_Field('post_modified', __("Time Person Last Modified", "event_espresso"), true, current_time('timestamp')),
				'PPL_author'=>new EE_Integer_Field('post_author', __("WP User that Created this Person", "event_espresso"), false, get_current_user_id() ),
				'PPL_parent'=>new EE_Integer_Field('post_parent', __("Parent Person", "event_espresso"), true),
				'post_type'=>new EE_WP_Post_Type_Field('espresso_people'),
				'status' => new EE_WP_Post_Status_Field('post_status', __('Person\'s Status', 'event_espresso'), false, 'publish')
			),
			'People_Meta'=>array(
				'PPLM_ID'=> new EE_DB_Only_Int_Field('ATTM_ID', __('People Meta Row ID','event_espresso'), false),
				'PPL_ID_fk'=>new EE_DB_Only_Int_Field('ATT_ID', __("Foreign Key to People in Post Table", "event_espresso"), false),
				'PPL_fname'=>new EE_Plain_Text_Field('PPL_fname', __('First Name','event_espresso'), true, ''),
				'PPL_lname'=>new EE_Plain_Text_Field('PPL_lname', __('Last Name','event_espresso'), true, ''),
				'PPL_address'=>new EE_Plain_Text_Field('PPL_address', __('Address Part 1','event_espresso'), true, ''),
				'PPL_address2'=>new EE_Plain_Text_Field('PPL_address2', __('Address Part 2','event_espresso'), true, ''),
				'PPL_city'=>new EE_Plain_Text_Field('PPL_city', __('City','event_espresso'), true, ''),
				'STA_ID'=>new EE_Foreign_Key_Int_Field('STA_ID', __('State','event_espresso'), true,0,'State'),
				'CNT_ISO'=>new EE_Foreign_Key_String_Field('CNT_ISO', __('Country','event_espresso'), true,'','Country'),
				'PPL_zip'=>new EE_Plain_Text_Field('PPL_zip', __('ZIP/Postal Code','event_espresso'), true, ''),
				'PPL_email'=>new EE_Email_Field('PPL_email', __('Email Address','event_espresso'), true, ''),
				'PPL_phone'=>new EE_Plain_Text_Field('PPL_phone', __('Phone','event_espresso'), true, '')
			));
		$this->_model_relations = array(
			'Event' => new EE_HABTM_Relation('People_Event') //note this will use the same people_to_post table that will eventually be shared with People_To_Venue, and People_To_Attendee relations.
		);
		$this->_default_where_conditions_strategy = new EE_CPT_Where_Conditions( 'espresso_people', 'PPLM_ID' );
		parent::__construct($timezone);
	}




	/**
	 * This function is a singleton method used to instantiate the EEM_People object
	 *
	 * @since 1.0.0
	 * @return EEM_People instance
	 */
	public static function instance( $timezone = null ){

		// check if instance of EEM_People already exists
		if ( self::$_instance === NULL ) {
			// instantiate Espresso_model
			self::$_instance = new self( $timezone );
		}

		//we might have a timezone set, let set_timezone decide what to do with it
		self::$_instance->set_timezone( $timezone );

		// EEM_People object
		return self::$_instance;
	}

	/**
	 * resets the model and returns it
	 * @return EEM_People
	 */
	public static function reset(){
		self::$_instance = NULL;
		return self::instance();
	}
}

// End of file EEM_People.model.php
