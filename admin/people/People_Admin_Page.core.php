<?php

use EventEspresso\core\domain\services\assets\CoreAssetManager;
use EventEspresso\core\services\request\DataType;

/**
 * People Admin Page class
 *
 * @since       1.0.0
 * @package     EE People Addon
 * @subpackage  admin
 * @author      Darren Ethier
 */
class People_Admin_Page extends EE_Admin_Page_CPT
{
    private int $PER_ID = 0;

    /**
     * @var EE_Person|EE_CPT_Base|null $_cpt_model_obj
     */
    protected $_cpt_model_obj;

    /**
     * Used to cache a WP_Term object
     * Will either be a espresso_people_category term OR a espresso_people_type term.
     *
     * @var WP_Term|null
     */
    protected ?WP_Term $_term_object = null;

    private EEM_Person $person_model;

    private EEM_Person_Post $person_post_model;


    /**
     * @throws ReflectionException
     * @throws EE_Error
     */
    public function __construct($routing = true)
    {
        $this->person_model      = EEM_Person::instance();
        $this->person_post_model = EEM_Person_Post::instance();
        parent::__construct($routing);
    }


    protected function _init_page_props()
    {
        $this->PER_ID = $this->request->getRequestParam('post', 0, DataType::INT);
        $this->PER_ID = $this->request->getRequestParam('post_ID', $this->PER_ID, DataType::INT);
        $this->PER_ID = $this->request->getRequestParam('PER_ID', $this->PER_ID, DataType::INT);

        $this->page_slug        = EEA_PEOPLE_PG_SLUG;
        $this->page_label       = esc_html__('Manage People', 'event_espresso');
        $this->_admin_base_url  = EEA_PEOPLE_ADDON_ADMIN_URL;
        $this->_admin_base_path = EEA_PEOPLE_ADDON_ADMIN;
        $this->_cpt_routes      = [
            'create_new'    => 'espresso_people',
            'edit'          => 'espresso_people',
            'insert_person' => 'espresso_people',
            'update_person' => 'espresso_people',
        ];
        $this->_cpt_model_names = [
            'create_new' => 'EEM_Person',
            'edit'       => 'EEM_Person',
        ];
        $this->_cpt_edit_routes = [
            'espresso_people' => 'edit',
        ];
        add_action('edit_form_after_title', [$this, 'after_title_form_fields']);
        add_filter('FHEE__EE_Admin_Page_CPT___edit_cpt_item__create_new_action', [$this, 'map_cpt_route'], 10, 2);
    }


    public function map_cpt_route(string $route, EE_Admin_Page $admin_page): string
    {
        if ($admin_page->page_slug == $this->page_slug && $route == 'create_new') {
            return 'create_new';
        }
        return $route;
    }


    /**
     * add in the form fields for the person first name/last name edit
     *
     * @param WP_Post $post wp post object
     * @return void
     */
    public function after_title_form_fields(WP_Post $post)
    {
        if ($post->post_type == 'espresso_people') {
            $template                =
                EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'people_details_after_title_form_fields.template.php';
            $template_args['people'] = $this->_cpt_model_obj;
            EEH_Template::display_template($template, $template_args);
        }
    }


    protected function _ajax_hooks()
    {
    }


    protected function _define_page_props()
    {
        $this->_admin_page_title = $this->page_label;
        $this->_labels           = [
            'buttons'      => [
                'add-person'      => esc_html__('Add Person', 'event_espresso'),
                'edit'            => esc_html__('Add Person', 'event_espresso'),
                'add_category'    => esc_html__('Add New Category', 'event_espresso'),
                'edit_category'   => esc_html__('Edit Category', 'event_espresso'),
                'delete_category' => esc_html__('Delete_Category', 'event_espresso'),
                'add_type'        => esc_html__('Add New Type', 'event_espresso'),
                'edit_type'       => esc_html__('Edit Type', 'event_espresso'),
                'delete_type'     => esc_html__('Delete Type', 'event_espresso'),
            ],
            'editor_title' => [
                'espresso_people' => esc_html__('Edit Person', 'event_espresso'),
            ],
            'publishbox'   => [
                'edit'          => esc_html__('Update Person Record', 'event_espresso'),
                'create_new'    => esc_html__('Save Person Record', 'event_espresso'),
                'add_category'  => esc_html__('Save New Category', 'event_espresso'),
                'edit_category' => esc_html__('Update Category', 'event_espresso'),
                'add_type'      => esc_html__('Save New Type', 'event_espresso'),
                'edit_type'     => esc_html__('Update Type', 'event_espresso'),
            ],
        ];
    }


