/* global jQuery, angular, MbCptLabels */

(function ( $, angular )
{
	'use strict';

	angular.module( 'mbPostType', [] ).controller( 'PostTypeController', ['$scope', function ( $scope )
	{
		// Initialize labels
		$scope.labels = {};

		/**
		 * Helper function to convert string to slug
		 * @param str
		 * @return string
		 */
		function stringToSlug( str )
		{
			// Trim the string
			str = str.replace( /^\s+|\s+$/g, '' );
			str = str.toLowerCase();

			// Remove accents
			var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;",
				to = "aaaaeeeeiiiioooouuuunc------",
				i, l;

			for ( i = 0, l = from.length; i < l; i++ )
			{
				str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
			}

			str = str.replace( /[^a-z0-9 -]/g, '' ) // remove invalid chars
				.replace( /\s+/g, '-' ) // collapse whitespace and replace by -
				.replace( /-+/g, '-' ); // collapse dashes

			return str;
		}

		// Update labels and slug when plural and singular name are updated
		$scope.updateLabels = function ()
		{
			var params = [
					'menu_name',
					'name_admin_bar',
					'all_items',
					'add_new',
					'add_new_item',
					'edit_item',
					'new_item',
					'view_item',
					'search_items',
					'not_found',
					'not_found_in_trash',
					'parent_item_colon'
				],
				i = params.length;
			for ( ; i--; )
			{
				$scope.labels[params[i]] = MbCptLabels[params[i]].replace( '%name%', $scope.labels.name ).replace( '%singular_name%', $scope.labels.singular_name );
			}

			// Update slug
			$scope.post_type = stringToSlug( $scope.labels.singular_name );
		};
	}] );

	// Bootstrap AngularJS app
	angular.element( document ).ready( function ()
	{
		angular.bootstrap( document.getElementById( 'wpbody-content' ), ['mbPostType'] );
	} );

	/**
	 * Toggle Label and Advanced Settings
	 * @return void
	 */
	function toggleAdvancedSettings()
	{
		$( '#label-settings' ).hide();
		$( '#advanced-settings' ).hide();
		$( '#btn-toggle-advanced' ).on( 'click', function ()
		{
			$( '#label-settings' ).toggle();
			$( '#advanced-settings' ).toggle();
		} );
	}

	/**
	 * Add/Remove active class for selected/unselected menu icon
	 * @return void
	 */
	function activeMenu()
	{
		var $menuIcons = $( 'input[type="radio"][name="args_menu_icon"]' );

		$menuIcons.on( 'click', function ()
		{
			var $this = $( this );
			$menuIcons.closest( '.icon-single' ).removeClass( 'active' );
			$this.closest( '.icon-single' ).addClass( 'active' );
			$this.prop( 'checked', true );
		} );
	}

	// Run when document is ready
	$( function ()
	{
		toggleAdvancedSettings();
		activeMenu();
	} );

})( jQuery, angular );
