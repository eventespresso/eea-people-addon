/**
 * People Addon js for person editor
 */
jQuery(document).ready(function($){
	//override wp tabbing from '#title' to editor.
	$('#title').off( 'keydown.editor-focus' );

	// This code is meant to allow tabbing from Title to Post content.
	$('#PER_lname').on( 'keydown.editor-focus', function( event ) {
		var editor, $textarea;

		if ( event.keyCode === 9 && ! event.ctrlKey && ! event.altKey && ! event.shiftKey ) {
			editor = typeof tinymce != 'undefined' && tinymce.get('content');
			$textarea = $('#content');

			if ( editor && ! editor.isHidden() ) {
				editor.focus();
			} else if ( $textarea.length ) {
				$textarea.focus();
			} else {
				return;
			}

			event.preventDefault();
		}
	});

});