    protected function _set_page_routes()
    {
        $this->_page_routes = [
            'default' => [
                'func'       => [$this, '_people_list_table'],
                'capability' => 'ee_read_peoples',
            ],

            'create_new' => [
                'func'       => [$this, '_create_new_cpt_item'],
                'capability' => 'ee_edit_peoples',
            ],

            'edit' => [
                'func'       => [$this, '_edit_cpt_item'],
                'capability' => 'ee_edit_people',
                'obj_id'     => $this->PER_ID,
            ],

            'trash_person' => [
                'func'       => [$this, '_trash_or_restore_people'],
                'args'       => ['trash' => true],
                'noheader'   => true,
                'capability' => 'ee_delete_people',
                'obj_id'     => $this->PER_ID,
            ],

            'trash_people' => [
                'func'       => [$this, '_trash_or_restore_people'],
                'args'       => [
                    'trash' => true,
                ],
                'noheader'   => true,
                'capability' => 'ee_delete_peoples',
            ],

            'restore_person' => [
                'func'       => [$this, '_trash_or_restore_people'],
                'args'       => ['trash' => false],
                'noheader'   => true,
                'capability' => 'ee_delete_people',
                'obj_id'     => $this->PER_ID,
            ],

            'restore_people'    => [
                'func'       => [$this, '_trash_or_restore_people'],
                'args'       => [
                    'trash' => false,
                ],
                'noheader'   => true,
                'capability' => 'ee_delete_peoples',
            ],

            // delete permanently
            'delete_person'     => [
                'func'       => [$this, '_delete_permanently_people'],
                'noheader'   => true,
                'capability' => 'ee_delete_people',
                'obj_id'     => $this->PER_ID,
            ],
            'delete_people'     => [
                'func'       => [$this, '_delete_permanently_people'],
                'noheader'   => true,
                'capability' => 'ee_delete_peoples',
            ],

            // categories
            'add_category'      => [
                'func'       => [$this, '_category_details'],
                'capability' => 'ee_edit_people_category',
                'args'       => ['add'],
            ],
            'edit_category'     => [
                'func'       => [$this, '_category_details'],
                'capability' => 'ee_edit_people_category',
                'args'       => ['edit'],
            ],
            'delete_categories' => [
                'func'       => [$this, '_delete_categories'],
                'capability' => 'ee_delete_people_category',
                'noheader'   => true,
            ],

            'delete_category' => [
                'func'       => [$this, '_delete_categories'],
                'capability' => 'ee_delete_people_category',
                'noheader'   => true,
            ],

            'insert_category' => [
                'func'       => [$this, '_insert_or_update_category'],
                'args'       => ['new_category' => true],
                'capability' => 'ee_edit_people_category',
                'noheader'   => true,
            ],

            'update_category' => [
                'func'       => [$this, '_insert_or_update_category'],
                'args'       => ['new_category' => false],
                'capability' => 'ee_edit_people_category',
                'noheader'   => true,
            ],
            'category_list'   => [
                'func'       => [$this, '_category_list_table'],
                'capability' => 'ee_manage_people_categories',
            ],

            // types
            'add_type'        => [
                'func'       => [$this, '_type_details'],
                'capability' => 'ee_edit_people_type',
                'args'       => ['add'],
            ],
            'edit_type'       => [
                'func'       => [$this, '_type_details'],
                'capability' => 'ee_edit_people_type',
                'args'       => ['edit'],
            ],
            'delete_types'    => [
                'func'       => [$this, '_delete_types'],
                'capability' => 'ee_delete_people_type',
                'noheader'   => true,
            ],

            'delete_type' => [
                'func'       => [$this, '_delete_types'],
                'capability' => 'ee_delete_people_type',
                'noheader'   => true,
            ],

            'insert_type' => [
                'func'       => [$this, '_insert_or_update_type'],
                'args'       => ['new_type' => true],
                'capability' => 'ee_edit_people_type',
                'noheader'   => true,
            ],

            'update_type' => [
                'func'       => [$this, '_insert_or_update_type'],
                'args'       => ['new_type' => false],
                'capability' => 'ee_edit_people_type',
                'noheader'   => true,
            ],
            'type_list'   => [
                'func'       => [$this, '_type_list_table'],
                'capability' => 'ee_manage_people_types',
            ],
        ];
    }


    protected function _set_page_config()
    {
        $PER_CAT_ID         = $this->request->getRequestParam('PER_CAT_ID', 0, DataType::INT);
        $PER_TYPE_ID        = $this->request->getRequestParam('PER_TYPE_ID', 0, DataType::INT);
        $this->_page_config = [
            'default'       => [
                'nav'           => [
                    'label' => esc_html__('People List', 'event_espresso'),
                    'order' => 5,
                ],
                'list_table'    => 'EE_People_List_Table',
                'metaboxes'     => [],
                'require_nonce' => false,
            ],
            'create_new'    => [
                'nav'           => [
                    'label'      => esc_html__('Add Person', 'event_espresso'),
                    'order'      => 10,
                    'persistent' => false,
                ],
                'metaboxes'     => ['people_editor_metaboxes'],
                'require_nonce' => false,
            ],
            'edit'          => [
                'nav'           => [
                    'label'      => esc_html__('Edit Person', 'event_espresso'),
                    'order'      => 10,
                    'persistent' => false,
                    'url'        => $this->PER_ID
                        ? add_query_arg(['post' => $this->PER_ID], $this->_current_page_view_url)
                        : $this->_admin_base_url,
                ],
                'metaboxes'     => ['people_editor_metaboxes'],
                'require_nonce' => false,
            ],

            // people category stuff
            'add_category'  => [
                'nav'       => [
                    'label'      => esc_html__('Add Category', 'event_espresso'),
                    'order'      => 10,
                    'persistent' => false,
                ],
                'help_tabs' => [
                    'add_category_help_tab' => [
                        'title'    => esc_html__('Add New People Category', 'event_espresso'),
                        'filename' => 'people_add_category',
                    ],
                ],
                'metaboxes' => ['_publish_post_box'],
            ],
            'edit_category' => [
                'nav'       => [
                    'label'      => esc_html__('Edit Category', 'event_espresso'),
                    'order'      => 10,
                    'persistent' => false,
                    'url'        => $PER_CAT_ID
                        ? add_query_arg(['PER_CAT_ID' => $PER_CAT_ID], $this->_current_page_view_url)
                        : $this->_admin_base_url,
                ],
                'help_tabs' => [
                    'edit_category_help_tab' => [
                        'title'    => esc_html__('Edit People Category', 'event_espresso'),
                        'filename' => 'people_edit_category',
                    ],
                ],
                'metaboxes' => ['_publish_post_box'],
            ],
            'category_list' => [
                'nav'           => [
                    'label' => esc_html__('Categories', 'event_espresso'),
                    'order' => 15,
                ],
                'list_table'    => 'EE_People_Categories_List_Table',
                'metaboxes'     => ['_espresso_news_post_box'],
                'require_nonce' => false,
            ],

            // people type stuff
            'add_type'      => [
                'nav'       => [
                    'label'      => esc_html__('Add Type', 'event_espresso'),
                    'order'      => 10,
                    'persistent' => false,
                ],
                'help_tabs' => [
                    'add_people_type_help_tab' => [
                        'title'    => esc_html__('Add People Type', 'event_espresso'),
                        'filename' => 'people_add_type',
                    ],
                ],
                'metaboxes' => ['_publish_post_box'],
            ],
            'edit_type'     => [
                'nav'       => [
                    'label'      => esc_html__('Edit Type', 'event_espresso'),
                    'order'      => 10,
                    'persistent' => false,
                    'url'        => $PER_TYPE_ID
                        ? add_query_arg(['PER_TYPE_ID' => $PER_TYPE_ID], $this->_current_page_view_url)
                        : $this->_admin_base_url,
                ],
                'help_tabs' => [
                    'edit_people_type_help_tab' => [
                        'title'    => esc_html__('Edit People Type', 'event_espresso'),
                        'filename' => 'people_edit_type',
                    ],
                ],
                'metaboxes' => ['_publish_post_box'],
            ],
            'type_list'     => [
                'nav'           => [
                    'label' => esc_html__('Types', 'event_espresso'),
                    'order' => 15,
                ],
                'list_table'    => 'EE_People_Types_List_Table',
                'metaboxes'     => ['_espresso_news_post_box'],
                'require_nonce' => false,
            ],
        ];
    }


