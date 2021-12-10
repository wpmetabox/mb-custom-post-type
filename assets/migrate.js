( function ( document, i18n ) {
	'use strict';

	const status = document.querySelector( '#status-migrate' ),
		button = document.querySelector( '#process-migrate' );

	button.addEventListener( 'click', async () => {
		printMessage( i18n.start );

        printMessage( i18n.migratingPostTypes );
		await migrate_post_types();

		printMessage( i18n.migratingTaxonomies );
		await migrate_taxonomies();

		printMessage( i18n.done );
	} );

	async function migrate_post_types( ) {
		const response = await get( `${ajaxurl}?action=mbcpt_migrate_post_types` );
		if ( response.data.type == 'continue' ) {
			await migrate();
		}
	}

	async function migrate_taxonomies( ) {
		const response = await get( `${ajaxurl}?action=mbcpt_migrate_taxonomies` );
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
