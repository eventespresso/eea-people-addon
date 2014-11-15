<?php
/**
 * This file contains the model class for the Person Event Model
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * Person Event Model class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEM_Person_Event extends EEM_Base{
	// private instance of the EEM_Person object
	private static $_instance = NULL;

	public function __construct($timezone = NULL) {
		$this->_tables = array(
			'Person_Event' => new EE_Primary_Table( 'esp_people_to_post', 'PTP_ID' )
			);
		$this->_fields = array(
			'Person_Event' => array(
				'PTP_ID' => new EE_Primary_Key_Int_Field( 'PTP_ID', __('Person to Event Link ID', 'event_espresso' ) ),
				'PER_ID' => new EE_Foreign_Key_Int_Field( 'PER_ID', __('Person Primary ID', 'event_espresso' ), false, 0, 'Person' ),
				'EVT_ID' => new EE_Foreign_Key_Int_Field( 'POST_ID', __('Event ID', 'event_espresso' ), false, 0, 'Event' ),
				'PER_EVT_order' => new EE_Integer_Field( 'P2P_Order', __('Person to Event Order', 'event_Espresso'), false, 0 ),
				'PT_ID' => new EE_Foreign_Key_Int_Field( 'PT_ID', __('People Type ID', 'event_espresso' ), 'Term_Taxonomy' )
				)
			);
		$this->_model_relations = array(
			'Person' => new EE_Belongs_To_Relation(),
			'Event' => new EE_Belongs_To_Relation(),
			'Term_Taxonomy' => new EE_Belongs_to_Relation()
			);
		parent::__construct();
	}



	/**
	 * This function is a singleton method used to instantiate the EEM_Person_Event object
	 *
	 * @since 1.0.0
	 * @return EEM_Person_Event instance
	 */
	public static function instance( $timezone = null ){

		// check if instance of EEM_Person_Event already exists
		if ( self::$_instance === NULL ) {
			// instantiate Espresso_model
			self::$_instance = new self();
		}
		// EEM_Person_Event object
		return self::$_instance;
	}



	/**
	 * resets the model and returns it
	 * @return EEM_Person_Event
	 */
	public static function reset(){
		self::$_instance = NULL;
		return self::instance();
	}


} //end EEM_Person_Event model
