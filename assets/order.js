( function ( $ ) {
	const table = jQuery( ".wp-list-table tbody" );
	table.sortable( {
		'items': 'tr',
		'axis': 'y',
		'handle': '.mbcpt_order',
		'update': function ( e, o ) {
			const id = o.item[ 0 ].id.substr( 5 );
			let prevId = '';
			if ( o.item.prev().length > 0 ) {
				prevId = o.item.prev().attr( "id" ).substr( 5 );
			}
			let nextId = '';
			if ( o.item.next().length > 0 ) {
				nextId = o.item.next().attr( "id" ).substr( 5 );
			}
			let $postId = $( `#post-${ id }` );
			$postId.find( '.column-mbcpt_order' ).addClass( 'spinner is-active' );
			table.sortable( 'disable' );
			$.post( ajaxurl, {
				action: 'mbcpt_update_order_items',
				id: id,
				prev_id: prevId,
				next_id: nextId,
				order: $( '#the-list' ).sortable( 'serialize' ),
				security: MBCPT.security
			}, response => {
				if ( !response.success ) {
					alert( response.data );
					window.location.reload();
				}
				let title = $( `#inline_${ id }` ).find( '.post_title' ).text();
				let l = '';
				for ( let s = 0; s < response.data; s++ ) {
					l = `&mdash; ${ l }`;
				}
				$postId.find( '.row-title' ).html( l + title );
				$postId.find( '.column-mbcpt_order' ).removeClass( 'spinner is-active' );
				table.sortable( 'enable' );
			} );
		}
	} );
} )( jQuery );
