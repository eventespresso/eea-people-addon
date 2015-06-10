<?php
/**
 * This file contains the CPT strategy class for persons
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage strategies
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
 /**
 *
 * EE_CPT_Person_Strategy
 *
 * @since 1.0.0
 *
 * @package		EE People Addon
 * @subpackage	strategies
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_CPT_Person_Strategy {

	/**
	 * $CPT - the current page, if it utilizes CPTs
	 *	@var 	object
	 * 	@access 	protected
	 */
	protected $CPT = NULL;



	/**
	 *    class constructor
	 *
	 * @access 	public
	 * @param WP_Query $wp_query
	 * @param array $CPT
	 * @return 	\EE_CPT_Person_Strategy
	 */
	public function __construct( $wp_query, $CPT = array() ) {

		if ( $wp_query instanceof WP_Query ) {
			$WP_Query = $wp_query;
			$this->CPT = $CPT;
		} else {
			$WP_Query = isset( $wp_query[ 'WP_Query' ] ) ? $wp_query[ 'WP_Query' ] : null;
			$this->CPT = isset( $wp_query[ 'CPT' ] ) ? $wp_query[ 'CPT' ] : null;
		}

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
		if ( $WP_Query instanceof WP_Query ) {
			$WP_Query->is_espresso_people_single = is_single() ? TRUE : FALSE;
			$WP_Query->is_espresso_people_archive = is_archive() ? TRUE : FALSE;
		}

	}



	/**
	 * When an instance of this class is created, we add our filters
	 * (which will get removed in case the next call to get_posts ISN'T
	 * for people CPTs)
	 */
	protected function _add_filters(){
		add_filter( 'the_posts', array( $this, 'the_posts' ), 1, 2 );
	}



	/**
	 * Should be called when the last filter or hook is fired for this CPT strategy.
	 * This is to avoid applying this CPT strategy for other posts or CPTs (eg,
	 * we don't want to join to the datetime table when querying for venues, do we!?)
	 */
	protected function _remove_filters(){
		remove_filter( 'the_posts', array( $this, 'the_posts' ), 1 );
	}




	/**
	 *    the_posts
	 *
	 * @access    public
	 * @param          $posts
	 * @param WP_Query $wp_query
	 * @return    array
	 */
	public function the_posts( $posts, WP_Query $wp_query ) {
		if ( isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] == 'espresso_people' ) {
			// automagically load the EEH_Event_View helper so that it's functions are available
			EE_Registry::instance()->load_helper('People_View');
		}
		return $posts;
	}


}
// End of file EE_CPT_Person_Strategy.core.php
// Location: /core/CPTs/EE_CPT_Person_Strategy.core.php
