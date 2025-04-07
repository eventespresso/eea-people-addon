<?php

 /**
 *
 * EE_CPT_Person_Strategy
 *
 * @since 1.0.0
 *
 * @package     EE People Addon
 * @subpackage  strategies
 * @author      Darren Ethier
 */
class EE_CPT_Person_Strategy
{
    /**
     * CPT details from CustomPostTypeDefinitions for specific post type
     */
    protected array $CPT;



    /**
     * @param WP_Query $wp_query
     * @param array    $CPT
     */
    public function __construct(WP_Query $wp_query, array $CPT = [])
    {
        $this->CPT = $CPT;

        // !!!!!!!!!!  IMPORTANT !!!!!!!!!!!!
        // here's the list of available filters in the WP_Query object
        // 'posts_where'
        // 'posts_where_paged'
        // 'posts_groupby'
        // 'posts_join_paged'
        // 'posts_orderby'
        // 'posts_distinct'
        // 'post_limits'
        // 'posts_fields'
        // 'posts_join'
        $this->_add_filters();
        $wp_query->is_espresso_people_single = is_single();
        $wp_query->is_espresso_people_archive = is_archive();
    }



    /**
     * When an instance of this class is created, we add our filters
     * (which will get removed in case the next call to get_posts ISN'T
     * for people CPTs)
     */
    protected function _add_filters()
    {
        add_filter('the_posts', array( $this, 'the_posts' ), 1, 2);
    }



    /**
     * Should be called when the last filter or hook is fired for this CPT strategy.
     * This is to avoid applying this CPT strategy for other posts or CPTs (eg,
     * we don't want to join to the datetime table when querying for venues, do we!?)
     */
    protected function _remove_filters()
    {
        remove_filter('the_posts', array( $this, 'the_posts' ), 1);
    }


    /**
     * @param array    $posts
     * @param WP_Query $wp_query
     * @return    array
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function the_posts(array $posts, WP_Query $wp_query): array
    {
        if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'espresso_people') {
            // automagically load the EEH_Event_View helper so that it's functions are available
            EE_Registry::instance()->load_helper('People_View');
        }
        return $posts;
    }
}
