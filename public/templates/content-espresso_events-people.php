<?php
/**
 * Template for when people are displayed with events (is_single());
 */
global $post;
EE_Registry::instance()->load_helper( 'People_View' );
$people = EEH_People_View::get_people_for_event();
?>
<div class="eea-people-addon-event-people-list-single">
	<?php foreach ( $people as $type => $persons ) : ?>
		<div class="eea-people-addon-people-type-container">
			<h4 class="eea-people-addon-people-type-label"><?php echo $type; ?></h4>
			<ul class="eea-people-addon-people-list-ul">
				<?php foreach ( $persons as $person ) : ?>
					<li>
						<a class="eea-people-addon-link-to-person" href="<?php echo get_permalink( $person->ID() ); ?>" title="<?php printf( __('Click here to view more info about %s', 'event_espresso' ), $person->full_name() ); ?>"><span class="eea-people-addon-person-full-name"><?php echo $person->full_name(); ?></span></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>

