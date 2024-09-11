( function ( $ ) {
	$( 'table.posts #the-list, table.pages #the-list' ).sortable( {
		'items': 'tr',
		'axis': 'y',
		'update': function () {
			$.post( ajaxurl, {
				action: 'update_menu_order',
				order: $( '#the-list' ).sortable( 'serialize' ),
			} );
		}
	} );
} )( jQuery );
