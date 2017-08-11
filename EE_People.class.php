<?php
defined('EVENT_ESPRESSO_VERSION') || exit();

// define the plugin directory path and URL
define( 'EEA_PEOPLE_ADDON_BASENAME', plugin_basename( EEA_PEOPLE_ADDON_PLUGIN_FILE ) );
define( 'EEA_PEOPLE_ADDON_PATH', plugin_dir_path( __FILE__ ) );
define( 'EEA_PEOPLE_ADDON_URL', plugin_dir_url( __FILE__ ) );
define( 'EEA_PEOPLE_ADDON_ADMIN', EEA_PEOPLE_ADDON_PATH . 'admin' . DS . 'people' . DS );



/**
 *
 * Main class setting up addon that hooks into EE_Plugin_API (EE_Addon)
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	plugin api
 * @author 		Darren Ethier
 */
Class  EE_People extends EE_Addon {




	public static function register_addon() {
		$people_capabilities = array(
			'ee_edit_people',
			'ee_read_people',
			'ee_read_peoples',
			'ee_read_others_peoples',
			'ee_delete_people',
			'ee_edit_peoples',
			'ee_edit_others_peoples',
			'ee_publish_peoples',
			'ee_read_private_peoples',
			'ee_delete_peoples',
			'ee_delete_private_peoples',
			'ee_delete_published_peoples',
			'ee_delete_others_peoples',
			'ee_edit_private_peoples',
			'ee_edit_published_peoples',
			'ee_manage_people_types',
			'ee_edit_people_type',
			'ee_delete_people_type',
			'ee_assign_people_type',
			'ee_manage_people_categories',
			'ee_edit_people_category',
			'ee_delete_people_category',
			'ee_assign_people_category',
		);

		// register addon via Plugin API
        EE_Register_Addon::register(
            'People',
            array(
                'version'               => EEA_PEOPLE_ADDON_VERSION,
                'min_core_version'      => '4.9.26.rc.000',
                'main_file_path'        => EEA_PEOPLE_ADDON_PLUGIN_FILE,
                'admin_path'            => EEA_PEOPLE_ADDON_ADMIN,
                'admin_callback'        => 'additional_admin_hooks',
                'config_class'          => 'EE_People_Config',
                'config_name'           => 'EE_People',
                'autoloader_paths'      => array(
                    'EE_People'              => EEA_PEOPLE_ADDON_PATH . 'EE_People.class.php',
                    'EE_People_Config'       => EEA_PEOPLE_ADDON_PATH . 'EE_People_Config.php',
                    'People_Admin_Page'      => EEA_PEOPLE_ADDON_ADMIN . 'People_Admin_Page.core.php',
                    'People_Admin_Page_Init' => EEA_PEOPLE_ADDON_ADMIN . 'People_Admin_Page_Init.core.php',
                ),
                'dms_paths'             => array(EEA_PEOPLE_ADDON_PATH . 'core' . DS . 'data_migration_scripts' . DS),
                'module_paths'          => array(
                    EEA_PEOPLE_ADDON_PATH . 'EED_People_Single.module.php',
                    EEA_PEOPLE_ADDON_PATH . 'EED_People_Event_Template_Parts.module.php',
                ),
                //'shortcode_paths' 	=> array( EEA_PEOPLE_ADDON_PATH . 'EES_Espresso_People.shortcode.php' ),
                //'widget_paths' 		=> array( EEA_PEOPLE_ADDON_PATH . 'EEW_eea-people-addon.widget.php' ),
                // if plugin update engine is being used for auto-updates. not needed if PUE is not being used.
                'pue_options'           => array(
                    'pue_plugin_slug' => 'eea-people-addon',
                    'plugin_basename' => EEA_PEOPLE_ADDON_BASENAME,
                    'checkPeriod'     => '24',
                    'use_wp_update'   => false,
                ),
                'capabilities'          => array(
                    'administrator'           => $people_capabilities,
                    'ee_events_administrator' => $people_capabilities,
                ),
                'capability_maps'       => array(
                    'EE_Meta_Capability_Map_Edit'   => array(
                        'ee_edit_people',
                        array(
                            'Person',
                            'ee_edit_published_peoples',
                            'ee_edit_others_peoples',
                            'ee_edit_private_peoples',
                        ),
                    ),
                    'EE_Meta_Capability_Map_Read'   => array(
                        'ee_read_people',
                        array(
                            'Person',
                            '',
                            'ee_read_others_peoples',
                            'ee_read_private_peoples',
                        ),
                    ),
                    'EE_Meta_Capability_Map_Delete' => array(
                        'ee_delete_people',
                        array(
                            'Person',
                            'ee_delete_published_peoples',
                            'ee_delete_others_peoples',
                            'ee_delete_private_peoples',
                        ),
                    ),
                ),
                'class_paths'           => EEA_PEOPLE_ADDON_PATH . 'core' . DS . 'db_classes',
                'model_paths'           => EEA_PEOPLE_ADDON_PATH . 'core' . DS . 'db_models',
                'model_extension_paths' => EEA_PEOPLE_ADDON_PATH . 'core' . DS . 'db_model_extensions',
                'custom_post_types'     => array(
                    'espresso_people' => array(
                        'singular_name' => __('Person', 'event_espresso'),
                        'plural_name'   => __('People', 'event_espresso'),
                        'singular_slug' => __('person', 'event_espresso'),
                        'plural_slug'   => __('people', 'event_espresso'),
                        'class_name'    => 'EE_Person',
                        'args'          => array(
                            'public'            => true,
                            'show_in_nav_menus' => true,
                            'capability_type'   => 'people',
                            'capabilities'      => array(
                                'edit_post'              => 'ee_edit_people',
                                'read_post'              => 'ee_read_people',
                                'delete_post'            => 'ee_delete_people',
                                'edit_posts'             => 'ee_edit_peoples',
                                'edit_others_posts'      => 'ee_edit_others_peoples',
                                'publish_posts'          => 'ee_publish_peoples',
                                'read_private_posts'     => 'ee_read_private_peoples',
                                'delete_posts'           => 'ee_delete_peoples',
                                'delete_private_posts'   => 'ee_delete_private_peoples',
                                'delete_published_posts' => 'ee_delete_published_peoples',
                                'delete_others_posts'    => 'ee_delete_others_peoples',
                                'edit_private_posts'     => 'ee_edit_private_peoples',
                                'edit_published_posts'   => 'ee_edit_published_peoples',
                            ),
                            'taxonomies'        => array(
                                'espresso_people_type',
                                'espresso_people_categories',
                            ),
                            'supports'          => array(
                                'title',
                                'editor',
                                'thumbnail',
                                'excerpt',
                                'custom-fields',
                                'comments',
                                'author',
                            ),
                        ),
                    ),
                ),
                'custom_taxonomies'     => array(
                    'espresso_people_type'       => array(
                        'singular_name' => __('People Type', 'event_espresso'),
                        'plural_name'   => __('People Types', 'event_espresso'),
                        'args'          => array(
                            'public'            => true,
                            'show_in_nav_menus' => true,
                            'labels'            => array(
                                'name'              => __('People Types', 'event_espresso'),
                                'singular_name'     => __('People Type', 'event_espresso'),
                                'add_new_item'      => __('Add New People Type', 'event_espresso'),
                                'new_item_name'     => __('New People Type Name', 'event_espresso'),
                                'parent_item'       => __('Parent People Type', 'event_espresso'),
                                'parent_item_colon' => __('Parent People Type:', 'event_espresso'),
                            ),
                            'capabilities'      => array(
                                'manage_terms' => 'ee_manage_people_types',
                                'edit_terms'   => 'ee_edit_people_type',
                                'delete_terms' => 'ee_delete_people_type',
                                'assign_terms' => 'ee_assign_people_type',
                            ),
                            'meta_box_cb'       => array(__CLASS__, 'people_type_metabox_content'),
                            'rewrite'           => array('slug' => __('people-type', 'event_espresso')),
                        ),
                    ),
                    'espresso_people_categories' => array(
                        'singular_name' => __('People Category', 'event_espresso'),
                        'plural_name'   => __('People Categories', 'event_espresso'),
                        'args'          => array(
                            'public'            => true,
                            'show_in_nav_menus' => true,
                            'capabilities'      => array(
                                'manage_terms' => 'ee_manage_people_categories',
                                'edit_terms'   => 'ee_edit_people_category',
                                'delete_terms' => 'ee_delete_people_category',
                                'assign_terms' => 'ee_assign_people_category',
                            ),
                            'rewrite'           => array('slug' => __('people-category', 'event_espresso')),
                        ),
                    ),
                ),
            )
        );
	}



    /**
     * a safe space for addons to add additional logic like setting hooks
     * that will run immediately after addon registration
     * making this a great place for code that needs to be "omnipresent"
     */
    public function after_registration()
    {
        //setting this SUPER late because EventSmart runs it's deregisters later as well.  This ensures that we are
        //running this hook well after any other plugins have possibly deregistered the addon.
        add_action(
            'AHEE__EE_System___detect_if_activation_or_upgrade__begin',
            array($this, 'load_early_hooks_when_registered'),
            1000
        );
    }



    /**
	 * Callback for 'AHEE__EE_System__load_espresso_addons' hook registered in constructor.
	 * This is to ensure that we load things necessary really early, but after this addon has been registered.  That way
	 * It can be determined whether the addon was registered successfully or not (and not deregistered) before executing.
	 *
	 * @throws \EE_Error
	 */
	public function load_early_hooks_when_registered() {

		if ( ! isset( EE_Registry::instance()->addons->EE_People) ) {
			return; //get out because the addon is not registered.
		}

		//filter extra paths
		add_filter( 'FHEE__EE_Registry__load_core__core_paths', array( $this, 'add_extra_core_paths' ), 10  );
		add_filter( 'FHEE__EE_Registry__load_helper__helper_paths', array( $this, 'add_extra_helper_paths' ), 10 );
		//add our templates folder to the EEH_Template::locate_template() paths checked.
		add_filter(
			'FHEE__EEH_Template__locate_template__template_folder_paths',
			array( $this, 'add_template_folder_to_paths' ),
			10
		);

		//make sure people addon nav menu metabox gets activated on fresh installs of wp
		add_filter(
			'FHEE__EE_Admin__enable_hidden_ee_nav_menu_boxes__initial_meta_boxes',
			array( $this, 'activate_people_nav_menu_options' ),
			10
		);

	}


	/**
	 * Callback for FHEE__EE_Admin__enable_hidden_ee_nav_menu_boxes__initial_meta_boxes to ensure that people addon
	 * nav menu metabox selector is activated by default in "Appearance->Menus" WP admin page.
	 *
	 * @param array $existing_activation_array existing array of default menu boxes activated.
	 * @return array
	 */
	public function activate_people_nav_menu_options( $existing_activation_array ) {
		$existing_activation_array[] = 'add-espresso_people';
		return $existing_activation_array;
	}



	/**
	 * Callback for the 'espresso_people_type' taxonomy metabox content.
	 *
	 * @param WP_Post $post
	 * @param array       $box  metabox args
	 *
	 * @return void
	 */
	public static function people_type_metabox_content( $post, $box ) {
		?>
		<div class="metabox-help-description">
			<p class="description"><?php _e('When you assign a person to a people type here, it just indicates that this person fulfills that role in your organization and this person will be listed on the archive page for that person type.', 'event_espresso' ); ?></p>
		</div>
		<?php post_categories_meta_box( $post, $box );
	}



	/**
	 * additional_admin_hooks
	 *
	 * @access 	public
	 * @return 	void
	 * @throws \EE_Error
	 */
	public function additional_admin_hooks() {
		// is admin and not in M-Mode ?
		if ( is_admin() && ! EE_Maintenance_Mode::instance()->level() ) {
			add_filter( 'plugin_action_links', array( $this, 'plugin_actions' ), 10, 2 );
		}
	}



	/**
	 * plugin_actions
	 *
	 * Add a settings link to the Plugins page, so people can go straight from the plugin page to the settings page.
	 * @param $links
	 * @param $file
	 * @return array
	 */
	public function plugin_actions( $links, $file ) {
		if ( $file === EEA_PEOPLE_ADDON_BASENAME ) {
			// before other links
			array_unshift( $links, '<a href="admin.php?page=espresso_people">' . __('Settings', 'event_espresso') . '</a>' );
		}
		return $links;
	}



	/**
	 * Add our cpt strategy path to the core paths array.
	 *
	 * @since 1.0.0
	 * @param array $core_paths incoming array of paths
	 * @return array
	 */
	public function add_extra_core_paths( $core_paths ) {
		$core_paths[] = EEA_PEOPLE_ADDON_PATH . 'core/CPTs/';
		return $core_paths;
	}


	/**
	 * Adds extra helper paths for when EE_Registry::instance()->load_helper() is called.
	 *
	 * @since 1.0.0
	 * @param array $helper_paths
	 * @return array
	 */
	public function add_extra_helper_paths( $helper_paths ) {
		$helper_paths[] = EEA_PEOPLE_ADDON_PATH . 'core/helpers/';
		return $helper_paths;
	}


	/**
	 * Registers the folder for core people templates to be included with the template path locator.
	 * Note: To customize, just copy the template from /public/templates/* and put in your theme folder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $template_paths incoming paths
	 * @return array
	 */
	public function add_template_folder_to_paths($template_paths) {
		$template_paths[] = EEA_PEOPLE_ADDON_PATH . 'public/templates/';
		return $template_paths;
	}



}
// End of file EE_People_Addon.class.php
// Location: wp-content/plugins/eea-people-addon/EE_People_Addon.class.php