<?php
/**
 * This file contains the class for extending EEM_Term_Relationship to add people relationships
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * EEME_Person_Term_Relationship class
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEME_Person_Term_Relationship extends EEME_Base{
	public function __construct() {
		$this->_model_name_extended = 'Term_Relationship';
		$this->_extra_relations = array('Person'=>new EE_Belongs_To_Relation() );
		parent::__construct();
	}


	/**
	 * Override parent so we can add Person to object ID field without missing other foreign keys that might have been * added.
	 *
	 * @param array $existing_fields array of existing fields
	 */
	public function add_extra_fields_on_filter( $existing_fields ) {
		$object_id_foreign_keys = $existing_fields['Term_Relationship']['object_id'] instanceof EE_Foreign_Key_Int_Field ?  $existing_fields['Term_Relationship']['object_id']->get_model_names_pointed_to() : null;
		if ( ! empty( $object_id_foreign_keys ) ) {
			$object_id_foreign_keys = (array) $object_id_foreign_keys;
			$object_id_foreign_keys[] = 'Person';
			$this->_extra_fields['Term_Relationship']['object_id'] = new EE_Foreign_Key_Int_Field('object_id', __('Object(Post) ID','event_espresso'), false,0,$object_id_foreign_keys);
		}
		return parent::add_extra_fields_on_filter( $existing_fields );
	}
}

// End of file EEME_Person_Term_Relationship.model_ext.php
