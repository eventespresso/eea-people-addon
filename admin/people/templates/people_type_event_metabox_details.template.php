<?php
/**
 * This is a template for people metabox content on the EE cpt page.
 *
 * Template Args available are:
 * @type $people_type EE_Term_Taxonomy people type taxonomy term object
 * @type $type              EE_Term Term being displayed.
 * @type $people          EE_Person[]  All the published people from the db (@todo need to do paging/filtering here)
 * @type $assigned_people EE_Person[]  Currently assigned persons for this display.
 * @type $create_person_link string     URL to create a new person.
 */
?>
<div id="cpt_to_people_container_<?php echo $people_type->get('term_taxonomy_id'); ?>">
	<p class="description"><?php echo $people_type->get('description'); ?></p>
	<?php if ( empty( $people ) ) : ?>
		<?php printf( __( 'There are no people in the system. Go ahead and %screate one now%s.', 'event_espresso' ), '<a href="' . $create_person_link . '" target="_blank">', '</a>' ); ?>
	<?php else : ?>
		<table class="people-to-cpt-table">
			<thead>
				<tr>
					<th></th>
					<th><?php _e('Order', 'event_espresso'); ?></th>
				</tr>
			</thead>
			<?php
			//first we do the currently assigned people and list first.
			$assigned_people_ids = array();
			$order_count = 0;
			$row_count = 0;
			foreach ( $assigned_people as $assigned_person ) :
			?>
			<tr>
				<td>
					<label class="selectit">
						<input value="<?php echo $assigned_person->ID(); ?>" type="checkbox" name="people_to_cpt[<?php echo $people_type->get('term_taxonomy_id'); ?>][<?php echo $row_count; ?>][PER_ID]" id="people-to-cpt-<?php echo $people_type->get('term_taxonomy_id'); ?>" checked="checked"> <?php echo $assigned_person->full_name(); ?>
					</label>
				</td>
				<td>
					<input class="PER_order" id="people-to-cpt-order-<?php echo $people_type->get('term_taxonomy_id'); ?>" value="<?php echo $order_count; ?>" type="text" name="people_to_cpt[<?php echo $people_type->get('term_taxonomy_id'); ?>][<?php echo $row_count; ?>][PER_order]">
				</td>

			</tr>
			<?php $assigned_people_ids[] = $assigned_person->ID();  $order_count++; $row_count++; endforeach; ?>
			<?php
			//next we loop through ALL people
			foreach ( $people as $person ) :
				if ( in_array( $person->ID(), $assigned_people_ids ) ) {
					continue;
				}
			?>
			<tr>
				<td>
					<label class="selectit">
						<input value="<?php echo $person->ID(); ?>" type="checkbox" name="people_to_cpt[<?php echo $people_type->get('term_taxonomy_id'); ?>][<?php echo $row_count; ?>][PER_ID]" id="people-to-cpt-<?php echo $people_type->get('term_taxonomy_id'); ?>"> <?php echo $person->full_name(); ?>
					</label>
				</td>
				<td>
					<input class="PER_order" id="people-to-cpt-order-<?php echo $people_type->get('term_taxonomy_id'); ?>" value="" type="text" name="people_to_cpt[<?php echo $people_type->get('term_taxonomy_id'); ?>][<?php echo $row_count; ?>][PER_order]">
				</td>
			</tr>
			<?php $row_count++; endforeach; ?>
		</table>
	<?php endif; ?>
</div>
