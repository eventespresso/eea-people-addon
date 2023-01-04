<?php

use EventEspresso\core\services\address\AddressInterface;

/**
 * Person model class
 *
 * @since       1.0.0
 * @package     EE Person Addon
 * @subpackage  models
 * @author      Darren Ethier
 * @method EE_Country|EE_State get_first_related($relationName, $query_params = [])
 */
class EE_Person extends EE_CPT_Base implements AddressInterface
{
    /**
     * Sets some dynamic values
     *
     * @param null            $fieldValues
     * @param bool|int|string $bydb     whether being set by db or not
     * @param null            $timezone timezone_string
     * @throws EE_Error
     * @throws ReflectionException
     * @since 1.0.0
     */
    protected function __construct($fieldValues = null, $bydb = false, $timezone = null)
    {
        if (! isset($fieldValues['PER_full_name'])) {
            $fname                        = $fieldValues['PER_fname'] ?? '';
            $lname                        = $fieldValues['PER_lname'] ?? '';
            $fieldValues['PER_full_name'] = "$fname $lname";
        }
        if (! isset($fieldValues['PER_slug'])) {
            $fieldValues['PER_slug'] = sanitize_title($fieldValues['PER_full_name']);
        }
        if (! isset($fieldValues['PER_short_bio']) && isset($fieldValues['PER_bio'])) {
            $fieldValues['PER_short_bio'] = substr($fieldValues['PER_bio'], 0, 50);
        }
        parent::__construct($fieldValues, $bydb, $timezone);
    }


    /**
     * @param array $props_n_values
     * @return EE_Person
     * @throws EE_Error
     * @throws ReflectionException
     */
    public static function new_instance(array $props_n_values = []): EE_Person
    {
        $has_object = parent::_check_for_object($props_n_values, __CLASS__);
        return $has_object
            ?: new self($props_n_values);
    }


    /**
     * @param array $props_n_values
     * @return EE_Person
     * @throws EE_Error
     * @throws ReflectionException
     */
    public static function new_instance_from_db(array $props_n_values = []): EE_Person
    {
        return new self($props_n_values, true);
    }


