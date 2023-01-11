<?php

/**
 * @var $person EE_Person
 * @var $state_html string html for displaying the person's state
 * @var $country_html string html for displaying the person's country
 */
?>
<table class="ee-admin-two-column-layout form-table">
    <tbody>
        <tr>
            <td>
                <label for="PER_address"><?php esc_html_e('Address:', 'event_espresso'); ?></label>
                <input class="all-options" type="text" id="PER_address" name="PER_address" value="<?php echo $person->address(); ?>"/>
                <label for="PER_address2" class="screen-reader-text"><?php esc_html_e('Address 2', 'event_espresso'); ?></label>
                <input class="all-options" type="text" id="PER_address2" name="PER_address2" value="<?php echo $person->address2(); ?>"/>
                <p class="description"><?php esc_html_e('The person\'s street address.', 'event_espresso'); ?></p>
            </td>
        </tr>
        <tr>
            <td>
                <label for="PER_city"><?php esc_html_e('City', 'event_espresso'); ?></label>
                <input class="all-options" type="text" id="PER_city" name="PER_city" value="<?php echo $person->city(); ?>"/>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $state_html?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $country_html?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="PER_zip"><?php esc_html_e('Zip/Postal Code', 'event_espresso'); ?></label>
                <input class="all-options" type="text" id="PER_zip" name="PER_zip" value="<?php echo $person->zip(); ?>"/>
            </td>
        </tr>
    </tbody>
</table>
