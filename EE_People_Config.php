<?php
/**
 *
 * Class EE_People_Config
 *
 * Description
 *
 * @package               Event Espresso
 * @subpackage            core
 * @author                Brent Christensen
 *
 *
 */

class EE_People_Config extends EE_Config_Base
{

    public $event_single_display_order_people;
    public $event_archive_display_order_people;


    /**
     *    class constructor
     */
    public function __construct()
    {
        $this->event_single_display_order_people = 125;
        $this->event_archive_display_order_people = 125;
    }
}