    /**
     * @param string $fname
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_fname(string $fname = '')
    {
        $this->set('PER_fname', $fname);
    }


    /**
     * @param string $lname
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_lname(string $lname = '')
    {
        $this->set('PER_lname', $lname);
    }


    /**
     * @param string $address
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_address(string $address = '')
    {
        $this->set('PER_address', $address);
    }


    /**
     * @param string $address2
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_address2(string $address2 = '')
    {
        $this->set('PER_address2', $address2);
    }


    /**
     * @param string $city
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_city(string $city = '')
    {
        $this->set('PER_city', $city);
    }


    /**
     * @param int $STA_ID
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_state(int $STA_ID = 0)
    {
        $this->set('STA_ID', $STA_ID);
    }


    /**
     * @param string $CNT_ISO
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_country(string $CNT_ISO = '')
    {
        $this->set('CNT_ISO', $CNT_ISO);
    }


    /**
     * @param string $zip
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_zip(string $zip = '')
    {
        $this->set('PER_zip', $zip);
    }


    /**
     * @param string $email
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_email(string $email = '')
    {
        $this->set('PER_email', $email);
    }


    /**
     * @param string $phone
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_phone(string $phone = '')
    {
        $this->set('PER_phone', $phone);
    }


    /**
     * @param bool|int|string $PER_deleted
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function set_deleted($PER_deleted = false)
    {
        $this->set('PER_deleted', $PER_deleted);
    }


    /**
     * Returns the value for the post_author id saved with the cpt
     *
     * @return int
     * @throws EE_Error
     * @throws ReflectionException
     * @since 4.5.0
     */
    public function wp_user(): int
    {
        return (int) $this->get('PER_wp_user');
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function fname(): string
    {
        return (string) $this->get('PER_fname');
    }


    /**
     * echoes out the person's first name
     *
     * @return void
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function e_full_name()
    {
        echo $this->full_name();
    }


    /**
     * Returns the first and last name concatenated together with a space.
     *
     * @param bool|int|string $apply_html_entities
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function full_name($apply_html_entities = false): string
    {
        $full_name = $this->fname() . ' ' . $this->lname();
        return $apply_html_entities
            ? htmlentities($full_name, ENT_QUOTES, 'UTF-8')
            : $full_name;
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function lname(): string
    {
        return (string) $this->get('PER_lname');
    }


    /**
     * Gets the person's full address as an array so client code can decide hwo to display it
     *
     * @return array numerically indexed, with each part of the address that is known.
     * Eg, if the user only responded to state and country,
     * it would be array(0=>'Alabama',1=>'USA')
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function full_address_as_array(): array
    {
        $full_address_array     = [];
        $initial_address_fields = ['PER_address', 'PER_address2', 'PER_city',];
        foreach ($initial_address_fields as $address_field_name) {
            $address_fields_value = $this->get($address_field_name);
            if (! empty($address_fields_value)) {
                $full_address_array[] = $address_fields_value;
            }
        }
        // now handle state and country
        $state_obj = $this->state_obj();
        if (! empty($state_obj)) {
            $full_address_array[] = $state_obj->name();
        }
        $country_obj = $this->country_obj();
        if (! empty($country_obj)) {
            $full_address_array[] = $country_obj->name();
        }
        // lastly get the xip
        $zip_value = $this->zip();
        if (! empty($zip_value)) {
            $full_address_array[] = $zip_value;
        }
        return $full_address_array;
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function address(): string
    {
        return (string) $this->get('PER_address');
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function address2(): string
    {
        return (string) $this->get('PER_address2');
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function city(): string
    {
        return (string) $this->get('PER_city');
    }


    /**
     * @return int
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function state_ID(): int
    {
        return (int) $this->get('STA_ID');
    }


    /**
     * Gets the state set to this person
     *
     * @return EE_State|null
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function state_obj(): ?EE_State
    {
        return $this->get_first_related('State');
    }


    /**
     * Returns the state's name, otherwise ''
     *
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function state_name(): string
    {
        return $this->state_obj() instanceof EE_State
            ? $this->state_obj()->name()
            : '';
    }


    /**
     * Returns the state's abbreviation, otherwise ''
     *
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function state_abbrev(): string
    {
        return $this->state_obj() instanceof EE_State
            ? $this->state_obj()->abbrev()
            : '';
    }


    /**
     * either displays the state abbreviation or the state name, as determined
     * by the "FHEE__EEI_Address__state__use_abbreviation" filter.
     * defaults to abbreviation
     *
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function state(): string
    {
        return apply_filters('FHEE__EEI_Address__state__use_abbreviation', true, $this->state_obj())
            ? $this->state_abbrev()
            : $this->state_name();
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function country_ID(): string
    {
        return (string) $this->get('CNT_ISO');
    }


    /**
     * @return EE_Country
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function country_obj(): EE_Country
    {
        return $this->get_first_related('Country');
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function country_name(): string
    {
        return $this->country_obj()
            ? $this->country_obj()->name()
            : '';
    }


    /**
     * either displays the country ISO2 code or the country name, as determined
     * by the "FHEE__EEI_Address__country__use_abbreviation" filter.
     * defaults to abbreviation
     *
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function country(): string
    {
        return apply_filters('FHEE__EEI_Address__country__use_abbreviation', true, $this->country_obj())
            ? $this->country_ID()
            : $this->country_name();
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function zip(): string
    {
        return (string) $this->get('PER_zip');
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function email(): string
    {
        return (string) $this->get('PER_email');
    }


    /**
     * @return string
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function phone(): string
    {
        return (string) $this->get('PER_phone');
    }


    /**
     * @return        bool
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function deleted(): bool
    {
        return (bool) $this->get('PER_deleted');
    }
}
