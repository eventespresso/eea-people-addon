<?php
EEH_Template_Validator::verify_instanceof($person, '$person', 'EE_Person');
/**
 * @var $person - instance of EE_Person
 */
?>
<div class="ee-layout-stack padding">
    <label for="PER_email"><?php esc_html_e('Email Address', 'event_espresso'); ?>
    <input class="all-options" type="text" id="PER_email" name="PER_email" value="<?php echo $person->email(); ?>"/>
    <br />
    <label for="PER_phone"><?php _e('Phone Number', 'event_espresso'); ?></label>
    <input class="all-options" type="text" id="PER_phone" name="PER_phone" value="<?php echo $person->phone(); ?>"/>
</div>
