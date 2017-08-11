<?php
/**
 * Template file to add extra content to a single person display
 * Note: To customize, just copy the template from /public/templates/* and put in your theme folder.
 * @since 1.0.0
 * @package EE People Addon
 * @subpackage  template
 * @author Darren Ethier
 */
global $post;
$events = EEH_People_View::get_events_for_person();
?>
<div class="eea-people-addon-person-events-container">
	<?php if ( $events ) : ?>
	<h3><?php _e('Events this person is involved with:', 'event_espresso'); ?></h3>
	<?php foreach ( $events as $type => $event ) : ?>
		<div class="eea-people-addon-people-type-container">
			<h4 class="eea-people-addon-people-type-label"><?php echo $type; ?></h4>
			<ul class="eea-people-addon-event-list-ul">
				<?php foreach ( $event as $evt ) : ?>
					<li>
						<a class="eea-people-addon-link-to-event" href="<?php echo get_permalink( $evt->ID() ); ?>" title="<?php printf( __('Click here to view more info about %s', 'event_espresso' ), $evt->name() ); ?>"><span class="eea-people-addon-event-name"><?php echo $evt->name(); ?></span></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
	<?php endif; ?>
</div>
