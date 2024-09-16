( function ( $ ) {
	$( 'table.posts #the-list' ).sortable( {
		'items': 'tr',
		'axis': 'y',
		'handle': '.mbcpt_order',
		'update': function () {
			$.post( ajaxurl, {
				action: 'mbcpt_update_menu_order',
				order: $( '#the-list' ).sortable( 'serialize' ),
				security: MBCPT.security
			} );
		}
	} );
} )( jQuery );
