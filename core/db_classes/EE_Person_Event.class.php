<?php
/**
 * This file contains the class for the Person Event Model object
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * Person Event model object class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_Person_Event extends EE_Base_Class {
	/**
	 *
	 * @param type $props_n_values
	 * @return EE_Person_Event
	 */
	public static function new_instance( $props_n_values = array() ) {
		$classname = __CLASS__;
		$has_object = parent::_check_for_object( $props_n_values, $classname );
		 return $has_object ? $has_object : new self( $props_n_values );
	}


	public static function new_instance_from_db ( $props_n_values = array() ) {
		return  new self( $props_n_values, TRUE );
	}
}

// End of file EE_Person_Event.class.php
