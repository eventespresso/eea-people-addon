<?php
/**
 * This file contains the Dat Migration Script for People addon version 1.0.0
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage dms
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * People Addon Data Migration Script
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	dms
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_DMS_People_1_0_0 extends EE_Data_Migration_Script_Base{

	public function __construct() {
		$this->_pretty_name = __("Data Migration to People Addon 1.0.0", "event_espresso");
		$this->_migration_stages = array();
		parent::__construct();
	}
	/**
	 * Indicates whether or not this data migration script should migrate data or not
	 * @param array $current_database_state_of keys are EE plugin slugs like
	 *				'Core', 'Calendar', 'Mailchimp',etc, Your addon's slug can be retrieved
	 *				using $this->slug(). Your addon's entry database state is located
	 *				at $current_database_state_of[ $this->slug() ] if it was previously
	 *				intalled; if it wasn't previously installed its NOT in the array
	 * @return boolean
	 */
	public function can_migrate_from_version($current_database_state_of) {
		return false;
	}

	public function schema_changes_after_migration() {}

	public function schema_changes_before_migration() {
		$this->_table_is_new_in_this_version('esp_people_to_post', "
			PTP_ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			PER_ID bigint(20) unsigned NOT NULL DEFAULT 0,
			OBJ_ID bigint(20) unsigned NOT NULL DEFAULT 0,
			OBJ_type varchar(50) NOT NULL DEFAULT 'Event',
			P2P_Order tinyint(3) NOT NULL DEFAULT 0,
			PT_ID bigint(20) unsigned NOT NULL DEFAULT 0,
			PRIMARY KEY  (PTP_ID),
			KEY PER_ID (PER_ID),
			KEY OBJ_ID (OBJ_ID),
			KEY OBJ_type (OBJ_type),
			KEY PT_ID (PT_ID)"
				);
	}
}

// End of file EE_DMS_eea-people-addon_0_0_1.dms.php
