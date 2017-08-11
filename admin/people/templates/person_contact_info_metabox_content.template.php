<?php
EEH_Template_Validator::verify_instanceof( $person, '$person', 'EE_Person');
/**
 * @var $person - instance of EE_Person
 */
?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<td>
				<label for="PER_email"><?php _e('Email Address', 'event_espresso'); ?><br />
				<input class="all-options" type="text" id="PER_email" name="PER_email" value="<?php echo $person->email(); ?>"/>
			</td>
		</tr>
		<tr valign="top">
			<td>
				<label for="PER_phone"><?php _e('Phone Number', 'event_espresso'); ?></label><br>
				<input class="all-options" type="text" id="PER_phone" name="PER_phone" value="<?php echo $person->phone(); ?>"/>
			</td>
		</tr>
	</tbody>
</table>
