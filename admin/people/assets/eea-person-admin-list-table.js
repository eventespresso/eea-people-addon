/**
 * People Addon js for person list tables
 */
jQuery(document).ready(function($){
    // Add delete confirmation
    $('table.peoplecategories').on('click', '.ee-requires-delete-confirmation', function(e){
        if (
            ! window.confirm(
                window.eei18n.confirm_delete_people_category
                || 'Are you sure you want to delete this person category? This action cannot be undone.'
            )
        ) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
    $('table.peopletypes').on('click', '.ee-requires-delete-confirmation', function(e){
        if (
            ! window.confirm(
                window.eei18n.confirm_delete_people_type
                || 'Are you sure you want to delete this person type? This action cannot be undone.'
            )
        ) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});
