// Prepend jquery
// Prepend angularJS

/* global jQuery, angular, MBPostTypeLabels */

(function ( $, angular )
{
	'use strict';

	var app = angular.module( 'mbPostType', [] );

	angular.element( document ).ready( function ()
	{
		angular.bootstrap( $( '#post' ), ['mbPostType'] );
	} );

	app.controller( 'PostTypeController', ['$scope', function ( $scope )
	{
		// Watch the change of label_name and auto fill in inputs
		$scope.$watch( 'label_name', function ()
		{
			// If it is not add new page
			if ( 'mb-post-type' !== getParameterByName( 'post_type' ) )
			{
				return;
			}

			$scope.label_menu_name = $scope.label_name;
			$scope.label_add_new = MBPostTypeLabels.add_new;
			$scope.label_parent_item_colon = MBPostTypeLabels.parent_item_colon + $scope.label_name;
			$scope.label_all_items = MBPostTypeLabels.all_items + $scope.label_name;
			$scope.label_search_items = MBPostTypeLabels.search_items + $scope.label_name;
			$scope.label_not_found = MBPostTypeLabels.no + $scope.label_name + MBPostTypeLabels.not_found;
			$scope.label_not_found_in_trash = MBPostTypeLabels.no + $scope.label_name + MBPostTypeLabels.not_found_in_trash;
		} );

		// Watch the change of label_singular_name and auto fill in inputs
		$scope.$watch( 'label_singular_name', function ()
		{
			// If it is not add new page
			if ( 'mb-post-type' !== getParameterByName( 'post_type' ) )
			{
				return;
			}

			$scope.label_name_admin_bar = $scope.label_singular_name;
			$scope.label_add_new_item = MBPostTypeLabels.add_new_item + $scope.label_singular_name;
			$scope.label_new_item = MBPostTypeLabels.new_item + $scope.label_singular_name;
			$scope.label_edit_item = MBPostTypeLabels.edit_item + $scope.label_singular_name;
			$scope.label_update_item = MBPostTypeLabels.update_item + $scope.label_singular_name;
			$scope.label_view_item = MBPostTypeLabels.view_item + $scope.label_singular_name;
			$scope.args_post_type = stringToSlug( $scope.label_singular_name );
		} );
	}] );

	/**
	 * Make some checkboxes in Supports Meta Box are checked by default
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
		var checkboxes = ['title', 'editor', 'thumbnail'];

		$.each( checkboxes, function ( k, v )
		{
			$( 'input:checkbox[name="args_supports[]"][value=' + v + ']' ).attr( 'checked', 'checked' );
		} );
	}

	/**
	 * Convert string to slug
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

	/**
	 * Transform string to slug after filled in slug's input
	 * @return void
	 */
	function slugEntering()
	{
		$( '#args_post_type' ).on( 'blur', function ()
		{
			var $this = $( this ), val = $this.val();
			$this.val( stringToSlug( val ) );
		} );
	}

	/**
	 * Get parameter from query string
	 * @param name
	 * @return string
	 */
	function getParameterByName( name )
	{
		name = name.replace( /[\[]/, "\\[" ).replace( /[\]]/, "\\]" );
		var regex = new RegExp( "[\\?&]" + name + "=([^&#]*)" ),
			results = regex.exec( location.search );
		return results === null ? "" : decodeURIComponent( results[1].replace( /\+/g, " " ) );
	}

	/**
	 * Toggle Label and Advanced Settings
	 * @return void
	 */
	function toggleAdvanceSettings()
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
			$this.attr( 'checked', 'checked' );
		} );
	}

	// Run when document is ready
	$( function ()
	{
		slugEntering();
		defaultCheckedCheckbox();
		toggleAdvanceSettings();
		activeMenu();
	} );

})( jQuery, angular );
