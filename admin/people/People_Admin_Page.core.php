<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) { exit('NO direct script access allowed'); }
/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @package		Event Espresso
 * @author			Event Espresso
 * @copyright 	(c) 2009-2014 Event Espresso All Rights Reserved.
 * @license			http://eventespresso.com/support/terms-conditions/  ** see Plugin Licensing **
 * @link				http://www.eventespresso.com
 * @version			EE4
 *
 * ------------------------------------------------------------------------
 *
 * People_Admin_Page
 *
 * This contains the logic for setting up the eea-people-addon Addon Admin related pages.  Any methods without PHP doc comments have inline docs with parent class.
 *
 *
 * @package			People_Admin_Page (eea-people-addon addon)
 * @subpackage 	admin/People_Admin_Page.core.php
 * @author				Darren Ethier, Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
class People_Admin_Page extends EE_Admin_Page {


	protected function _init_page_props() {
		$this->page_slug = EEA-PEOPLE-ADDON_PG_SLUG;
		$this->page_label = EEA-PEOPLE-ADDON_LABEL;
		$this->_admin_base_url = EEA_PEOPLE_ADDON_ADMIN_URL;
		$this->_admin_base_path = EEA_PEOPLE_ADDON_ADMIN;
	}




	protected function _ajax_hooks() {}





	protected function _define_page_props() {
		$this->_admin_page_title = EEA-PEOPLE-ADDON_LABEL;
		$this->_labels = array(
			'publishbox' => __('Update Settings', 'event_espresso')
		);
	}




	protected function _set_page_routes() {
		$this->_page_routes = array(
			'default' => '_basic_settings',
			'update_settings' => array(
				'func' => '_update_settings',
				'noheader' => TRUE
			),
			'usage' => '_usage'
		);
	}





	protected function _set_page_config() {

		$this->_page_config = array(
			'default' => array(
				'nav' => array(
					'label' => __('Settings', 'event_espresso'),
					'order' => 10
					),
				'metaboxes' => array_merge( $this->_default_espresso_metaboxes, array( '_publish_post_box') ),
				'require_nonce' => FALSE
			),
			'usage' => array(
				'nav' => array(
					'label' => __('eea-people-addon Usage', 'event_espresso'),
					'order' => 30
					),
				'require_nonce' => FALSE
			)
		);
	}


