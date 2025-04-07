<?php

use EventEspresso\core\services\admin\AdminListTableFilters;
use EventEspresso\core\services\loaders\LoaderFactory;
use EventEspresso\core\services\request\RequestInterface;

/**
 * People Admin List Table class
 *
 * @package     EE People Addon
 * @subpackage  admin
 * @author      Darren Ethier
 * @since       1.0.0
 * @property    People_Admin_Page $_admin_page
 */
class EE_People_List_Table extends EE_Admin_List_Table
{
    private EE_Capabilities $capabilities;

    private EEM_Person $person_model;

    /**
     * @since 1.0.11
     */
    private string $category = '';

    /**
     * @since 1.0.11
     */
    private string $type = '';


    /**
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function __construct(EE_Admin_Page $admin_page, ?AdminListTableFilters $filters = null)
    {
        $this->capabilities = EE_Registry::instance()->CAP;
        $this->person_model = EEM_Person::instance();
        $this->request      = $this->request ?? LoaderFactory::getShared(RequestInterface::class);
        $this->setupFilters();
        parent::__construct($admin_page, $filters);
    }


    private function setupFilters()
    {
        $this->category = $this->request->getRequestParam('category', '');
        $this->type     = $this->request->getRequestParam('type', '');
        add_filter(
            'FHEE__EE_Admin_Page__get_list_table_view_RLs__extra_query_args',
            [$this, 'filterExtraQueryArgs'],
            10,
            2
        );
    }


    /**
     * @param array         $extra_query_args
     * @param EE_Admin_Page $admin_page
     * @return array
     * @since 5.0.13.p
     */
    public function filterExtraQueryArgs(array $extra_query_args, EE_Admin_Page $admin_page): array
    {
        if ($admin_page instanceof Registrations_Admin_Page) {
            foreach ($admin_page->get_views() as $view_details) {
                if ($this->type) {
                    $extra_query_args[ $view_details['slug'] ]['type'] = $this->type;
                } else {
                    unset($extra_query_args[ $view_details['slug'] ]['type']);
                }
                if ($this->category) {
                    $extra_query_args[ $view_details['slug'] ]['category'] = $this->category;
                } else {
                    unset($extra_query_args[ $view_details['slug'] ]['category']);
                }
            }
        }
        return $extra_query_args;
    }


    /**
     * @throws ReflectionException
     * @throws EE_Error
     */
    protected function _setup_data()
    {
        $this->_data           = $this->_view !== 'trash'
            ? $this->_admin_page->get_people($this->_per_page)
            : $this->_admin_page->get_people(
                $this->_per_page,
                false,
                true
            );
        $this->_all_data_count = $this->_view !== 'trash'
            ? $this->_admin_page->get_people($this->_per_page, true)
            : $this->_admin_page->get_people($this->_per_page, true, true);
    }


    protected function _set_properties()
    {
        $this->_wp_list_args = [
            'singular' => esc_html__('person', 'event_espresso'),
            'plural'   => esc_html__('people', 'event_espresso'),
            'ajax'     => true,
            'screen'   => $this->_admin_page->get_current_screen()->id,
        ];

        $this->_columns = [
            'cb'              => '<input type="checkbox" />', // Render a checkbox instead of text
            'PER_ID'          => esc_html__('ID', 'event_espresso'),
            'FET_Image'       => esc_html__('Picture', 'event_espresso'),
            'PER_lname'       => esc_html__('Name', 'event_espresso'),
            'PER_address'     => esc_html__('Address', 'event_espresso'),
            'PER_event_types' => esc_html__('Assigned As On Events', 'event_espresso'),
        ];

        $this->_sortable_columns = [
            'PER_ID'    => ['PER_ID' => false],
            'PER_lname' => ['PER_lname' => true], // true means its already sorted
        ];

        $this->_hidden_columns = [];
    }


    protected function _get_table_filters()
    {
        return [];
    }


