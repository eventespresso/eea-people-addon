<?php
EEH_Template_Validator::verify_instanceof( $person, '$person', 'EE_Person');
/*
 * @var $person EE_Person
 * @var $state_html html for displaying the person's state
 * @var $country_html html for displaying the person's country
 */
?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<td>
				<label for="PER_address"><?php _e('Address:', 'event_espresso'); ?></label><br>
				<input class="all-options" type="text" id="PER_address" name="PER_address" value="<?php echo $person->address(); ?>"/>
				<br/>
				<input class="all-options" type="text" id="PER_address2" name="PER_address2" value="<?php echo $person->address2(); ?>"/>
				<br/>
				<p class="description"><?php _e('The person\'s street address.', 'event_espresso'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<td>
				<label for="PER_city"><?php _e('City', 'event_espresso'); ?></label><br>
				<input class="all-options" type="text" id="PER_city" name="PER_city" value="<?php echo $person->city(); ?>"/>
			</td>
		</tr>
		<tr valign="top">
			<td>
				<?php echo $state_html?>
			</td>
		</tr>
		<tr valign="top">
			<td>
				<?php echo $country_html?>
			</td>
		</tr>
		<tr valign="top">
			<td>
				<label for="PER_zip"><?php _e('Zip/Postal Code', 'event_espresso'); ?></label><br>
				<input class="all-options" type="text" id="PER_zip" name="PER_zip" value="<?php echo $person->zip(); ?>"/>
			</td>
		</tr>
	</tbody>
</table>