    protected function _add_screen_options()
    {
    }


    protected function _add_screen_options_default()
    {
        $this->_per_page_screen_option();
    }


    protected function _add_feature_pointers()
    {
    }


    public function load_scripts_styles()
    {
    }


    public function load_scripts_styles_create_new()
    {
        $this->load_scripts_styles_edit();
    }


    public function load_scripts_styles_edit()
    {
        $asset_version = wp_get_environment_type() === 'production' ? EEA_PEOPLE_ADDON_VERSION : time();
        wp_enqueue_style(
            'eea-person-admin-css',
            EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-person-admin.css',
            ['ee-admin-css'],
            $asset_version
        );
        wp_enqueue_script(
            'eea-person-admin-js',
            EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-person-admin.js',
            ['post'],
            $asset_version
        );
    }


    public function load_scripts_styles_default()
    {
        wp_enqueue_style(
            'eea-person-admin-list-table-css',
            EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-person-admin-list-table.css',
            ['ee-admin-css'],
            wp_get_environment_type() === 'production' ? EEA_PEOPLE_ADDON_VERSION : time()
        );
    }


    public function load_scripts_styles_category_list()
    {
        wp_enqueue_script(
            'eea-person-admin-list-table-js',
            EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-person-admin-list-table.js',
            [CoreAssetManager::JS_HANDLE_CORE],
            wp_get_environment_type() === 'production' ? EEA_PEOPLE_ADDON_VERSION : time()
        );
    }


    public function load_scripts_styles_type_list()
    {
        wp_enqueue_script(
            'eea-person-admin-list-table-js',
            EEA_PEOPLE_ADDON_ADMIN_ASSETS_URL . 'eea-person-admin-list-table.js',
            [CoreAssetManager::JS_HANDLE_CORE],
            wp_get_environment_type() === 'production' ? EEA_PEOPLE_ADDON_VERSION : time()
        );
    }


    public function admin_init()
    {
    }


    public function admin_notices()
    {
    }


    public function admin_footer_scripts()
    {
    }


    protected function _set_list_table_views_default()
    {
        $this->_views = [
            'all'     => [
                'slug'        => 'all',
                'label'       => esc_html__('All', 'event_espresso'),
                'count'       => 0,
                'bulk_action' => [],
            ],
            'publish' => [
                'slug'        => 'publish',
                'label'       => esc_html__('Published', 'event_espresso'),
                'count'       => 0,
                'bulk_action' => [],
            ],
            'draft'   => [
                'slug'        => 'draft',
                'label'       => esc_html__('Draft', 'event_espresso'),
                'count'       => 0,
                'bulk_action' => [],
            ],
        ];

        if (
            EE_Registry::instance()->CAP->current_user_can(
                'ee_delete_peoples',
                'eea-people-addon_trash_people'
            )
        ) {
            $this->_views['trash']                  = [
                'slug'        => 'trash',
                'label'       => esc_html__('Trash', 'event_espresso'),
                'count'       => 0,
                'bulk_action' => [
                    'restore_people' => esc_html__('Restore from Trash', 'event_espresso'),
                    'delete_people'  => esc_html__('Delete Permanently', 'event_espresso'),
                ],
            ];
            $this->_views['all']['bulk_action']     = [
                'trash_people' => esc_html__('Move to Trash', 'event_espresso'),
            ];
            $this->_views['publish']['bulk_action'] = [
                'trash_people' => esc_html__('Move to Trash', 'event_espresso'),
            ];
            $this->_views['draft']['bulk_action']   = [
                'trash_people' => esc_html__('Move to Trash', 'event_espresso'),
            ];
        }
    }


    protected function _set_list_table_views_category_list()
    {
        $this->_views = [
            'all' => [
                'slug'        => 'all',
                'label'       => esc_html__('All', 'event_espresso'),
                'count'       => 0,
                'bulk_action' => [],
            ],
        ];

        if (
            EE_Registry::instance()->CAP->current_user_can(
                'ee_delete_people_category',
                'eea-people-addon-delete_category'
            )
        ) {
            $this->_views['all']['bulk_action'] = [
                'delete_categories' => esc_html__('Delete Permanently', 'event_espresso'),
            ];
        }
    }


    protected function _set_list_table_views_type_list()
    {
        $this->_views = [
            'all' => [
                'slug'        => 'all',
                'label'       => esc_html__('All', 'event_espresso'),
                'count'       => 0,
                'bulk_action' => [],
            ],
        ];

        if (
            EE_Registry::instance()->CAP->current_user_can(
                'ee_delete_people_type',
                'eea-people-addon-delete_type'
            )
        ) {
            $this->_views['all']['bulk_action'] = [
                'delete_types' => esc_html__('Delete Permanently', 'event_espresso'),
            ];
        }
    }


