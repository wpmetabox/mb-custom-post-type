{
	// Empty container for React.
	// Must empty to avoid problems of detecting #submitdiv in wp-admin/js/post.js:701 that prevents submitting the form.
	document.querySelector( '#poststuff' ).innerHTML = '';

	// Remove .wp-header-end element to properly show notices.
	document.querySelector( '.wp-header-end' ).remove();

	const form = document.querySelector( '#post' );

	// Force form to validate to force users to enter required fields.
	// Use setTimeout because this attribute is dynamically added.
	setTimeout( () => {
		form.removeAttribute( 'novalidate' );
	}, 100 );

	// Set post status when clicking submit buttons.
	form.addEventListener( 'submit', e => {
		const submitButton = e.submitter;
		const status = submitButton.dataset.status;
		const originalStatus = form.querySelector( '#original_post_status' ).value;
		if ( originalStatus !== status ) {
			form.querySelector( '[name="messages"]' ).setAttribute( 'name', MBCPT.status !== 'publish' ? 'publish' : 'save' );
		}
		if ( originalStatus === 'auto-draft' && status === 'draft' ) {
			form.querySelector( '[name="messages"]' ).setAttribute( 'name', 'save' );
		}

		submitButton.disabled = true;
		submitButton.setAttribute( 'value', MBCPT.saving );
		form.querySelector( '[name="post_status"]' ).setAttribute( 'value', status );
	} );

}