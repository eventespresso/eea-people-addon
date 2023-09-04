<?php

/**
 * Person Event Model class
 *
 * @since       1.0.0
 * @package     EE People Addon
 * @subpackage  models
 * @author      Darren Ethier
 */
class EEM_Person_Post extends EEM_Base
{
    protected static ?EEM_Person_Post $_instance = null;


    public function __construct(?string $timezone = '')
    {
        $this->_tables          = [
            'Person_Post' => new EE_Primary_Table('esp_people_to_post', 'PTP_ID'),
        ];
        $this->_fields          = [
            'Person_Post' => [
                'PTP_ID'        => new EE_Primary_Key_Int_Field(
                    'PTP_ID',
                    __('Person to Event Link ID', 'event_espresso')
                ),
                'PER_ID'        => new EE_Foreign_Key_Int_Field(
                    'PER_ID',
                    __('Person Primary ID', 'event_espresso'),
                    false,
                    0,
                    'Person'
                ),
                'OBJ_ID'        => new EE_Foreign_Key_Int_Field(
                    'OBJ_ID', __('Event ID', 'event_espresso'), false, 0, ['Event']
                ),
                'OBJ_type'      => new EE_Any_Foreign_Model_Name_Field(
                    'OBJ_type',
                    __('Model Person Related to', 'event_espresso'),
                    false,
                    'Event',
                    ['Event']
                ),
                'PER_OBJ_order' => new EE_Integer_Field(
                    'P2P_Order',
                    __('Person to Event Order', 'event_espresso'),
                    false,
                    0
                ),
                'PT_ID'         => new EE_Foreign_Key_Int_Field(
                    'PT_ID',
                    __('People Type ID', 'event_espresso'),
                    false,
                    0,
                    'Term_Taxonomy'
                ),
            ],
        ];
        $this->_model_relations = [
            'Person'        => new EE_Belongs_To_Relation(),
            'Event'         => new EE_Belongs_To_Any_Relation(),
            'Term_Taxonomy' => new EE_Belongs_to_Relation(),
        ];
        parent::__construct($timezone);
    }


    public function get_all_people_ids_for_post_and_type($post_id, $type_id)
    {
        $_where = [
            'OBJ_ID' => $post_id,
            'PT_ID'  => $type_id,
        ];
        $pes    = $this->get_all([$_where]);
        $ids    = [];
        foreach ($pes as $pe) {
            $ids[] = $pe->get('PER_ID');
        }
        return $ids;
    }
}
