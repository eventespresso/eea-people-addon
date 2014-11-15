<?php
/**
 * This file contains the class for extending EEM Country to add people relationships
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * EEME_Person_Country class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEME_Person_Country extends EEME_Base{
	function __construct() {
		$this->_model_name_extended = 'Country';
		$this->_extra_relations = array('Person'=>new EE_Has_Many_Relation() );
		parent::__construct();
	}
}

// End of file EEME_Person_Country.model_ext.php
