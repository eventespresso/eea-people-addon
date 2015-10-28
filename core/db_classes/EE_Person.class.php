<?php
/**
 * This file contains the class for the Person Model
 *
 * @since 1.0.0
 * @package  EE People Addon
 * @subpackage models
 */
if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 *
 * Person model class
 *
 * @since 1.0.0
 *
 * @package		EE Person Addon
 * @subpackage	models
 * @author 		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_Person extends EE_CPT_Base implements EEI_Address {



	/**
	 * Sets some dynamic values
	 *
	 * @since 1.0.0
	 *
	 * @param array $fieldValues
	 * @param bool   $bydb        whether being set by db or not
	 * @param string $timezone    timezone_string
	 */
	protected function __construct( $fieldValues = NULL, $bydb = FALSE, $timezone = NULL ) {
		if ( !isset( $fieldValues[ 'PER_full_name' ] ) ) {
			$fname = isset( $fieldValues[ 'PER_fname' ] ) ? $fieldValues[ 'PER_fname' ] . ' ' : '';
			$lname = isset( $fieldValues[ 'PER_lname' ] ) ? $fieldValues[ 'PER_lname' ] : '';
			$fieldValues[ 'PER_full_name' ] = $fname . $lname;
		}
		if ( !isset( $fieldValues[ 'PER_slug' ] ) ) {
			$fieldValues[ 'PER_slug' ] = sanitize_title( $fieldValues[ 'PER_full_name' ] );
		}
		if ( !isset( $fieldValues[ 'PER_short_bio' ] ) && isset( $fieldValues[ 'PER_bio' ] ) ) {
			$fieldValues[ 'PER_short_bio' ] = substr( $fieldValues[ 'PER_bio' ], 0, 50 );
		}
		parent::__construct( $fieldValues, $bydb, $timezone );
	}


	/**
	 *
	 * @param type $props_n_values
	 * @return EE_Person
	 */
	public static function new_instance( $props_n_values = array() ) {
		$classname = __CLASS__;
		$has_object = parent::_check_for_object( $props_n_values, $classname );
		$obj = $has_object ? $has_object : new self( $props_n_values );
		return $obj;
	}


	public static function new_instance_from_db ( $props_n_values = array() ) {
		$obj =  new self( $props_n_values, TRUE );
		return $obj;
	}


	/**
	 *        Set Person First Name
	 *
	 * @access        public
	 * @param string $fname
	 */
	public function set_fname( $fname = '' ) {
		$this->set( 'PER_fname', $fname );
	}



	/**
	 *        Set Person Last Name
	 *
	 * @access        public
	 * @param string $lname
	 */
	public function set_lname( $lname = '' ) {
		$this->set( 'PER_lname', $lname );
	}



	/**
	 *        Set Person Address
	 *
	 * @access        public
	 * @param string $address
	 */
	public function set_address( $address = '' ) {
		$this->set( 'PER_address', $address );
	}



	/**
	 *        Set Person Address2
	 *
	 * @access        public
	 * @param        string $address2
	 */
	public function set_address2( $address2 = '' ) {
		$this->set( 'PER_address2', $address2 );
	}



	/**
	 *        Set Person City
	 *
	 * @access        public
	 * @param        string $city
	 */
	public function set_city( $city = '' ) {
		$this->set( 'PER_city', $city );
	}



	/**
	 *        Set Person State ID
	 *
	 * @access        public
	 * @param        int $STA_ID
	 */
	public function set_state( $STA_ID = 0 ) {
		$this->set( 'STA_ID', $STA_ID );
	}



	/**
	 *        Set Person Country ISO Code
	 *
	 * @access        public
	 * @param        string $CNT_ISO
	 */
	public function set_country( $CNT_ISO = '' ) {
		$this->set( 'CNT_ISO', $CNT_ISO );
	}



	/**
	 *        Set Person Zip/Postal Code
	 *
	 * @access        public
	 * @param        string $zip
	 */
	public function set_zip( $zip = '' ) {
		$this->set( 'PER_zip', $zip );
	}



	/**
	 *        Set Person Email Address
	 *
	 * @access        public
	 * @param        string $email
	 */
	public function set_email( $email = '' ) {
		$this->set( 'PER_email', $email );
	}



	/**
	 *        Set Person Phone
	 *
	 * @access        public
	 * @param        string $phone
	 */
	public function set_phone( $phone = '' ) {
		$this->set( 'PER_phone', $phone );
	}



	/**
	 *        set deleted
	 *
	 * @access        public
	 * @param        bool $PER_deleted
	 */
	public function set_deleted( $PER_deleted = FALSE ) {
		$this->set( 'PER_deleted', $PER_deleted );
	}



	/**
	 * Returns the value for the post_author id saved with the cpt
	 *
	 * @since 4.5.0
	 *
	 * @return int
	 */
	public function wp_user() {
		return $this->get( 'PER_wp_user' );
	}



	/**
	 *        get Person First Name
	 * @access        public
	 * @return string
	 */
	public function fname() {
		return $this->get( 'PER_fname' );
	}



	/**
	 * echoes out the person's first name
	 * @return void
	 */
	public function e_full_name() {
		echo $this->full_name();
	}



	/**
	 * Returns the first and last name concatenated together with a space.
	 * @param bool $apply_html_entities
	 * @return string
	 */
	public function full_name( $apply_html_entities = FALSE ) {
		$full_name = $this->fname() . ' ' . $this->lname();
		return $apply_html_entities ? htmlentities( $full_name, ENT_QUOTES, 'UTF-8' ) : $full_name;
	}



	/**
	 *        get Person Last Name
	 * @access        public
	 * @return string
	 */
	public function lname() {
		return $this->get( 'PER_lname' );
	}



	/**
	 * Gets the person's full address as an array so client code can decide hwo to display it
	 * @return array numerically indexed, with each part of the address that is known.
	 * Eg, if the user only responded to state and country,
	 * it would be array(0=>'Alabama',1=>'USA')
	 * @return array
	 */
	public function full_address_as_array() {
		$full_address_array = array();
		$initial_address_fields = array( 'PER_address', 'PER_address2', 'PER_city', );
		foreach ( $initial_address_fields as $address_field_name ) {
			$address_fields_value = $this->get( $address_field_name );
			if ( !empty( $address_fields_value ) ) {
				$full_address_array[ ] = $address_fields_value;
			}
		}
		//now handle state and country
		$state_obj = $this->state_obj();
		if ( !empty( $state_obj ) ) {
			$full_address_array[ ] = $state_obj->name();
		}
		$country_obj = $this->country_obj();
		if ( !empty( $country_obj ) ) {
			$full_address_array[ ] = $country_obj->name();
		}
		//lastly get the xip
		$zip_value = $this->zip();
		if ( !empty( $zip_value ) ) {
			$full_address_array[ ] = $zip_value;
		}
		return $full_address_array;
	}



	/**
	 *        get Person Address
	 * @return string
	 */
	public function address() {
		return $this->get( 'PER_address' );
	}



	/**
	 *        get Person Address2
	 * @return string
	 */
	public function address2() {
		return $this->get( 'PER_address2' );
	}



	/**
	 *        get Person City
	 * @return string
	 */
	public function city() {
		return $this->get( 'PER_city' );
	}



	/**
	 *        get Person State ID
	 * @return string
	 */
	public function state_ID() {
		return $this->get( 'STA_ID' );
	}



	/**
	 * Gets the state set to this person
	 * @return EE_State
	 */
	public function state_obj() {
		return $this->get_first_related( 'State' );
	}

	/**
	 * Returns the state's name, otherwise ''
	 * @return string
	 */
	public function state_name(){
		if( $this->state_obj() instanceof EE_State ){
			return $this->state_obj()->name();
		}else{
			return __( '', 'event_espresso' );
		}
	}



	/**
	 * Returns the state's abbreviation, otherwise ''
	 * @return string
	 */
	public function state_abbrev() {
		if ( $this->state_obj() instanceof EE_State ) {
			return $this->state_obj()->abbrev();
		} else {
			return __( '', 'event_espresso' );
		}
	}




	/**
	 * either displays the state abbreviation or the state name, as determined
	 * by the "FHEE__EEI_Address__state__use_abbreviation" filter.
	 * defaults to abbreviation
	 * @return string
	 */
	public function state() {
		if ( apply_filters( 'FHEE__EEI_Address__state__use_abbreviation', true, $this->state_obj() ) ) {
			return $this->state_abbrev();
		} else {
			return $this->state_name();
		}
	}



	/**
	 *    get Person Country ISO Code
	 * @return string
	 */
	public function country_ID() {
		return $this->get( 'CNT_ISO' );
	}



	/**
	 * Gets country set for this person
	 * @return EE_Country
	 */
	public function country_obj() {
		return $this->get_first_related( 'Country' );
	}

	/**
	 * REturns the country's name if known, otherwise ''
	 * @return string
	 */
	public function country_name(){
		if( $this->country_obj() ){
			return $this->country_obj()->name();
		}else{
			return __( '', 'event_espresso' );
		}
	}



	/**
	 * either displays the country ISO2 code or the country name, as determined
	 * by the "FHEE__EEI_Address__country__use_abbreviation" filter.
	 * defaults to abbreviation
	 * @return string
	 */
	public function country() {
		if ( apply_filters( 'FHEE__EEI_Address__country__use_abbreviation', true, $this->country_obj() ) ) {
			return $this->country_ID();
		} else {
			return $this->country_name();
		}
	}



	/**
	 *        get Person Zip/Postal Code
	 * @return string
	 */
	public function zip() {
		return $this->get( 'PER_zip' );
	}



	/**
	 *        get Person Email Address
	 * @return string
	 */
	public function email() {
		return $this->get( 'PER_email' );
	}



	/**
	 *        get Person Phone #
	 * @return string
	 */
	public function phone() {
		return $this->get( 'PER_phone' );
	}



	/**
	 *    get deleted
	 * @return        bool
	 */
	public function deleted() {
		return $this->get( 'PER_deleted' );
	}
}

// End of file EE_Person.class.php
