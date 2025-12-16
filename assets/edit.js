{
	// Remove header elements to properly show notices.
	if ( document.querySelector( 'h1' ) ) {
		document.querySelector( 'h1' ).remove();
	}
	if ( document.querySelector( '.page-title-action' ) ) {
		document.querySelector( '.page-title-action' ).remove();
	}
	if ( document.querySelector( '.wp-header-end' ) ) {
		document.querySelector( '.wp-header-end' ).remove();
	}

	const form = document.querySelector( '#post' );

	// Force form to validate to force users to enter required fields.
	// Use setTimeout because this attribute is dynamically added.
	setTimeout( () => {
		form.removeAttribute( 'novalidate' );
	}, 100 );

	// Set post status when clicking submit buttons.
	form.addEventListener( 'submit', e => {
		const submitButton = e.submitter;
		submitButton.disabled = true;
		submitButton.setAttribute( 'value', MBCPT.saving );

		const originalStatus = form.querySelector( '#original_post_status' ).value;
		const messages = originalStatus === 'auto-draft' ? 'publish' : 'save';
		form.querySelector( '[name="messages"]' ).setAttribute( 'name', messages );
	} );
}