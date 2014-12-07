<?php
/**
 * This file contains the class for extending EEM_Term_Taxonomy to add people relationships
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * EEME_Person_Term_Taxonomy class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEME_Person_Term_Taxonomy extends EEME_Base{
	function __construct() {
		$this->_model_name_extended = 'Term_Taxonomy';
		$this->_extra_relations = array('Person'=>new EE_HABTM_Relation('Term_Relationship') );
		parent::__construct();
	}
}

// End of file EEME_Person_Term_Taxonomy.model_ext.php
