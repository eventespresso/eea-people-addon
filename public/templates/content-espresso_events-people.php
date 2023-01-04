<?php
/**
 * Template for when people are displayed with events (is_single());
 * Note: To customize, just copy the template from /public/templates/* and put in your theme folder.
 *
 * @since       1.0.0
 * @package     EE People Addon
 * @subpackage  template
 * @author      Darren Ethier
 */

global $post;
EE_Registry::instance()->load_helper('People_View');
$people = EEH_People_View::get_people_for_event();
if ($people) :
    wp_enqueue_style(
        'espresso_eea-people-addon',
        EEA_PEOPLE_ADDON_URL . 'css/espresso_eea-people-addon.css',
        [],
        EEA_PEOPLE_ADDON_VERSION
    );
    ?>
    <div class="eea-people-addon-event-people-list-single">
        <?php foreach ($people as $type => $persons) : ?>
            <div class="eea-people-addon-people-type-container">
                <h4 class="eea-people-addon-people-type-label <?php echo 'eea-people-addon-people-type-' . str_replace(' ', '-', strtolower($type)); ?>">
                    <?php echo ucfirst($type); ?>
                </h4>
                <ul class="eea-people-addon-people-list-ul">
                    <?php foreach ($persons as $person) : ?>
                        <?php if ($person instanceof EE_Person) : ?>
                            <li>
                                <?php
                                $feature_image = get_the_post_thumbnail($person->ID(), [80, 80]);
                                if (! empty($feature_image)) :?>
                                    <div class="eea-people-addon-feature-image"><?php echo $feature_image; ?></div>
                                <?php endif; ?>
                                <a class="eea-people-addon-link-to-person"
                                   href="<?php echo get_permalink($person->ID()); ?>"
                                   title="<?php echo esc_attr(
                                       sprintf(
                                           __('Click here to view more info about %s', 'event_espresso'),
                                           $person->full_name()
                                       )
                                   ); ?>"
                                >
                                    <span class="eea-people-addon-person-full-name"><?php echo $person->full_name(); ?></span>
                                </a>
                                <br>
                                <span class="eea-people-addon-excerpt"><?php echo $person->get('PER_short_bio'); ?></span>
                                <div class="clear-float"></div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; // end people check
