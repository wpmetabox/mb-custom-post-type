( ( $ ) => {
	const hierarchical = MB_CPT_ORDER.hierarchical === '1';

	/**
	 * Get the current mode, either 'default' or 'sortable'
	 * 
	 * @returns {string} The current mode
	 */
	function getMode() {
		return MB_CPT_ORDER.mode ?? 'default';
	}

	/**
	 * Build tree structure from flat list
	 * @param {array} posts 
	 * @returns array
	 */
	function buildPostTree( posts ) {
		const postMap = new Map();
		const tree = [];

		posts.forEach( post => {
			post.children = [];
			postMap.set( post.ID, post );
		} );

		// Only build hierarchy if hierarchical mode is enabled
		if ( hierarchical ) {
			posts.forEach( post => {
				if ( post.post_parent && postMap.has( post.post_parent ) ) {
					postMap.get( post.post_parent ).children.push( post );
				} else {
					tree.push( post );
				}
			} );
		} else {
			// Flat list if not hierarchical
			tree.push( ...posts );
		}

		return tree;
	}

	/**
	 * Convert tree structure to HTML for sortable view
	 * 
	 * @param {array} tree
	 * @param {number} level
	 * @returns {string}
	 **/
	function treeToHtml( tree, level = 0 ) {
		let html = `<ul class="mb-cpt-sortable" data-level="${ level }">`;
		tree.forEach( post => {
			html += `<li data-id="${ post.ID }" data-parent="${ post.post_parent }">
				<div class="mb-cpt-page-item">
					<div class="mb-cpt-handle">☰</div>
					<div class="mb-cpt-title">
						${ post.post_title }
						<span class="mb-cpt-status">${ post.post_status !== 'publish' ? post.post_status : '' }</span>
					</div>
				</div>`;
			// Only include nested ul if hierarchical is enabled and there are children
			if ( hierarchical ) {
				if ( post.children.length > 0 ) {
					html += treeToHtml( post.children, level + 1 );
				} else {
					html += `<ul class="mb-cpt-sortable" data-level="${ level + 1 }"></ul>`;
				}
			}
			html += '</li>';
		} );
		html += '</ul>';

		return html;
	}

	/**
	 * Send update order request through ajax for saving
	 * 
	 * @returns void
	 */
	function updateOrder() {
		const orderData = [];

		$( '.mb-cpt-sortable li' ).each( function ( index ) {
			const $this = $( this );
			let parentId = 0;
			const current_page = MB_CPT_ORDER.current_page;
			const per_page = MB_CPT_ORDER.per_page;
			const order = ( current_page - 1 ) * per_page * 10 + index + 1;

			// Only determine parent if hierarchical mode is enabled
			if ( hierarchical ) {
				const $parentLi = $this.parent().closest( 'li' );
				parentId = $parentLi.length ? $parentLi.data( 'id' ) : 0;
			}

			orderData.push( {
				id: $this.data( 'id' ),
				parent_id: parentId,
				order
			} );
		} );

		$.ajax( {
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'mb_cpt_save_order',
				nonce: MB_CPT_ORDER.nonce,
				post_type: MB_CPT_ORDER.post_type,
				order_data: JSON.stringify( orderData )
			},
			success: function ( response ) {
				if ( response.success ) {
					MB_CPT_ORDER.posts = orderData.map( item => ( {
						ID: item.id,
						post_title: $( `li[data-id="${ item.id }"] .mb-cpt-title` ).text(),
						post_parent: item.parent_id,
						menu_order: item.order
					} ) );
				}
			},
			error: function ( xhr ) {
				// eslint-disable-next-line no-console
			}
		} );
	}

	// Initialize sortable view only if mode=sortable
	if ( getMode() !== 'sortable' ) {
		return;
	}

	const $table = $( '.wp-list-table' );
	const tree = buildPostTree( MB_CPT_ORDER.posts );
	const html = treeToHtml( tree );
	$table.html( html );

	const $sortables = $table.find( '.mb-cpt-sortable' );
	$sortables.each( function () {
		new Sortable( this, {
			group: hierarchical ? 'nested-posts' : 'flat-posts', // Nested group only if hierarchical
			animation: 150,
			handle: '.mb-cpt-page-item',
			fallbackOnBody: true,
			swapThreshold: 0.65,
			forceFallback: true,
			onEnd: ( evt ) => {
				updateOrder();
			}
		} );
	} );
} )( jQuery );