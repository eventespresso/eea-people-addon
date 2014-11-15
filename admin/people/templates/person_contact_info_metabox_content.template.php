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
				<label for="PER_email"><?php _e('Email Address', 'event_espresso'); ?><span class="denotes-required-spn">*</span></label><br>
				<div class="validation-notice-dv"><?php _e( 'The following is  a required field', 'event_espresso' );?></div>
				<input class="all-options required" type="text" id="PER_email" name="PER_email" value="<?php echo $person->email(); ?>" required/><br/>
				<p class="description"><?php _e('( required value )', 'event_espresso'); ?></p>
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
