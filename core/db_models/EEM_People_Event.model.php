<?php
/**
 * This file contains the model class for the People Event Model
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * People Event Model class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEM_People_Event extends EEM_Base{
	// private instance of the EEM_People object
	private static $_instance = NULL;

	public function __construct($timezone = NULL) {
		$this->_tables = array(
			'People_Event' => new EE_Primary_Table( 'esp_people_to_post', 'PTP_ID' )
			);
		$this->_fields = array(
			'People_Event' => array(
				'PTP_ID' => new EE_Primary_Key_Int_Field( 'PTP_ID', __('People to Event Link ID', 'event_espresso' ) ),
				'PPL_ID' => new EE_Foreign_Key_Int_Field( 'PPL_ID', __('People Primary ID', 'event_espresso' ), false, 0, 'People' ),
				'EVT_ID' => new EE_Foreign_Key_Int_Field( 'POST_ID', __('Event ID', 'event_espresso' ), false, 0, 'Event' ),
				'PPL_EVT_order' => new EE_Integer_Field( 'P2P_Order', __('People to Event Order', 'event_Espresso'), false, 0 ),
				'PT_ID' => new EE_Foreign_Key_Int_Field( 'PT_ID', __('People Type ID', 'event_espresso' ), 'Term_Taxonomy' )
				)
			);
		$this->_model_relations = array(
			'People' => new EE_Belongs_To_Relation(),
			'Event' => new EE_Belongs_To_Relation(),
			'Term_Taxonomy' => new EE_Belongs_to_Relation()
			);
		parent::__construct();
	}



	/**
	 * This function is a singleton method used to instantiate the EEM_People_Event object
	 *
	 * @since 1.0.0
	 * @return EEM_People_Event instance
	 */
	public static function instance(){

		// check if instance of EEM_People_Event already exists
		if ( self::$_instance === NULL ) {
			// instantiate Espresso_model
			self::$_instance = new self();
		}
		// EEM_People_Event object
		return self::$_instance;
	}



	/**
	 * resets the model and returns it
	 * @return EEM_People_Event
	 */
	public static function reset(){
		self::$_instance = NULL;
		return self::instance();
	}


} //end EEM_People_Event model
