jQuery( function( $ )
{
	/**
	 * Make some checkboxes in Supports Meta Box are checked by default
	 *
	 * @return void
	 */
	function defaultCheckedCheckbox()
	{
		// Name of checkboxes that will be checked
		var checkboxes = [ 'title', 'editor', 'thumbnail' ];

		$.each( checkboxes, function( k, v )
		{
			$( 'input:checkbox[name="args_supports[]"][value=' + v + ']' ).attr( 'checked', 'checked' );
		} );
	}

	defaultCheckedCheckbox();
} );