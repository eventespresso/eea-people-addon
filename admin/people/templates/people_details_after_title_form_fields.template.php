<?php
EEH_Template_Validator::verify_instanceof( $people, '$people', 'EE_Person');
/**
 * @var $attendee - instance of EE_Attendee
 */
?>
<div id="titlediv-people-addon">
	<div id="titlewrap-people-addon">
		<label class="hidden" id="attendee-first-name-text" for="PER_fname"><?php _e('First Name:', 'event_espresso'); ?></label>
		<input type="text" class="smaller-text-field" name="PER_fname" value="<?php echo $people->get('PER_fname'); ?>" id="PER_fname" placeholder="<?php _e('First Name', 'event_espresso'); ?>" required>
		<label class="hidden" id="attendee-first-name-text" for="PER_lname"><?php _e('Last Name:', 'event_espresso'); ?></label>
		<input id="PER_lname" type="text" class="smaller-text-field" name="PER_lname" value="<?php echo $people->get('PER_lname'); ?>" id="PER_lname" placeholder="<?php _e('Last Name', 'event_espresso'); ?>" required>
		<div style="clear:both"></div>
	</div>
</div>
<?php
