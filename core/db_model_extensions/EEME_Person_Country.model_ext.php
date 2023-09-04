<?php

/**
 *
 * EEME_Person_Country class
 *
 * @since 1.0.0
 *
 * @package     EE People Addon
 * @subpackage  models
 * @author      Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEME_Person_Country extends EEME_Base
{
    public function __construct()
    {
        $this->_model_name_extended = 'Country';
        $this->_extra_relations = array('Person' => new EE_Has_Many_Relation() );
        parent::__construct();
    }
}
