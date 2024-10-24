( function ( document, i18n ) {
	'use strict';

	const status = document.querySelector( '#migrate-status' );

	document.querySelector( '#migrate-button' ).addEventListener( 'click', async () => {
		printMessage( i18n.start );

		printMessage( i18n.migratingPostTypes );
		await migrate_post_types();

		printMessage( i18n.migratingTaxonomies );
		await migrate_taxonomies();

		printMessage( i18n.done );
		document.querySelector( '#migrate-links' ).removeAttribute( 'style' );
	} );

	async function migrate_post_types() {
		await get( `${ ajaxurl }?action=mbcpt_migrate_post_types` );
	}

	async function migrate_taxonomies() {
		await get( `${ ajaxurl }?action=mbcpt_migrate_taxonomies` );
	}

	async function get( url ) {
		const response = await fetch( url );
		const json = await response.json();
		if ( !response.ok ) {
			throw Error( json.data );
		}
		return json;
	}

	const printMessage = text => status.innerHTML += `<p>${ text }</p>`;
} )( document, MbCpt );
