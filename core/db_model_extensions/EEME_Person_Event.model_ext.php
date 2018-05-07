<?php

/**
 *
 * EEME_Person_Event class
 *
 * @since 1.0.0
 *
 * @package     EE People Addon
 * @subpackage  models
 * @author      Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EEME_Person_Event extends EEME_Base
{
    public function __construct()
    {
        $this->_model_name_extended = 'Event';
        $this->_extra_relations = array(
            'Person'=>new EE_HABTM_Relation('Person_Post'),
            'Person_Post' => new EE_Has_Many_Relation()
             );
        parent::__construct();
    }
}
