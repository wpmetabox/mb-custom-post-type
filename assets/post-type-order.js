( ( $ ) => {
	const hierarchical = MB_CPT_ORDER.hierarchical === '1';

	/**
	 * Get the current mode, either 'default' or 'sortable'
	 *
	 * @returns {string} The current mode
	 */
	const getMode = () => MB_CPT_ORDER.mode ?? 'default';

	/**
	 * Build tree structure from flat list
	 * @param {array} posts
	 * @returns array
	 */
	function buildTree( posts ) {
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
			html += `<li data-id="${ post.ID }" data-parent="${ post.post_parent }" data-order="${ post.menu_order }">
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
	function updateOrder( evt ) {
		const orderData = [];

		$( '.mb-cpt-sortable li' ).each( function () {
			const $this = $( this );
			let parentId = 0;
			const order = $this.attr( 'data-order' );

			// Only determine parent if hierarchical mode is enabled
			if ( hierarchical ) {
				const $parentLi = $this.parent().closest( 'li' );
				parentId = $parentLi.length ? $parentLi.attr( 'data-id' ) : 0;
			}

			orderData.push( {
				id: $this.attr( 'data-id' ),
				parent_id: parentId,
				order
			} );
		} );

		let current = parseInt( MB_CPT_ORDER.current_page ) - 1,
			i = current * parseInt( MB_CPT_ORDER.per_page );
		const updatedOrder = orderData.map( ( item, index ) => {
			i++;
			item.order = i;
			return item;
		} );

		$.ajax( {
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'mb_cpt_save_order',
				nonce: MB_CPT_ORDER.nonce,
				post_type: MB_CPT_ORDER.post_type,
				order_data: JSON.stringify( updatedOrder )
			}
		} );
	}

	// Initialize sortable view only if mode=sortable
	if ( getMode() !== 'sortable' ) {
		return;
	}

	const $table = $( '.wp-list-table' );
	const tree = buildTree( MB_CPT_ORDER.posts );
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
				updateOrder( evt );
			}
		} );
	} );
} )( jQuery );