	protected function _add_screen_options() {}
	protected function _add_screen_options_default() {}
	protected function _add_feature_pointers() {}
	public function load_scripts_styles() {
		wp_register_script( 'eea-people-addon_admin', EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-people-addon_admin.js', array( 'espresso_core' ), EEA_PEOPLE_ADDON_VERSION, TRUE );
		wp_enqueue_script( 'eea-people-addon_admin');

		EE_Registry::$i18n_js_strings['confirm_reset'] = __( 'Are you sure you want to reset ALL your Event Espresso eea-people-addon Information? This cannot be undone.', 'event_espresso' );
		wp_localize_script( 'eea-people-addon_admin', 'eei18n', EE_Registry::$i18n_js_strings );
	}

	public function admin_init() {}
	public function admin_notices() {}
	public function admin_footer_scripts() {}






	protected function _basic_settings() {
		$this->_settings_page( 'eea-people-addon_basic_settings.template.php' );
	}




	/**
	 * _settings_page
	 * @param $template
	 */
	protected function _settings_page( $template ) {
		EE_Registry::instance()->load_helper( 'Form_Fields' );
		$this->_template_args['eea-people-addon_config'] = EE_Config::instance()->get_config( 'addons', 'EED_People', 'EE_People_Config' );
		add_filter( 'FHEE__EEH_Form_Fields__label_html', '__return_empty_string' );
		$this->_template_args['yes_no_values'] = array(
			EE_Question_Option::new_instance( array( 'QSO_value' => 0, 'QSO_desc' => __('No', 'event_espresso'))),
			EE_Question_Option::new_instance( array( 'QSO_value' => 1, 'QSO_desc' => __('Yes', 'event_espresso')))
		);

		$this->_template_args['return_action'] = $this->_req_action;
		$this->_template_args['reset_url'] = EE_Admin_Page::add_query_args_and_nonce( array('action'=> 'reset_settings','return_action'=>$this->_req_action), EEA_PEOPLE_ADDON_ADMIN_URL );
		$this->_set_add_edit_form_tags( 'update_settings' );
		$this->_set_publish_post_box_vars( NULL, FALSE, FALSE, NULL, FALSE);
		$this->_template_args['admin_page_content'] = EEH_Template::display_template( EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . $template, $this->_template_args, TRUE );
		$this->display_admin_page_with_sidebar();
	}


	protected function _usage() {
		$this->_template_args['admin_page_content'] = EEH_Template::display_template( EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'eea-people-addon_usage_info.template.php', array(), TRUE );
		$this->display_admin_page_with_no_sidebar();
	}

	protected function _update_settings(){
		EE_Registry::instance()->load_helper( 'Class_Tools' );
		if(isset($_POST['reset_eea-people-addon']) && $_POST['reset_eea-people-addon'] == '1'){
			$config = new EE_People_Config();
			$count = 1;
		}else{
			$config = EE_Config::instance()->get_config( 'addons', 'EED_People', 'EE_People_Config' );
			$count=0;
			//otherwise we assume you want to allow full html
			foreach($this->_req_data['eea-people-addon'] as $top_level_key => $top_level_value){
				if(is_array($top_level_value)){
					foreach($top_level_value as $second_level_key => $second_level_value){
						if ( EEH_Class_Tools::has_property( $config, $top_level_key ) && EEH_Class_Tools::has_property( $config->$top_level_key, $second_level_key ) && $second_level_value != $config->$top_level_key->$second_level_key ) {
							$config->$top_level_key->$second_level_key = $this->_sanitize_config_input( $top_level_key, $second_level_key, $second_level_value );
							$count++;
						}
					}
				}else{
					if ( EEH_Class_Tools::has_property($config, $top_level_key) && $top_level_value != $config->$top_level_key){
						$config->$top_level_key = $this->_sanitize_config_input($top_level_key, NULL, $top_level_value);
						$count++;
					}
				}
			}
		}
		EE_Config::instance()->update_config( 'addons', 'EED_People', $config );
		$this->_redirect_after_action( $count, 'Settings', 'updated', array('action' => $this->_req_data['return_action']));
	}

	/**
	 * resets the eea-people-addon data and redirects to where they came from
	 */
//	protected function _reset_settings(){
//		EE_Config::instance()->addons['eea-people-addon'] = new EE_People_Config();
//		EE_Config::instance()->update_espresso_config();
//		$this->_redirect_after_action(1, 'Settings', 'reset', array('action' => $this->_req_data['return_action']));
//	}
	private function _sanitize_config_input( $top_level_key, $second_level_key, $value ){
		$sanitization_methods = array(
			'display'=>array(
				'enable_eea-people-addon'=>'bool',
//				'eea-people-addon_height'=>'int',
//				'enable_eea-people-addon_filters'=>'bool',
//				'enable_category_legend'=>'bool',
//				'use_pickers'=>'bool',
//				'event_background'=>'plaintext',
//				'event_text_color'=>'plaintext',
//				'enable_cat_classes'=>'bool',
//				'disable_categories'=>'bool',
//				'show_attendee_limit'=>'bool',
			)
		);
		$sanitization_method = NULL;
		if(isset($sanitization_methods[$top_level_key]) &&
				$second_level_key === NULL &&
				! is_array($sanitization_methods[$top_level_key]) ){
			$sanitization_method = $sanitization_methods[$top_level_key];
		}elseif(is_array($sanitization_methods[$top_level_key]) && isset($sanitization_methods[$top_level_key][$second_level_key])){
			$sanitization_method = $sanitization_methods[$top_level_key][$second_level_key];
		}
//		echo "$top_level_key [$second_level_key] with value $value will be sanitized as a $sanitization_method<br>";
		switch($sanitization_method){
			case 'bool':
				return (boolean)intval($value);
			case 'plaintext':
				return wp_strip_all_tags($value);
			case 'int':
				return intval($value);
			case 'html':
				return $value;
			default:
				$input_name = $second_level_key == NULL ? $top_level_key : $top_level_key."[".$second_level_key."]";
				EE_Error::add_error(sprintf(__("Could not sanitize input '%s' because it has no entry in our sanitization methods array", "event_espresso"),$input_name), __FILE__, __FUNCTION__, __LINE__ );
				return NULL;

		}
	}





}
// End of file People_Admin_Page.core.php
// Location: /wp-content/plugins/eea-people-addon/admin/eea-people-addon/People_Admin_Page.core.php
