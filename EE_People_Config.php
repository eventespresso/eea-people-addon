<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Event Espresso
 *
 * Event Registration and Ticketing Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author			    Event Espresso
 * @ copyright		(c) 2008-2014 Event Espresso  All Rights Reserved.
 * @ license			http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link					http://www.eventespresso.com
 * @ version		 	$VID:$
 *
 * ------------------------------------------------------------------------
 */
 /**
 *
 * Class EE_People_Config
 *
 * Description
 *
 * @package         Event Espresso
 * @subpackage    core
 * @author				Brent Christensen
 * @since		 	   $VID:$
 *
 */

class EE_People_Config extends EE_Config_Base {


  public $event_single_display_order_people;
  public $event_archive_display_order_people;



  /**
   *    class constructor
   */
  public function __construct() {
   $this->event_single_display_order_people = 125;
   $this->event_archive_display_order_people = 125;
  }

}



// End of file EE_People_Config.php
// Location: /wp-content/plugins/eea-people-addon/EE_People_Config.php
