( function ( document, i18n ) {
	'use strict';

	const status = document.querySelector( '#status-migrate' ),
		button = document.querySelector( '#process-migrate' );

	button.addEventListener( 'click', async () => {
		printMessage( i18n.start );

        printMessage( i18n.migrating );
		await migrate();

		printMessage( i18n.done );
	} );

	async function migrate( ) {
		const response = await get( `${ajaxurl}?action=mbcpt_migrate` );
		if ( response.data.type == 'continue' ) {
			await migrate();
		}
	}

	async function get( url ) {
		const response = await fetch( url );
	    const json = await response.json();
		if ( ! response.ok ) {
	       	throw Error( json.data );
	    }
		return json;
	}

	const printMessage = text => status.innerHTML += `<p>${text}</p>`;
} )( document, MbCpt );
