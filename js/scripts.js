jQuery( function( $ )
{
	/**
	 * Make some checkboxes in Supports Meta Box are checked by default
	 *
	 * @return void
	 */
	function defaultCheckedCheckbox()
	{
		// If it is not add new page
		if ( 'mb-post-type' !== getParameterByName( 'post_type' ) )
		{
			return;
		}

		// Name of checkboxes that will be checked
		var checkboxes = [ 'title', 'editor', 'thumbnail' ];

		$.each( checkboxes, function( k, v )
		{
			$( 'input:checkbox[name="args_supports[]"][value=' + v + ']' ).attr( 'checked', 'checked' );
		} );
	}

	/**
	 * Convert string to slug
	 *
	 * @param string str
	 *
	 * @return string
	 */
	function stringToSlug( str )
	{
		// Trim the string
		str = str.replace( /^\s+|\s+$/g, '' );
		str = str.toLowerCase();

		// Remove accents
		var from = "אבהגטיכךלםןמעףצפשתüûסח·/_,:;",
			to = "aaaaeeeeiiiioooouuuunc------",
			i, l;

		for ( i = 0, l = from.length ; i < l ; i++ )
		{
			str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
		}

		str = str.replace( /[^a-z0-9 -]/g, '' ) // remove invalid chars
			.replace( /\s+/g, '-' ) // collapse whitespace and replace by -
			.replace( /-+/g, '-' ); // collapse dashes

		return str;
	}

	/**
	 * Transform string to slug after filled in slug's input
	 *
	 * return void
	 */
	function slugEntering()
	{
		$( '#args_post_type' ).on( 'blur', function()
		{
			var $this = $( this ), val = $this.val();
			$this.val( stringToSlug( val ) );
		} );
	}

	/**
	 * Get parameter from query string
	 *
	 * @param string name
	 *
	 * @return string
	 */
	function getParameterByName( name )
	{
		name = name.replace( /[\[]/, "\\[").replace(/[\]]/, "\\]" );
		var regex = new RegExp( "[\\?&]" + name + "=([^&#]*)" ),
			results = regex.exec( location.search );
		return results === null ? "" : decodeURIComponent( results[1].replace( /\+/g, " " ) );
	}

	/**
	 * Show Advance Settings
	 *
	 * @return void
	 */
	function showAdvanceSettings()
	{
		$( '#btn-advance' ).on( 'click', function()
		{
			$( '#advance' ).css( 'display', 'block' );
		} );
	}

	/**
	 * Initializing
	 *
	 * return void
	 */
	function init()
	{
		// Hide Advance Settings
		$( '#advance' ).css( 'display', 'none' );

		slugEntering();
		defaultCheckedCheckbox();
		showAdvanceSettings();
	}

	init();
} );