<?php

/**
 *
 * EEME_Person_Term_Taxonomy class
 *
 * @since 1.0.0
 *
 * @package     EE People Addon
 * @subpackage  models
 * @author      Darren Ethier
 */
class EEME_Person_Term_Taxonomy extends EEME_Base
{
    public function __construct()
    {
        $this->_model_name_extended = 'Term_Taxonomy';
        $this->_extra_relations = array('Person' => new EE_HABTM_Relation('Term_Relationship') );
        parent::__construct();
    }
}
