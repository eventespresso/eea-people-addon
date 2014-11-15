<?php
/**
 * This file contains the class for the People Model
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * People model class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_People extends EE_CPT_Base {
	/**
	 *
	 * @param type $props_n_values
	 * @return EE_People
	 */
	public static function new_instance( $props_n_values = array() ) {
		$classname = __CLASS__;
		$has_object = parent::_check_for_object( $props_n_values, $classname );
		$obj = $has_object ? $has_object : new self( $props_n_values );
		$obj->set_timezone( $obj->timezone_string() );
		return $obj;
	}


	public static function new_instance_from_db ( $props_n_values = array() ) {
		$obj =  new self( $props_n_values, TRUE );
		$obj->set_timezone( $obj->timezone_string() );
		return $obj;
	}
}

// End of file EE_People.class.php
