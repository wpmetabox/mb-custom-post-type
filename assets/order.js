( ( $ ) => {
    var hierarchical = MB_CPT_ORDER.hierarchical === '1';
    
    console.log( 'hierarchical', hierarchical );
    // Get mode from URL query parameter
    function getMode() {
        return MB_CPT_ORDER.mode ?? 'default';
    }

    // Build tree structure from flat list
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

    // Convert tree to HTML with nesting
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

    // Update order and hierarchy
    function updateOrder() {
        const orderData = [];
        $( '.mb-cpt-sortable li' ).each( function ( index ) {
            const $this = $( this );
            let parentId = 0;
            // Only determine parent if hierarchical mode is enabled
            if ( hierarchical ) {
                const $parentLi = $this.parent().closest( 'li' );
                parentId = $parentLi.length ? $parentLi.data( 'id' ) : 0;
            }

            orderData.push( {
                id: $this.data( 'id' ),
                parent_id: parentId,
                order: index
            } );
        } );

        $.ajax( {
            url: MB_CPT_ORDER.ajax_url,
            method: 'POST',
            data: {
                action: 'mb_cpt_save_order',
                nonce: MB_CPT_ORDER.nonce,
                order_data: JSON.stringify( orderData )
            },
            success: function ( response ) {
                if ( response.success ) {
                    console.log( 'Order and hierarchy saved successfully' );
                    MB_CPT_ORDER.posts = orderData.map( item => ( {
                        ID: item.id,
                        post_title: $( `li[data-id="${ item.id }"] .mb-cpt-title` ).text(),
                        post_parent: item.parent_id,
                        menu_order: item.order
                    } ) );
                }
            },
            error: function ( xhr ) {
                console.error( 'Failed to save order:', xhr.responseText );
            }
        } );
    }

    // Initialize sortable view only if mode=sortable
    if ( getMode() === 'sortable' ) {
        const $table = $( '.wp-list-table' );
        const tree = buildPostTree( MB_CPT_ORDER.posts );
        const html = treeToHtml( tree );
        $table.html( html );

        const $sortables = $table.find( '.mb-cpt-sortable' );
        $sortables.each( function () {
            new Sortable( this, {
                group: hierarchical ? 'nested-posts' : 'flat-posts', // Nested group only if hierarchical
                animation: 150,
                handle: '.mb-cpt-handle',
                fallbackOnBody: true,
                swapThreshold: 0.65,
                forceFallback: true,
                onEnd: function ( evt ) {
                    updateOrder();
                }
            } );
        } );
    }
} )( jQuery );