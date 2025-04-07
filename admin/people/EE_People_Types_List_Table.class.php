<?php

use EventEspresso\core\services\admin\AdminListTableFilters;

/**
 * People Type List table class
 *
 * @since       1.0.0
 *
 * @package     EE People Addon
 * @subpackage  admin
 * @author      Darren Ethier
 * @property    People_Admin_Page $_admin_page
 */
class EE_People_Types_List_Table extends EE_Admin_List_Table
{
    private EE_Capabilities $capabilities;


    public function __construct(EE_Admin_Page $admin_page, ?AdminListTableFilters $filters = null)
    {
        $this->capabilities = EE_Registry::instance()->CAP;
        parent::__construct($admin_page, $filters);
    }


    /**
     * @throws EE_Error
     * @throws ReflectionException
     */
    protected function _setup_data()
    {
        $this->_data           = $this->_admin_page->get_terms(
            'espresso_people_type',
            $this->_per_page,
            $this->_current_page
        );
        $this->_all_data_count = EEM_Term_Taxonomy::instance()->count([['taxonomy' => 'espresso_people_type']]);
    }


    protected function _set_properties()
    {
        $this->_wp_list_args = [
            'singular' => esc_html__('people type', 'event_espresso'),
            'plural'   => esc_html__('people types', 'event_espresso'),
            'ajax'     => true, // for now,
            'screen'   => $this->_admin_page->get_current_screen()->id,
        ];

        $this->_columns = [
            'cb'    => '<input type="checkbox" />',
            'id'    => esc_html__('ID', 'event_espresso'),
            'name'  => esc_html__('Name', 'event_espresso'),
            // 'shortcode' => esc_html__('Shortcode', 'event_espresso'),
            'count' => esc_html__('People', 'event_espresso'),
        ];

        $this->_sortable_columns = [
            'id'    => ['Term.term_id' => true],
            'name'  => ['Term.slug' => false],
            'count' => ['term_count' => false],
        ];

        $this->_hidden_columns = [];

        EE_Registry::$i18n_js_strings['confirm_delete_people_type'] = esc_html__(
            'Are you sure you want to delete this person type? This action cannot be undone.',
            'event_espresso'
        );
    }


    // not needed
    protected function _get_table_filters()
    {
        return [];
    }


    protected function _add_view_counts()
    {
        $this->_views['all']['count'] = $this->_all_data_count;
    }


    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="PER_TYPE_ID[]" value="%s" />', $item->get('term_id'));
    }


    /**
     * @throws ReflectionException
     * @throws EE_Error
     */
    public function column_id(EE_Term_Taxonomy $item): string
    {
        return $item->get('term_id');
    }


    /**
     * @throws ReflectionException
     * @throws EE_Error
     */
    public function column_name(EE_Term_Taxonomy $item): string
    {
        $user_can_edit_people_type = $this->capabilities->current_user_can(
            'ee_edit_people_type',
            'espresso-people-type-list-table',
            $item->ID()
        );
        $user_can_delete_people_type = $this->capabilities->current_user_can(
            'ee_delete_people_type',
            'espresso-people-type-list-table',
            $item->ID()
        );

        // Build row actions
        $actions = [];
        $edit_link   = EE_Admin_Page::add_query_args_and_nonce(
            [
                'action'      => 'edit_type',
                'PER_TYPE_ID' => $item->get('term_id'),
            ],
            EEA_PEOPLE_ADDON_ADMIN_URL
        );

        if ($user_can_edit_people_type) {
            $actions['edit'] = '
            <a href="' . $edit_link . '" title="' . esc_html__('Edit Type', 'event_espresso') . '">
                ' . esc_html__('Edit', 'event_espresso') . '
            </a>';
        }

        $view_link   = get_term_link($item->get('term_id'), 'espresso_people_type');
        $actions['view'] = '
        <a href="' . $view_link . '" title="' . esc_attr__('View Type Archive', 'event_espresso') . '">
            ' . esc_html__('View', 'event_espresso') . '
        </a>';

        if ($user_can_delete_people_type) {
            $delete_link = EE_Admin_Page::add_query_args_and_nonce(
                [
                    'action'      => 'delete_type',
                    'PER_TYPE_ID' => $item->get('term_id'),
                ],
                EEA_PEOPLE_ADDON_ADMIN_URL
            );

            $actions['delete'] = '
            <a class="ee-requires-delete-confirmation"
               href="' . $delete_link . '"
               title="' . esc_html__('Delete Type', 'event_espresso') . '"
            >
                ' . esc_html__('Delete', 'event_espresso') . '
            </a>';
        }

        $term_link = $user_can_edit_people_type
            ? '
            <a class="row-title" href="' . $edit_link . '">
                ' . $item->get_first_related('Term')->get('name') . '
            </a>'
            : $item->get_first_related('Term')->get('name');

        return "<strong>$term_link</strong>" . $this->row_actions($actions);
    }


    /**
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function column_shortcode(EE_Term_Taxonomy $item): string
    {
        return '[ESPRESSO_PEOPLE type_slug="' . $item->get_first_related('Term')->get('slug') . '"]';
    }


    /**
     * @throws ReflectionException
     * @throws EE_Error
     */
    public function column_count(EE_Term_Taxonomy $item): string
    {
        $e_link  = EE_Admin_Page::add_query_args_and_nonce(
            [
                'action' => 'default',
                'type'   => $item->get_first_related('Term')->get('slug'),
            ],
            EEA_PEOPLE_ADDON_ADMIN_URL
        );
        return '<a href="' . $e_link . '">' . $item->get('term_count') . '</a>';
    }
}
