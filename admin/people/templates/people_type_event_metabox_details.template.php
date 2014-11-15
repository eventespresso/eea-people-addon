<?php
/**
 * This is a template for people metabox content on the event page.
 *
 * Template Args available are:
 * @type $people_type EE_Term_Taxonomy people type taxonomy term object
 * @type $type              EE_Term Term being displayed.
 * @type $people          EE_Person[]  All the published people from the db (@todo need to do paging/filtering here)
 * @type $assigned_people EE_Person[]  Currently assigned persons for this event.
 */
?>
<div id="event_to_people_container_<?php echo $people_type->get('term_taxonomy_id'); ?>">
	<p class="description"><?php echo $people_type->get('description'); ?></p>
	<p>
		<ul>
			<?php
			//first we do the currently assigned people and list first.
			$assigned_people_ids = array();
			foreach ( $assigned_people as $assigned_person ) :
			?>
			<li>
				<label class="selectit">
					<input value="<?php echo $assigned_person->ID(); ?>" type="checkbox" name="people_to_event[<?php echo $people_type->get('term_taxonomy_id'); ?>][]" id="people-to-event-<?php echo $people_type->get('term_taxonomy_id'); ?>"> <?php echo $assigned_person->full_name(); ?>
				</label>
			</li>
			<?php $assigned_people_ids[] = $assigned_person->ID();  endforeach; ?>
			<?php
			//next we loop through ALL people
			foreach ( $people as $person ) :
				if ( in_array( $person->ID(), $assigned_people_ids ) ) {
					continue;
				}
			?>
			<li>
				<label class="selectit">
					<input value="<?php echo $person->ID(); ?>" type="checkbox" name="people_to_event[<?php echo $people_type->get('term_taxonomy_id'); ?>][]" id="people-to-event-<?php echo $people_type->get('term_taxonomy_id'); ?>"> <?php echo $person->full_name(); ?>
				</label>
			</li>
			<?php endforeach; ?>
		</ul>
	</p>
</div>