    /**
     * @throws EE_Error
     * @throws ReflectionException
     */
    protected function _add_view_counts()
    {
        $orig_status = $this->_req_data['status'] ?? null;
        // change status to publish so we can get the count of published people
        $this->_req_data['status']        = 'publish';
        $this->_views['publish']['count'] = $this->person_model->count([['status' => 'publish']]);
        if ($this->capabilities->current_user_can('ee_delete_peoples', 'eea-people-addon_delete_people')) {
            $this->_views['trash']['count'] = $this->person_model->count_deleted();
        }
        // change status to draft so we can get the count of draft people
        $this->_req_data['status']      = 'draft';
        $this->_views['draft']['count'] = $this->person_model->count([['status' => 'draft']]);
        // change status to all so we can get the count of all people
        $this->_req_data['status']    = 'all';
        $this->_views['all']['count'] = $this->person_model->count([['status' => ['NOT_IN', ['trash']]]]);
        // reset status to original value
        $this->_req_data['status'] = $orig_status;
    }


    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="checkbox[%1$s]" value="%1$s" />', /* $1%s */ $item->ID());
    }


    /**
     * @throws ReflectionException
     * @throws EE_Error
     */
    public function column_PER_ID(EE_Person $item): string
    {
        return "
        {$item->ID()}
        <span class=\"show-on-mobile-view-only\"> {$item->feature_image([36, 36])} {$item->full_name()}</span>";
    }


    public function column_FET_Image(EE_Person $item): string
    {
        return $item->feature_image([56, 56]);
    }


    /**
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function column_PER_lname(EE_Person $item): string
    {
        // Build row actions
        $actions = [];
        // edit person link
        if (
            $this->capabilities->current_user_can(
                'ee_edit_people',
                'eea-people-addon_edit_people',
                $item->ID()
            )
        ) {
            $edit_lnk_url    = EE_Admin_Page::add_query_args_and_nonce(
                ['action' => 'edit', 'post' => $item->ID()],
                EEA_PEOPLE_ADDON_ADMIN_URL
            );
            $actions['edit'] = '
            <a href="' . $edit_lnk_url . '" title="' . esc_html__('Edit Person', 'event_espresso') . '">
                ' . esc_html__('Edit', 'event_espresso') . '
            </a>';
        }

        if ($this->_view == 'publish' || $this->_view == 'all' || $this->_view == 'draft') {
            // trash person link
            if (
                $this->capabilities->current_user_can(
                    'ee_delete_people',
                    'eea-people-addon_trash_people',
                    $item->ID()
                )
            ) {
                $trash_lnk_url    = EE_Admin_Page::add_query_args_and_nonce(
                    ['action' => 'trash_person', 'PER_ID' => $item->ID()],
                    EEA_PEOPLE_ADDON_ADMIN_URL
                );
                $actions['trash'] = '
                <a href="' . $trash_lnk_url . '"
                   title="' . esc_html__('Move Person to Trash', 'event_espresso') . '"
                >
                    ' . esc_html__('Trash', 'event_espresso') . '
                </a>';
            }
        } else {
            if (
                $this->capabilities->current_user_can(
                    'ee_delete_people',
                    'eea-people-addon_restore_people',
                    $item->ID()
                )
            ) {
                // restore person link
                $restore_lnk_url    = EE_Admin_Page::add_query_args_and_nonce(
                    ['action' => 'restore_person', 'PER_ID' => $item->ID()],
                    EEA_PEOPLE_ADDON_ADMIN_URL
                );
                $actions['restore'] = '
                    <a href="' . $restore_lnk_url . '"
                       title="' . esc_html__('Restore Person', 'event_espresso') . '"
                    >
                        ' . esc_html__('Restore', 'event_espresso') . '
                    </a>';
                // delete person link
                $delete_lnk_url    = EE_Admin_Page::add_query_args_and_nonce(
                    ['action' => 'delete_person', 'PER_ID' => $item->ID()],
                    EEA_PEOPLE_ADDON_ADMIN_URL
                );
                $actions['delete'] = '
                    <a href="' . $delete_lnk_url . '"
                       title="' . esc_html__('Delete Permanently This Person.', 'event_espresso') . '"
                    >
                        ' . esc_html__('Delete Permanently', 'event_espresso') . '
                    </a>';
            }
        }

        $edit_lnk_url = EE_Admin_Page::add_query_args_and_nonce(
            ['action' => 'edit', 'post' => $item->ID()],
            EEA_PEOPLE_ADDON_ADMIN_URL
        );
        $name_link    = $this->capabilities->current_user_can(
            'ee_edit_people',
            'eea-people-addon_edit_people',
            $item->ID()
        )
            ? '
            <a href="' . $edit_lnk_url . '"
               title="' . esc_html__('Edit Person', 'event_espresso') . '"
            >
                ' . $item->full_name() . '
            </a>'
            : $item->full_name();

        $email_addy = $item->email();
        $name_link  .= $email_addy ? '<br>' . '<a href="mailto:' . $email_addy . '">' . $email_addy . '</a>' : '';
        $phone_nmbr = $item->phone();
        $name_link  .= $phone_nmbr ? '<br>' . sprintf(__('Phone: %s', 'event_espresso'), $phone_nmbr) : '';

        // Return the name contents
        return $name_link . $this->row_actions($actions);
    }


    /**
     * @throws ReflectionException
     * @throws EE_Error
     */
    public function column_PER_address(EE_Person $item): string
    {
        EE_Registry::instance()->load_helper('Formatter');
        return EEH_Address::format($item);
    }


    /**
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function column_PER_event_types(EE_Person $item)
    {
        EE_Registry::instance()->load_helper('URL');
        // first do a query to get all the types for this user for where they are assigned to an event.
        $event_type_IDs = EEM_Person_Post::instance()->get_col(
            [
                [
                    'PER_ID'   => $item->ID(),
                    'OBJ_type' => 'Event',
                ],
            ],
            'PT_ID'
        );

        $event_types = EEM_Term_Taxonomy::instance()->get_all(
            [
                [
                    'term_taxonomy_id' => ['IN', $event_type_IDs],
                ],
            ]
        );

        // loop through the types and set up the pills and the links
        $content = '
        <ul class="person-to-cpt-people-type-list">';
        foreach ($event_types as $type) {
            $name  = $type->get_first_related('Term')->get('name');
            $count = EEM_Person_Post::instance()->count(
                [
                    [
                        'PER_ID'   => $item->ID(),
                        'OBJ_type' => 'Event',
                        'PT_ID'    => $type->ID(),
                    ],
                ]
            );

            $event_filter_link = EEH_URL::add_query_args_and_nonce(
                [
                    'page'   => 'espresso_events',
                    'action' => 'default',
                    'PER_ID' => $item->ID(),
                    'PT_ID'  => $type->ID(),
                ],
                admin_url('admin.php')
            );
            $content           .= '
            <li class="ee-status-outline ee-status-outline--info">
                <a href="' . $event_filter_link . '" class="ee-event-filter-link ee-link">
                    <span class="person-type-relation">' . $name . '</span><span class="person-type-count">' . $count . '</span>
                </a>
            </li>';
        }
        $content .= '
        </ul>';
        echo $content;
    }
}