    /**
     * Set up page for people list table
     *
     * @return void
     * @throws EE_Error
     * @throws ReflectionException
     */
    protected function _people_list_table()
    {
        $this->_search_btn_label = esc_html__('People', 'event_espresso');
        $this->_admin_page_title .= ' ' . $this->get_action_link_or_button(
            'create_new',
            'add-person',
            [],
            'add-new-h2'
        );

        $EVT_ID = $this->request->getRequestParam('EVT_ID', 0, DataType::INT);
        if ($EVT_ID) {
            $event = EEM_Event::instance()->get_one_by_ID($EVT_ID);
            if ($event instanceof EE_Event) {
                $this->_template_args['before_list_table'] = '
                    <h2>
                        ' . sprintf(
                    esc_html__('Showing people assigned to the event: %s', 'event_espresso'),
                    $event->name()
                ) . '
                    </h2>';
            }
        }
        $this->_template_args['after_list_table'] = EEH_Template::get_button_or_link(
            get_post_type_archive_link('espresso_people'),
            esc_html__("View People Archive Page", "event_espresso"),
            'button'
        );
        $this->display_admin_list_table_page_with_no_sidebar();
    }


    /**
     * get people.
     *
     * @param int  $per_page number of people per page
     * @param bool $count    whether to return count or data.
     * @param bool $trash    whether to just return trashed or not.
     * @return EE_Person[]|int
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function get_people(int $per_page, bool $count = false, bool $trash = false)
    {
        $orderby      = $this->request->getRequestParam('orderby', 'PER_lname');
        $sort         = $this->request->getRequestParam('order', 'ASC');
        $current_page = $this->request->getRequestParam('paged', 1, DataType::INT);
        $per_page     = $per_page ?: 10;
        $per_page     = $this->request->getRequestParam('paged', $per_page, DataType::INT);

        $where = [];

        // determine what post status our condition will have for the query.
        $status = $this->request->getRequestParam('status');
        // determine what post_status our condition will have for the query.
        switch ($status) {
            case null:
            case 'all':
                break;

            case 'draft':
                $where['status'] = ['IN', ['draft', 'auto-draft']];
                break;

            default:
                $where['status'] = $status;
        }

        // possible conditions for capability checks
        if (! EE_Registry::instance()->CAP->current_user_can('ee_read_private_peoples', 'get_people')) {
            $where['status**'] = ['!=', 'private'];
        }

        if (! EE_Registry::instance()->CAP->current_user_can('ee_read_others_peoples', 'get_people')) {
            $where['PER_wp_user'] = get_current_user_id();
        }

        $EVT_ID = $this->request->getRequestParam('EVT_ID', 0, DataType::INT);
        if ($EVT_ID) {
            $where['Person_Post.OBJ_ID'] = $EVT_ID;
        }

        $people_type = $this->request->getRequestParam('type', '');
        if ($people_type) {
            $where['Term_Relationship.Term_Taxonomy.Term.slug'] = $people_type;
        }

        $people_category = $this->request->getRequestParam('category', '');
        if ($people_category) {
            $where['Term_Taxonomy.taxonomy']  = 'espresso_people_categories';
            $where['Term_Taxonomy.Term.slug'] = $people_category;
        }

        $search_term = $this->request->getRequestParam('s');
        if ($search_term) {
            $search_term = "%$search_term%";
            $where['OR'] = [
                'Event.EVT_name'       => ['LIKE', $search_term],
                'Event.EVT_desc'       => ['LIKE', $search_term],
                'Event.EVT_short_desc' => ['LIKE', $search_term],
                'PER_fname'            => ['LIKE', $search_term],
                'PER_lname'            => ['LIKE', $search_term],
                'PER_short_bio'        => ['LIKE', $search_term],
                'PER_email'            => ['LIKE', $search_term],
                'PER_address'          => ['LIKE', $search_term],
                'PER_address2'         => ['LIKE', $search_term],
                'PER_city'             => ['LIKE', $search_term],
                'Country.CNT_name'     => ['LIKE', $search_term],
                'State.STA_name'       => ['LIKE', $search_term],
                'PER_phone'            => ['LIKE', $search_term],
            ];
        }

        $offset = ($current_page - 1) * $per_page;
        $limit  = $count ? null : [$offset, $per_page];


        if ($trash) {
            $people = $count
                ? $this->person_model->count_deleted([$where, 'order_by' => [$orderby => $sort], 'limit' => $limit])
                : $this->person_model->get_all_deleted([$where, 'order_by' => [$orderby => $sort], 'limit' => $limit]);
        } else {
            $people = $count
                ? $this->person_model->count([$where, 'order_by' => [$orderby => $sort], 'limit' => $limit])
                : $this->person_model->get_all([$where, 'order_by' => [$orderby => $sort], 'limit' => $limit]);
        }

        return $people;
    }


    /**
     * Callback for cpt route insert/updates.  Runs on the "save_post" hook.
     *
     * @param int     $post_id Post id of item
     * @param WP_Post $post
     * @return void
     * @throws EE_Error
     * @throws ReflectionException
     * @since  1.0.0
     */
    protected function _insert_update_cpt_item($post_id, $post)
    {
        $person = $this->person_model->get_one_by_ID($post_id);

        // for people updates
        if ($post->post_type != 'espresso_people' || ! $person instanceof EE_Person) {
            return;
        }

        $first          = $this->request->getRequestParam('PER_fname');
        $last           = $this->request->getRequestParam('PER_lname');
        $updated_fields = [
            'PER_fname'     => $first,
            'PER_lname'     => $last,
            'PER_full_name' => "$first $last",
            'PER_address'   => $this->request->getRequestParam('PER_address'),
            'PER_address2'  => $this->request->getRequestParam('PER_address2'),
            'PER_city'      => $this->request->getRequestParam('PER_city'),
            'STA_ID'        => $this->request->getRequestParam('STA_ID'),
            'CNT_ISO'       => $this->request->getRequestParam('CNT_ISO'),
            'PER_zip'       => $this->request->getRequestParam('PER_zip'),
            'PER_email'     => $this->request->getRequestParam('PER_email'),
            'PER_phone'     => $this->request->getRequestParam('PER_phone'),
        ];
        foreach ($updated_fields as $field => $value) {
            $person->set($field, $value);
        }

        $success = $person->save();

        $people_update_callbacks = apply_filters('FHEE__People_Admin_Page__insert_update_cpt_item__people_update', []);
        foreach ($people_update_callbacks as $a_callback) {
            if (false === call_user_func_array($a_callback, [$person, $this->request->requestParams()])) {
                throw new EE_Error(
                    sprintf(
                        esc_html__(
                            'The %s callback given for the "FHEE__People_Admin_Page__insert_update_cpt_item__people_update" filter is not a valid callback.  Please check the spelling.',
                            'event_espresso'
                        ),
                        $a_callback
                    )
                );
            }
        }

        if ($success === false) {
            EE_Error::add_error(
                esc_html__(
                    'Something went wrong with updating the meta table data for the person.',
                    'event_espresso'
                ),
                __FILE__,
                __FUNCTION__,
                __LINE__
            );
        }
    }


    // unused cpt route callbacks
    public function trash_cpt_item($post_id)
    {
    }


    public function delete_cpt_item($post_id)
    {
    }


    public function restore_cpt_item($post_id)
    {
    }


    protected function _restore_cpt_item($post_id, $revision_id)
    {
    }


    /**
     * People Editor metabox registration
     *
     * @return void
     * @throws EE_Error
     * @throws ReflectionException
     * @since  1.0.0
     */
    public function people_editor_metaboxes()
    {
        $this->verify_cpt_object();

        if (! $this->_cpt_model_obj instanceof EE_Person) {
            return;
        }
        $screen = $this->_cpt_routes[ $this->_req_action ];
        $post   = $this->_cpt_model_obj->wp_post();
        remove_meta_box('postexcerpt', $screen, 'normal');
        remove_meta_box('commentstatusdiv', $screen, 'normal');

        if ($post instanceof WP_Post) {
            if (post_type_supports('espresso_people', 'excerpt')) {
                $this->addMetaBox(
                    'postexcerpt',
                    esc_html__('Short Biography', 'event_espresso'),
                    function () use ($post) {
                        post_excerpt_meta_box($post);
                    },
                    $screen
                );
            }

            if (post_type_supports('espresso_people', 'comments')) {
                $this->addMetaBox(
                    'commentsdiv',
                    esc_html__('Notes on the Person', 'event_espresso'),
                    function () use ($post) {
                        post_comment_meta_box($post);
                    },
                    $screen
                );
            }
        }

        $this->addMetaBox(
            'person_contact_info',
            esc_html__('Contact Info', 'event_espresso'),
            [$this, 'person_contact_info'],
            $screen,
            'side',
            'core'
        );

        $this->addMetaBox(
            'person_details_address',
            esc_html__('Address Details', 'event_espresso'),
            [$this, 'person_address_details'],
            $screen,
            'normal',
            'core'
        );

        // add event editor relationship
        $this->addMetaBox(
            'person_to_cpt_relationship',
            esc_html__('Where is this person assigned?', 'event_espresso'),
            [$this, 'person_to_cpt_details'],
            $screen,
            'normal',
            'core'
        );
    }


    /**
     * Metabox for person contact info
     *
     * @param WP_Post $post wp post object
     * @return void
     */
    public function person_contact_info(WP_Post $post)
    {
        // get attendee object ( should already have it )
        $this->_template_args['person'] = $this->_cpt_model_obj;

        $template = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'person_contact_info_metabox_content.template.php';
        EEH_Template::display_template($template, $this->_template_args);
    }


    /**
     * Metabox for person details
     *
     * @param WP_Post $post wp post object
     * @return void
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function person_address_details(WP_Post $post)
    {
        // get people object (should already have it)
        $this->_template_args['person']       = $this->_cpt_model_obj;
        $this->_template_args['state_html']   = EEH_Form_Fields::generate_form_input(
            new EE_Question_Form_Input(
                EE_Question::new_instance(
                    [
                        'QST_ID'           => 0,
                        'QST_display_text' => esc_html__('State/Province', 'event_espresso'),
                        'QST_system'       => 'admin-state',
                    ]
                ),
                EE_Answer::new_instance(
                    [
                        'ANS_ID'    => 0,
                        'ANS_value' => $this->_cpt_model_obj->state_ID(),
                    ]
                ),
                [
                    'input_id'       => 'STA_ID',
                    'input_name'     => 'STA_ID',
                    'input_prefix'   => '',
                    'append_qstn_id' => false,
                ]
            )
        );
        $this->_template_args['country_html'] = EEH_Form_Fields::generate_form_input(
            new EE_Question_Form_Input(
                EE_Question::new_instance(
                    [
                        'QST_ID'           => 0,
                        'QST_display_text' => esc_html__('Country', 'event_espresso'),
                        'QST_system'       => 'admin-country',
                    ]
                ),
                EE_Answer::new_instance(
                    [
                        'ANS_ID'    => 0,
                        'ANS_value' => $this->_cpt_model_obj->country_ID(),
                    ]
                ),
                [
                    'input_id'       => 'CNT_ISO',
                    'input_name'     => 'CNT_ISO',
                    'input_prefix'   => '',
                    'append_qstn_id' => false,
                ]
            )
        );

        $template = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'people_address_details_metabox_content.template.php';
        EEH_Template::display_template($template, $this->_template_args);
    }


    /**
     * Displays all the person relationships for this user.
     *
     * @param WP_Post $post
     * @return void
     * @throws EE_Error
     * @throws ReflectionException
     * @todo   Add filters.
     * @since  1.0.0
     * @todo   Add paging.
     */
    public function person_to_cpt_details(WP_Post $post)
    {
        // get all relationships for the given person
        $person_relationships = $this->person_post_model->get_all([['PER_ID' => $post->ID]]);

        // let's setup the row data for the rows.
        $row_data = [];
        foreach ($person_relationships as $person_relationship) {
            $cpt_obj = EE_Registry::instance()->load_model(
                $person_relationship->get('OBJ_type')
            )->get_one_by_ID(
                $person_relationship->get('OBJ_ID')
            );
            if ($cpt_obj instanceof EE_Base_Class) {
                if (! isset($row_data[ $cpt_obj->ID() ])) {
                    switch (get_class($cpt_obj)) {
                        case 'EE_Event':
                            $css_class = 'dashicons dashicons-calendar-alt';
                            $edit_link = add_query_arg(
                                [
                                    'page'   => 'espresso_events',
                                    'action' => 'edit',
                                    'post'   => $cpt_obj->ID(),
                                ],
                                admin_url('admin.php')
                            );
                            break;
                        case 'EE_Venue':
                            $css_class = 'ee-icon ee-icon-venue';
                            $edit_link = add_query_arg(
                                [
                                    'page'   => 'espresso_venues',
                                    'action' => 'edit',
                                    'post'   => $cpt_obj->ID(),
                                ],
                                admin_url('admin.php')
                            );
                            break;
                        case 'EE_Attendee':
                            $css_class = 'dashicons dashicons-admin-users';
                            $edit_link = add_query_arg(
                                [
                                    'page'   => 'espresso_registrations',
                                    'action' => 'edit_attendee',
                                    'post'   => $cpt_obj->ID(),
                                ],
                                admin_url('admin.php')
                            );
                            break;
                        default:
                            $css_class = '';
                            $edit_link = '';
                            break;
                    }
                    $row_data[ $cpt_obj->ID() ] = [
                        'css_class' => $css_class,
                        'cpt_type'  => strtolower($person_relationship->get('OBJ_type')),
                        'cpt_obj'   => $cpt_obj,
                        'edit_link' => $edit_link,
                        'ct_obj'    => [
                            EEM_Term_Taxonomy::instance()->get_one_by_ID(
                                $person_relationship->get('PT_ID')
                            ),
                        ],
                    ];
                } else {
                    // add other person types.
                    $row_data[ $cpt_obj->ID() ]['ct_obj'][] =
                        EEM_Term_Taxonomy::instance()->get_one_by_ID($person_relationship->get('PT_ID'));
                }
            }
        }

        // now we have row data so we can send that to the template
        $template_args = ['row_data' => $row_data];
        $template      = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'person_to_cpt_details_metabox_content.template.php';
        EEH_Template::display_template($template, $template_args);
    }


    /**
     * Trashing or restoring people.
     *
     * @param bool $trash true if trashing otherwise restoring
     * @return void
     * @throws EE_Error
     * @throws ReflectionException
     * @since  1.0.0
     */
    protected function _trash_or_restore_people(bool $trash = true)
    {
        $success = 1;

        // Checkboxes
        $checkboxes = $this->request->getRequestParam('checkbox', [], DataType::INT, true);
        if (! empty($checkboxes)) {
            // if array has more than one element than success message should be plural
            $success = count($checkboxes) > 1 ? 2 : 1;
            // cycle thru checkboxes
            foreach ($checkboxes as $PER_ID) {
                $updated = $trash
                    ? $this->person_model->delete_by_ID($PER_ID)
                    : $this->person_model->restore_by_ID($PER_ID);
                if (! $updated) {
                    $success = 0;
                }
            }
        } else {
            // get person
            $person  = $this->person_model->get_one_by_ID($this->PER_ID);
            $updated = $trash ? $person->delete() : $person->restore();
            $saved   = $person->save();
            if (! $updated && ! $saved) {
                $success = 0;
            }
        }

        $what        = $success > 1
            ? esc_html__('People', 'event_espresso')
            : esc_html__('Person', 'event_espresso');
        $action_desc = $trash
            ? esc_html__('moved to the trash', 'event_espresso')
            : esc_html__('restored', 'event_espresso');
        $this->_redirect_after_action($success, $what, $action_desc, ['action' => 'default']);
    }


    /**
     * Deletes permanently people (or person).
     *
     * @return void
     * @throws EE_Error
     * @throws ReflectionException
     * @since 1.0.0
     */
    protected function _delete_permanently_people()
    {
        $total_deleted     = 0;
        $total_not_deleted = 0;

        $checkboxes = $this->request->getRequestParam('checkbox', [], DataType::INT, true);
        if (! empty($checkboxes)) {
            // cycle thru checkboxes
            foreach ($checkboxes as $PER_ID) {
                // first delete any relationships with other posts for this id.
                $this->person_post_model->delete([['PER_ID' => $PER_ID]]);

                // delete any term_taxonomy_relationships (gonna use wp core functions cause it's likely a bit faster)
                wp_delete_object_term_relationships($PER_ID, ['espresso_people_type', 'espresso_people_categories']);

                // now should be able to delete permanently with no issues.
                $deleted = $this->person_model->delete_permanently_by_ID($PER_ID, false);
                if ($deleted) {
                    $total_deleted++;
                } else {
                    $total_not_deleted++;
                }
            }
        } else {
            if (empty($this->PER_ID)) {
                EE_Error::add_error(
                    esc_html__(
                        'Unable to delete permanently the selected Person because no ID was given.',
                        'event_espresso'
                    ),
                    __FILE__,
                    __FUNCTION__,
                    __LINE__
                );
                $total_not_deleted++;
            }

            // first delete any relationships with other posts for this id.
            $this->person_post_model->delete([['PER_ID' => $this->PER_ID]]);

            // delete any term_taxonomy_relationships (gonna use wp core functions cause it's likely a bit faster)
            wp_delete_object_term_relationships($this->PER_ID, ['espresso_people_type', 'espresso_people_categories']);

            $deleted = $this->person_model->delete_permanently_by_ID($this->PER_ID, false);
            if ($deleted) {
                $total_deleted++;
            } else {
                $total_not_deleted++;
            }
        }

        if ($total_deleted > 0) {
            EE_Error::add_success(
                sprintf(
                    _n(
                        '1 Person successfully deleted.',
                        '%s People successfully deleted.',
                        $total_deleted,
                        'event_espresso'
                    ),
                    $total_deleted
                )
            );
        }

        if ($total_not_deleted > 0) {
            EE_Error::add_error(
                sprintf(
                    _n(
                        '1 Person not deleted.',
                        '%d People not deleted',
                        $total_not_deleted,
                        'event_espresso'
                    ),
                    $total_not_deleted
                ),
                __FILE__,
                __FUNCTION__,
                __LINE__
            );
        }
        $this->_redirect_after_action($total_deleted > 0, '', '', ['action' => 'default'], true);
    }




    ####################
    # PEOPLE CATEGORY AND TYPE STUFF
    # ##################


    /**
     * set the _term_object property with the term object for the loaded page.
     *
     * @return void
     */
    private function _set_term_object($taxonomy = 'espresso_people_categories')
    {
        if (isset($this->_term_object->id) && ! empty($this->_term_object->id)) {
            return; // already have the term object so get out.
        }

        // set default term object
        $this->_set_empty_term_object();

        $term_id = $taxonomy == 'espresso_people_categories'
            ? $this->request->getRequestParam('PER_CAT_ID', 0, DataType::INT)
            : $this->request->getRequestParam('PER_TYPE_ID', 0, DataType::INT);

        // only set if we've got an id
        if (! $term_id) {
            return;
        }

        $term = get_term($term_id, $taxonomy);
        if (! empty($term)) {
            $this->_term_object                      = $term;
            $this->_term_object->category_name       = $term->name;
            $this->_term_object->category_identifier = $term->slug;
            $this->_term_object->category_desc       = $term->description;
            $this->_term_object->id                  = $term->term_id;
        }
    }


    private function _set_empty_term_object()
    {
        $term_object                      = new stdClass();
        $term_object->name                = '';
        $term_object->category_name       = '';
        $term_object->slug                = '';
        $term_object->category_identifier = '';
        $term_object->description         = '';
        $term_object->category_desc       = '';
        $term_object->id                  = 0;
        $term_object->term_id             = 0;
        $term_object->parent              = 0;
        $this->_term_object               = new WP_Term($term_object);
    }


    /**
     * @throws EE_Error
     */
    protected function _category_list_table()
    {
        do_action('AHEE_log', __FILE__, __FUNCTION__, '');
        $this->_search_btn_label = esc_html__('Categories', 'event_espresso');
        $this->_admin_page_title .= ' ' . $this->get_action_link_or_button(
            'add_category',
            'add_category',
            [],
            'add-new-h2'
        );
        $this->display_admin_list_table_page_with_sidebar();
    }


    /**
     * @throws EE_Error
     */
    protected function _type_list_table()
    {
        $this->_search_btn_label = esc_html__('Types', 'event_espresso');
        $this->_admin_page_title .= ' ' . $this->get_action_link_or_button(
            'add_type',
            'add_type',
            [],
            'add-new-h2'
        );
        $this->display_admin_list_table_page_with_sidebar();
    }


    /**
     * @throws EE_Error
     */
    protected function _output_details($view, $taxonomy = 'espresso_people_categories')
    {
        $slug = $taxonomy == 'espresso_people_categories' ? 'category' : 'type';

        $route = $view == 'edit' ? 'update_' . $slug : 'insert_' . $slug;
        $this->_set_add_edit_form_tags($route);

        $this->_set_term_object($taxonomy);
        $id = ! empty($this->_term_object->id) ? $this->_term_object->id : '';

        // custom redirect
        $redirect = EE_Admin_Page::add_query_args_and_nonce(['action' => $slug . '_list'], $this->_admin_base_url);

        $id_ident = $taxonomy == 'espresso_people_categories' ? 'PER_CAT_ID' : 'PER_TYPE_ID';

        $this->_set_publish_post_box_vars($id_ident, $id, "delete_$slug", $redirect);

        // take care of contents
        $this->_template_args['admin_page_content'] = $this->_term_object_details_content($taxonomy);
        $this->display_admin_page_with_sidebar();
    }


    /**
     * @throws EE_Error
     */
    protected function _category_details($view)
    {
        $this->_output_details($view);
    }


    /**
     * @throws EE_Error
     */
    protected function _type_details($view)
    {
        $this->_output_details($view, 'espresso_people_type');
    }


    protected function _term_object_details_content($taxonomy = 'espresso_people_categories'): string
    {
        $editor_args['category_desc'] = [
            'type'          => 'wp_editor',
            'value'         => EEH_Formatter::admin_format_content($this->_term_object->category_desc),
            'class'         => 'my_editor_custom',
            'wpeditor_args' => ['media_buttons' => false],
        ];
        $_wp_editor                   = $this->_generate_admin_form_fields($editor_args, 'array');

        $all_terms = get_terms([$taxonomy], ['hide_empty' => 0, 'exclude' => [$this->_term_object->id]]);

        // setup category select for term parents.
        $category_select_values[] = [
            'text' => esc_html__('No Parent', 'event_espresso'),
            'id'   => 0,
        ];
        foreach ($all_terms as $term) {
            $category_select_values[] = [
                'text' => $term->name,
                'id'   => $term->term_id,
            ];
        }

        $category_select =
            EEH_Form_Fields::select_input('category_parent', $category_select_values, $this->_term_object->parent);

        $template_args = [
            'category'                 => $this->_term_object,
            'category_select'          => $category_select,
            'unique_id_info_help_link' => $this->_get_help_tab_link('unique_id_info'),
            'category_desc_editor'     => $_wp_editor['category_desc']['field'],
            'disable'                  => '',
            'disabled_message'         => false,
            'term_name_label'          => $taxonomy == 'espresso_people_categories'
                ? esc_html__('Category Name', 'event_espresso')
                : esc_html__('Type Name', 'event_espresso'),
            'term_id_description'      => $taxonomy == 'espresso_people_categories'
                ? esc_html__(
                    'This is a default category so you can edit the label and the description but not the slug',
                    'event_espresso'
                )
                : esc_html__(
                    'This is a default type so you can edit the label and the description but not the slug',
                    'event_espresso'
                ),
            'term_parent_label'        => $taxonomy == 'espresso_people_categories'
                ? esc_html__('Category Parent', 'event_espresso')
                : esc_html__('Type Parent', 'event_espresso'),
            'term_parent_description'  => $taxonomy == 'espresso_people_categories'
                ? esc_html__(
                    'Categories are hierarchical.  You can change the parent for this category here.',
                    'event_espresso'
                )
                : esc_html__(
                    'People Types are hierarchical.  You can change the parent for this type here.',
                    'event_espresso'
                ),
            'term_description_label'   => $taxonomy == 'espresso_people_categories'
                ? esc_html__('Category Description', 'event_espresso')
                : esc_html__('Type Description', 'event_espresso'),
        ];
        $template      = EEA_PEOPLE_ADDON_ADMIN_TEMPLATE_PATH . 'people_term_details.template.php';
        return EEH_Template::display_template($template, $template_args, true);
    }


    /**
     * @throws EE_Error
     */
    protected function _delete_terms($taxonomy = 'espresso_people_categories')
    {
        if ($taxonomy == 'espresso_people_categories') {
            $term_ids = $this->request->getRequestParam('PER_CAT_ID', [], DataType::INT, true);
            $term_ids = $this->request->getRequestParam('category_id', $term_ids, DataType::INT, true);
        } else {
            $term_ids = $this->request->getRequestParam('PER_TYPE_ID', [], DataType::INT, true);
            $term_ids = $this->request->getRequestParam('type_id', $term_ids, DataType::INT, true);
        }

        foreach ($term_ids as $term_id) {
            $this->_delete_term($taxonomy, $term_id);
        }

        // doesn't matter what page we're coming from... we're going to the same place after delete.
        $query_args = [
            'action' => $taxonomy == 'espresso_people_categories' ? 'category_list' : 'type_list',
        ];
        $this->_redirect_after_action(0, '', '', $query_args);
    }


    /**
     * @throws EE_Error
     */
    protected function _delete_categories()
    {
        $this->_delete_terms();
    }


    /**
     * @throws EE_Error
     */
    protected function _delete_types()
    {
        $this->_delete_terms('espresso_people_type');
    }


    protected function _delete_term($taxonomy, $term_id)
    {
        wp_delete_term(absint($term_id), $taxonomy);
    }


    /**
     * @throws EE_Error
     */
    protected function _insert_or_update_term($new_term, $taxonomy = 'espresso_people_categories')
    {
        $term_id    = $new_term ? $this->_insert_term(false, $taxonomy) : $this->_insert_term(true, $taxonomy);
        $success    = 0; // we already have a success message so lets not send another.
        $id_ident   = $taxonomy == 'espresso_people_categories' ? 'PER_CAT_ID' : 'PER_TYPE_ID';
        $slug       = $taxonomy == 'espresso_people_categories' ? 'category' : 'type';
        $query_args = [
            'action'  => 'edit_' . $slug,
            $id_ident => $term_id,
        ];
        $this->_redirect_after_action($success, '', '', $query_args, true);
    }


    /**
     * @throws EE_Error
     */
    protected function _insert_or_update_category($new_category)
    {
        $this->_insert_or_update_term($new_category);
    }


    /**
     * @throws EE_Error
     */
    protected function _insert_or_update_type($new_type)
    {
        $this->_insert_or_update_term($new_type, 'espresso_people_type');
    }


    private function _insert_term($update = false, $taxonomy = 'espresso_people_categories')
    {
        $term_id = $taxonomy === 'espresso_people_categories'
            ? $this->request->getRequestParam('PER_CAT_ID', 0, DataType::INT)
            : $this->request->getRequestParam('PER_TYPE_ID', 0, DataType::INT);

        $category_id     = $this->request->getRequestParam('category_identifier');
        $category_name   = $this->request->getRequestParam('category_name');
        $category_desc   = $this->request->getRequestParam('category_desc', '', DataType::HTML);
        $category_parent = $this->request->getRequestParam('category_parent', 0, DataType::INT);

        $term_args = [
            'name'        => $category_name,
            'description' => $category_desc,
            'parent'      => $category_parent,
        ];
        // was the category_identifier input disabled?
        if ($category_id) {
            $term_args['slug'] = $category_id;
        }

        $insert_ids = $update
            ? wp_update_term($term_id, $taxonomy, $term_args)
            : wp_insert_term(
                $category_name,
                $taxonomy,
                $term_args
            );

        if ($insert_ids instanceof WP_Error) {
            $term_type = $taxonomy === 'espresso_people_categories'
                ? esc_html__('category', 'event_espresso')
                : esc_html__('people type', 'event_espresso');

            EE_Error::add_error(
                sprintf(
                /* translators: 1: Term type: 'category' or 'people type' 2: <br /> 3: WP error message. */
                    esc_html__(
                        'The %1$s has not been saved to the database because of the following error: %2$s %3$s',
                        'event_espresso'
                    ),
                    $term_type,
                    '<br />',
                    $insert_ids->get_error_message()
                ),
                __FILE__,
                __FUNCTION__,
                __LINE__
            );
            return $term_id;
        }

        $term_id = $insert_ids['term_id'];
        $msg     = $taxonomy === 'espresso_people_categories'
            ? sprintf(
                esc_html__('The category %s was successfully saved', 'event_espresso'),
                $category_name
            )
            : sprintf(__('The people type %s was successfully saved', 'event_espresso'), $category_name);
        EE_Error::add_success($msg);

        return $term_id;
    }


    /**
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function get_terms(
        $taxonomy = 'espresso_people_categories',
        $per_page = 10,
        $current_page = 1,
        $count = false
    ) {
        // testing term stuff
        $orderby      = $this->request->getRequestParam('orderby', 'Term.term_id');
        $order        = $this->request->getRequestParam('order', 'DESC');
        $current_page = $this->request->getRequestParam('paged', $current_page, DataType::INT);
        $per_page     = $per_page ?? 10;
        $per_page     = $this->request->getRequestParam('paged', $per_page, DataType::INT);
        $limit        = ($current_page - 1) * $per_page;
        $where        = ['taxonomy' => $taxonomy];

        $search_term = $this->request->getRequestParam('s');
        if ($search_term) {
            $search_term = "%$search_term%";
            $where['OR'] = [
                'Term.name'   => ['LIKE', $search_term],
                'description' => ['LIKE', $search_term],
            ];
        }

        $query_params = [
            $where,
            'order_by'   => [$orderby => $order],
            'limit'      => $limit . ',' . $per_page,
            'force_join' => ['Term'],
        ];

        return $count
            ? EEM_Term_Taxonomy::instance()->count($query_params, 'term_id')
            : EEM_Term_Taxonomy::instance()->get_all($query_params);
    }


    /**
     * @param string                 $box_id
     * @param string                 $title
     * @param callable|string|null   $callback
     * @param string|array|WP_Screen $screen
     * @param string                 $context
     * @param string                 $priority
     * @param array|null             $callback_args
     */
    protected function addMetaBox(
        string $box_id,
        string $title,
        $callback,
        $screen,
        string $context = 'normal',
        string $priority = 'default',
        ?array $callback_args = null
    ) {
        if (! (is_callable($callback) || ! function_exists($callback))) {
            return;
        }

        add_meta_box($box_id, $title, $callback, $screen, $context, $priority, $callback_args);
        add_filter(
            "postbox_classes_{$this->_wp_page_slug}_$box_id",
            function ($classes) {
                $classes[] = 'ee-admin-container';
                return $classes;
            }
        );
    }
}
