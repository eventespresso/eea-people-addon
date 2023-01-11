<?php
/**
 * @var $category                 stdClass
 * @var $term_name_label          string
 * @var $unique_id_info_help_link string
 * @var $disable                  string
 * @var $disabled_message         string
 * @var $term_id_description      string
 * @var $term_parent_label        string
 * @var $category_select          string
 * @var $term_parent_description  string
 * @var $term_description_label   string
 * @var $category_desc_editor     string
 */

?>

<h3><?php echo $category->category_name ?></h3>
<table class="ee-admin-two-column-layout form-table">
    <tbody>
        <tr>
            <th>
                <label for="category_name">
                    <?php echo $term_name_label; ?>
                    <em title="<?php esc_html_e('This field is required', 'event_espresso') ?>"> *</em>
                </label>
            </th>
            <td>
                <input id="category_name"
                       type="text"
                       name="category_name"
                       value="<?php echo $category->category_name; ?>"
                />
            </td>
        </tr>
        <tr>
            <th>
                <label for="cat_id">
                    <?php _e('Unique ID', 'event_espresso'); ?>
                    <?php echo $unique_id_info_help_link; ?>
                </label>
            </th>
            <td>
                <input id="cat_id"
                       type="text"
                       name="category_identifier"
                       value="<?php echo $category->category_identifier; ?>"<?php echo $disable; ?>
                />
                <?php if ($disabled_message) : ?>
                    <p class="small-text"><?php echo $term_id_description; ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="category_parent">
                    <?php echo $term_parent_label; ?>
                </label>
            </th>
            <td><?php echo $category_select; ?>
                <p class="description"><?php echo $term_parent_description; ?></p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h4 class="category_description_label">
                    <?php echo $term_description_label; ?>
                </h4>
                <?php echo $category_desc_editor; ?>
                <table id="cat-descr-add-form">
                    <tbody>
                        <tr>
                            <td class="aer-word-count"></td>
                            <td class="autosave-info"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
