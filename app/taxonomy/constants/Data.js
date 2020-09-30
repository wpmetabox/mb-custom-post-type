const { __ } = wp.i18n;

export const BasicDatas = [
	{ type: 'text', name: 'labels.name', label: __( 'Plural name', 'mb-custom-post-type' ), required: true },
	{ type: 'text', name: 'labels.singular_name', label: __( 'Singular name', 'mb-custom-post-type' ), required: true },
	{ type: 'text', name: 'slug', label: __( 'Slug', 'mb-custom-post-type' ), required: true, updateFrom: 'labels.singular_name' },
];

export const CodeDatas = [
	{ type: 'text', name: 'function_name', label: __( 'Function name', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'text_domain', label: __( 'Text domain', 'mb-custom-post-type' ) },
];

export const LabelDatas = [
	// name
	// singular_name
	{ type: 'text', name: 'labels.search_items', label: __( 'Search items', 'mb-custom-post-type' ), default: __( 'Search %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.popular_items', label: __( 'Popular items', 'mb-custom-post-type' ), default: __( 'Popular %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.all_items', label: __( 'All items', 'mb-custom-post-type' ), default: __( 'All %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.parent_item', label: __( 'Parent item', 'mb-custom-post-type' ), default: __( 'Parent %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.parent_item_colon', label: __( 'Parent item colon', 'mb-custom-post-type' ), default: __( 'Parent %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.edit_item', label: __( 'Edit item', 'mb-custom-post-type' ), default: __( 'Edit %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.view_item', label: __( 'View item', 'mb-custom-post-type' ), default: __( 'View %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.update_item', label: __( 'Update item', 'mb-custom-post-type' ), default: __( 'Update %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.add_new_item', label: __( 'Add new item', 'mb-custom-post-type' ), default: __( 'Add new %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.new_item_name', label: __( 'New item name', 'mb-custom-post-type' ), default: __( 'New %singular_name% name', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.separate_items_with_commas', label: __( 'Separate items with commas', 'mb-custom-post-type' ), default: __( 'Separate %name% with commas', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.add_or_remove_items', label: __( 'Add or remove items', 'mb-custom-post-type' ), default: __( 'Add or remove %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.choose_from_most_used', label: __( 'Choose from most used', 'mb-custom-post-type' ), default: __( 'Choose most used %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.not_found', label: __( 'Not found', 'mb-custom-post-type' ), default: __( 'No %name% found', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.no_terms', label: __( 'No terms', 'mb-custom-post-type' ), default: __( 'No %name% found', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.items_list_navigation', label: __( 'Table pagination hidden heading', 'mb-custom-post-type' ), default: __( '%name% list pagination', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.items_list', label: __( 'Table hidden heading', 'mb-custom-post-type' ), default: __( '%name% list', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.most_used', label: __( 'Most used tab', 'mb-custom-post-type' ), default: __( 'Most Used', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.back_to_items', label: __( 'Back to items', 'mb-custom-post-type' ), default: __( 'Back to %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.menu_name', label: __( 'Menu name', 'mb-custom-post-type' ), default: __( '%name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
];

export const AdvancedDatas = [
	{ type: 'textarea', name: 'description', label: __( 'Description', 'mb-custom-post-type' ), description: __( 'A short descriptive summary of what the taxonomy is for.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'public', label: __( 'Public', 'mb-custom-post-type' ), description: __( 'Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'publicly_queryable', label: __( 'Public queryable', 'mb-custom-post-type' ), description: __( 'Whether the taxonomy is publicly queryable.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'hierarchical', label: __( 'Hierarchical', 'mb-custom-post-type' ), description: __( 'Whether the taxonomy is hierarchical.', 'mb-custom-post-type' ), checked: false },
	{ type: 'checkbox', name: 'show_ui', label: __( 'Show UI', 'mb-custom-post-type' ), description: __( 'Whether to generate and allow a UI for managing terms in this taxonomy in the admin.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_menu', label: __( 'Show in menu', 'mb-custom-post-type' ), description: __( 'Whether to show the taxonomy in the admin menu.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_nav_menus', label: __( 'Show in nav menus', 'mb-custom-post-type' ), description: __( 'Makes this taxonomy available for selection in navigation menus.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'meta_box_cb', label: __( 'Show on edit page', 'mb-custom-post-type' ), description: __( 'Whether to show the taxonomy on the edit page.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_rest', label: __( 'Show in REST', 'mb-custom-post-type' ), description: __( 'Whether to include the taxonomy in the REST API. Must be true to enable the Gutenberg editor.', 'mb-custom-post-type' ), checked: true },
	{ type: 'text', name: 'rest_base', label: __( 'REST API base slug', 'mb-custom-post-type' ), description: __( 'The base URL of the REST API route. Leave empty to use the taxonomy slug.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'show_tagcloud', label: __( 'Show tag cloud', 'mb-custom-post-type' ), description: __( 'Whether to list the taxonomy in the Tag Cloud Widget controls.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_quick_edit', label: __( 'Show in quick edit', 'mb-custom-post-type' ), description: __( 'Whether to show the taxonomy in the quick/bulk edit panel.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_admin_column', label: __( 'Show admin column', 'mb-custom-post-type' ), description: __( 'Whether to display a column for the taxonomy on its post type listing screens.', 'mb-custom-post-type' ), checked: false },
	{ type: 'text', name: 'rewrite.slug', label: __( 'Custom rewrite slug', 'mb-custom-post-type' ), description: __( 'Leave empty to use the taxonomy slug.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'rewrite.with_front', label: __( 'Prepended permalink structure', 'mb-custom-post-type' ), description: __( 'Example: if your permalink structure is /blog/, then your links will be: false -> /news/, true -> /blog/news/.', 'mb-custom-post-type' ), checked: false },
	{ type: 'checkbox', name: 'rewrite.hierarchical', label: __( 'Hierarchical URL', 'mb-custom-post-type' ), description: __( 'Either hierarchical rewrite tag or not', 'mb-custom-post-type' ), checked: false },
	{ type: 'checkbox', name: 'query_var', label: __( 'Query var', 'mb-custom-post-type' ), description: __( 'Uncheck to disable the query var, check to use the taxonomy\'s "name" as query var.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'sort', label: __( 'Sort', 'mb-custom-post-type' ), description: __( 'Whether this taxonomy should remember the order in which terms are added to objects.', 'mb-custom-post-type' ), checked: false },
];