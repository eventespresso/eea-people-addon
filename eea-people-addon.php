<?php
/*
  Plugin Name: Event Espresso - People Addon (EE 4.5+)
  Plugin URI: http://www.eventespresso.com
  Description: The Event Espresso People Addon adds a people manager for your events. Compatible with Event Espresso 4.5 or higher
  Version: 1.0.7.p
  Author: Event Espresso
  Author URI: http://www.eventespresso.com
  Copyright 2014 Event Espresso (email : support@eventespresso.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA

 * ------------------------------------------------------------------------
 *
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package		Event Espresso
 * @ author		Event Espresso
 * @ copyright	(c) 2008-2014 Event Espresso  All Rights Reserved.
 * @ license		http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link			http://www.eventespresso.com
 * @ version	 	EE4
 *
 * ------------------------------------------------------------------------
 */
define( 'EEA_PEOPLE_ADDON_VERSION', '1.0.7.p' );
define( 'EEA_PEOPLE_ADDON_PLUGIN_FILE',  __FILE__ );
function load_espresso_eea_people_addon() {
	if ( class_exists( 'EE_Addon' )) {
		// eea-people-addon version
		require_once ( plugin_dir_path( __FILE__ ) . 'EE_People.class.php' );
		EE_People::register_addon();
	}
}
add_action( 'AHEE__EE_System__load_espresso_addons', 'load_espresso_eea_people_addon' );

// End of file eea-people-addon.php
// Location: wp-content/plugins/eea-people-addon/eea-people-addon.php
