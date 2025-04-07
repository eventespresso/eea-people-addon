<?php

/**
 * Person Post model object class
 *
 * @package     EE People Addon
 * @subpackage  models
 * @author      Darren Ethier
 * @since       1.0.0
 */
class EE_Person_Post extends EE_Base_Class
{
    /**
     *
     * @param array $props_n_values
     * @return EE_Person_Post
     * @throws EE_Error
     * @throws ReflectionException
     */
    public static function new_instance($props_n_values = [])
    {
        $classname  = __CLASS__;
        $has_object = parent::_check_for_object($props_n_values, $classname);
        return $has_object ?: new self($props_n_values);
    }


    /**
     * @throws ReflectionException
     * @throws EE_Error
     */
    public static function new_instance_from_db($props_n_values = [])
    {
        return new self($props_n_values, true);
    }
}
