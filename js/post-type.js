(function ( $, document, angular, hljs, i18n ) {
	'use strict';

	/**
	 * Helper function to convert string to slug
	 * @param str
	 * @return string
	 */
	function stringToSlug( str ) {
		// Trim the string
		str = str.replace( /^\s+|\s+$/g, '' );
		str = str.toLowerCase();

		// Remove accents
		var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;",
			to = "aaaaeeeeiiiioooouuuunc------",
			i, l;

		for ( i = 0, l = from.length; i < l; i ++ ) {
			str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
		}

		str = str.replace( /[^a-z0-9 -]/g, '' ) // remove invalid chars
			.replace( /\s+/g, '-' ) // collapse whitespace and replace by -
			.replace( /-+/g, '-' ); // collapse dashes
		return str;
	}

	function toggleSettings( btn, target ) {
		var $target = $( target );
		$target.hide();
		$( btn ).on( 'click', function() {
			$target.toggle();
		} );
	}

	/**
	 * Add/Remove active class for selected/unselected menu icon
	 */
	function activeMenu() {
		$( 'input[name="args_menu_icon"]' ).on( 'change', function () {
			$( this ).closest( '.icon-single' ).addClass( 'active' ).siblings().removeClass( 'active' );
		} );
	}

	function copyToClipboard() {
		var icon = '<svg class="mb-icon--copy" aria-hidden="true" role="img"><use href="#mb-icon-copy" xlink:href="#icon-copy"></use></svg> ',
			clipboard = new ClipboardJS( '.mb-button--copy', {
				target: function ( trigger ) {
					return trigger.nextElementSibling;
				}
			} );
		clipboard.on('success', function(e) {
			e.clearSelection();
			e.trigger.innerHTML = icon + i18n.copied;
			setTimeout(function() {
				e.trigger.innerHTML = icon + i18n.copy;
			}, 3000);
		} );
		clipboard.on('error', function() {
			alert( i18n.manualCopy );
		});
	}

	angular.module( 'mbPostType', [] ).controller( 'PostTypeController', [ '$scope', function ( $scope ) {
		// Initialize labels
		$scope.labels = {};

		// Update labels and slug when plural and singular name are updated
		$scope.updateLabels = function () {
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
			];
			params.forEach( function( param ) {
				$scope.labels[param] = i18n[param].replace( '%name%', $scope.labels.name ).replace( '%singular_name%', $scope.labels.singular_name );
			} );

			// Update slug, make sure it has <= 20 characters.
			var slug = stringToSlug( $scope.labels.singular_name );
			if ( slug.length > 20 ) {
				slug = slug.substring( 0, 20 );
				var lastChar = slug.substr( 19 );
				if ( '-' === lastChar ) {
					slug = slug.substring( 0, 19 );
				}
			}

			$scope.post_type = slug;
		};
	} ] );

	// Bootstrap AngularJS app
	angular.element( document ).ready( function () {
		angular.bootstrap( document.getElementById( 'wpbody-content' ), ['mbPostType'] );
	} );

	// Run when document is ready
	$( function () {
		toggleSettings( '#mb-cpt-toggle-labels', '#mb-cpt-label-settings' );
		toggleSettings( '#mb-cpt-toggle-code', '#mb-cpt-generate-code' );
		activeMenu();
		copyToClipboard();
	} );
	hljs.initHighlightingOnLoad();
} )( jQuery, document, angular, hljs, MbCptLabels );
