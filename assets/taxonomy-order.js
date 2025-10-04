( ( $ ) => {
	const hierarchical = MB_CPT_ORDER_TERMS.hierarchical === '1';

	/**
	 * Get the current mode, either 'default' or 'sortable'
	 *
	 * @returns {string} The current mode
	 */
	const getMode = () => MB_CPT_ORDER_TERMS.mode ?? 'default';

	/**
	 * Build tree structure from flat list
	 * @param {array} terms
	 * @returns array
	 */
	function buildTree( terms ) {
		const termMap = new Map();
		const tree = [];

		terms.forEach( term => {
			term.children = [];
			termMap.set( term.term_id, term );
		} );

		// Only build hierarchy if hierarchical mode is enabled
		if ( hierarchical ) {
			terms.forEach( term => {
				if ( term.parent && termMap.has( term.parent ) ) {
					termMap.get( term.parent ).children.push( term );
				} else {
					tree.push( term );
				}
			} );
		} else {
			// Flat list if not hierarchical
			tree.push( ...terms );
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
		let lists = $( "#the-list" ).attr( "data-wp-lists" ),
			html = `<ul id="the-list" data-wp-lists="${ lists }" class="mb-cpt-sortable" data-level="${ level }">`;
		tree.forEach( term => {
			html += `<li data-id="${ term.term_id }" data-parent="${ term.parent }" data-order="${ term.term_order }">
				<div class="mb-cpt-page-item">
					<div class="mb-cpt-handle">☰</div>
					<div class="mb-cpt-title">
						${ term.name }
					</div>
				</div>`;
			// Only include nested ul if hierarchical is enabled and there are children
			if ( hierarchical ) {
				if ( term.children.length > 0 ) {
					html += treeToHtml( term.children, level + 1 );
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

		let current = parseInt( MB_CPT_ORDER_TERMS.current_page ) - 1,
			i = current * parseInt( MB_CPT_ORDER_TERMS.per_page );
		const updatedOrder = orderData.map( ( item, index ) => {
			i++;
			item.order = i;
			return item;
		} );

		$.ajax( {
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'mb_cpt_save_order_terms',
				nonce: MB_CPT_ORDER_TERMS.nonce,
				order_data: JSON.stringify( updatedOrder )
			}
		} );
	}

	// Initialize sortable view only if mode=sortable
	if ( getMode() !== 'sortable' ) {
		return;
	}

	const $table = $( '.wp-list-table' );
	const tree = buildTree( MB_CPT_ORDER_TERMS.terms );
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