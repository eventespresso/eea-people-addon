<?php
/**
 * Template for the person_to_cpt details metabox.
 * Template args:
 * @type array $row_data  	an array of formatted data for each row containing the cpt_type, cpt_obj (EE_Base_CPT ),
 *       				and ct_obj (an array of Term_Taxonomy objects)
 */
?>
<table class="person-to-cpt-details-table">
	<thead>
		<tr>
			<th><!-- icon representing type --></th>
			<th><?php _e('Title', 'event_espresso'); ?></th>
			<th><?php _e('Doing', 'event_espresso'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $row_data as $data ) : ?>
			<tr>
				<td><span class="<?php echo $data['css_class']; ?>"></span></td>
				<td><a href="<?php echo $data['edit_link']; ?>" title="<?php _e('Click here to edit', 'event_espresso'); ?>"><span class="<?php echo $data['cpt_type'];?>-title"><?php echo $data['cpt_obj']->name(); ?></span></a></td>
				<td>
					<ul class="person-to-cpt-people-type-list">
						<?php foreach( $data['ct_obj'] as $ct ) : ?>
							<li><?php echo $ct->get_first_related('Term')->get('name'); ?></li>
						<?php endforeach; ?>
					</ul>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
