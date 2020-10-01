import { Icons } from './Icons';
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
	// Name
	// Singular name
	{ type: 'text', name: 'labels.add_new', label: __( 'Add new', 'mb-custom-post-type' ), default: __( 'Add New', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'labels.add_new_item', label: __( 'Add new item', 'mb-custom-post-type' ), default: __( 'Add new %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.edit_item', label: __( 'Edit item', 'mb-custom-post-type' ), default: __( 'Edit %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.new_item', label: __( 'New item', 'mb-custom-post-type' ), default: __( 'New %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.view_item', label: __( 'View item', 'mb-custom-post-type' ), default: __( 'View %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.view_items', label: __( 'View items', 'mb-custom-post-type' ), default: __( 'View %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.search_items', label: __( 'Search items', 'mb-custom-post-type' ), default: __( 'Search %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.not_found', label: __( 'Not found', 'mb-custom-post-type' ), default: __( 'No %name% found', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.not_found_in_trash', label: __( 'Not found in trash', 'mb-custom-post-type' ), default: __( 'No %name% found in Trash', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.parent_item_colon', label: __( 'Parent items', 'mb-custom-post-type' ), default: __( 'Parent %singular_name%:', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.all_items', label: __( 'All items', 'mb-custom-post-type' ), default: __( 'All %name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.archives', label: __( 'Nav menus archives', 'mb-custom-post-type' ), default: __( '%singular_name% Archives', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.attributes', label: __( 'Attributes meta box', 'mb-custom-post-type' ), default: __( '%singular_name% Attributes', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.insert_into_item', label: __( 'Media frame button', 'mb-custom-post-type' ), default: __( 'Insert into %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.uploaded_to_this_item', label: __( 'Media frame filter', 'mb-custom-post-type' ), default: __( 'Uploaded to this %singular_name%', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.featured_image', label: __( 'Featured image meta box', 'mb-custom-post-type' ), default: __( 'Featured image', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'labels.set_featured_image', label: __( 'Setting the featured image', 'mb-custom-post-type' ), default: __( 'Set featured image', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'labels.remove_featured_image', label: __( 'Removing the featured image', 'mb-custom-post-type' ), default: __( 'Remove featured image', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'labels.use_featured_image', label: __( 'Used as featured image', 'mb-custom-post-type' ), default: __( 'Use as featured image', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'labels.menu_name', label: __( 'Menu name', 'mb-custom-post-type' ), default: __( '%name%', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.filter_items_list', label: __( 'Table views hidden heading', 'mb-custom-post-type' ), default: __( 'Filter %name% list', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.items_list_navigation', label: __( 'Table pagination hidden heading', 'mb-custom-post-type' ), default: __( '%name% list navigation', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.items_list', label: __( 'Table hidden heading', 'mb-custom-post-type' ), default: __( '%name% list', 'mb-custom-post-type' ), updateFrom: 'labels.name' },
	{ type: 'text', name: 'labels.item_published', label: __( 'Item published', 'mb-custom-post-type' ), default: __( '%singular_name% published', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.item_published_privately', label: __( 'Item published with private visibility', 'mb-custom-post-type' ), default: __( '%singular_name% published privately', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.item_reverted_to_draft', label: __( 'Item switched to draft', 'mb-custom-post-type' ), default: __( '%singular_name% reverted to draft', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.item_scheduled', label: __( 'Item scheduled', 'mb-custom-post-type' ), default: __( '%singular_name% scheduled', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
	{ type: 'text', name: 'labels.item_updated', label: __( 'Item updated', 'mb-custom-post-type' ), default: __( '%singular_name% updated', 'mb-custom-post-type' ), updateFrom: 'labels.singular_name' },
];

export const SupportDatas = [
	{ value: 'title', label: __( 'Title', 'mb-custom-post-type' ) },
	{ value: 'editor', label: __( 'Editor', 'mb-custom-post-type' ) },
	{ value: 'excerpt', label: __( 'Excerpt', 'mb-custom-post-type' ) },
	{ value: 'author', label: __( 'Author', 'mb-custom-post-type' ) },
	{ value: 'thumbnail', label: __( 'Thumbnail', 'mb-custom-post-type' ) },
	{ value: 'trackbacks', label: __( 'Trackbacks', 'mb-custom-post-type' ) },
	{ value: 'custom-fields', label: __( 'Custom fields', 'mb-custom-post-type' ) },
	{ value: 'comments', label: __( 'Comments', 'mb-custom-post-type' ) },
	{ value: 'revisions', label: __( 'Revisions', 'mb-custom-post-type' ) },
	{ value: 'page-attributes', label: __( 'Page attributes', 'mb-custom-post-type' ) },
	{ value: 'post-formats', label: __( 'Post formats', 'mb-custom-post-type' ) },
];

const CapabilityDatas = [
	{ value: 'post', label: __( 'Post', 'mb-custom-post-type' ) },
	{ value: 'page', label: __( 'Page', 'mb-custom-post-type' ) },
	{ value: 'custom', label: __( 'Custom', 'mb-custom-post-type' ) }
];

const ShowInMenuData = [
	{ name: 'show_in_menu', value: true, label: __( 'Show as top-level menu', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: false, label: __( 'Do not show in the admin menu', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'index.php', label: __( 'Show as sub-menu of Dashboard', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'upload.php', label: __( 'Show as sub-menu of Media', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'edit-tags.php?taxonomy=link_category', label: __( 'Show as sub-menu of Links', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'edit-comments.php', label: __( 'Show as sub-menu of Comments ', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'edit.php', label: __( 'Show as sub-menu of Posts', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'edit.php?post_value=page', label: __( 'Show as sub-menu of Pages', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'themes.php', label: __( 'Show as sub-menu of Appearance', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'plugins.php', label: __( 'Show as sub-menu of Plugins', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'users.php', label: __( 'Show as sub-menu of Users', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'tools.php', label: __( 'Show as sub-menu of Tools', 'mb-custom-post-type' ) },
	{ name: 'show_in_menu', value: 'options-general.php', label: __( 'Show as sub-menu of Settings', 'mb-custom-post-type' ) }
];

const MenuPosition = [
	{ name: 'menu_position', value: '', label: __( 'Select an item', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 2, label: __( 'Dashboard', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 5, label: __( 'Posts', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 10, label: __( 'Media', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 15, label: __( 'Links', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 20, label: __( 'Pages', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 25, label: __( 'Comments', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 60, label: __( 'Appearance', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 65, label: __( 'Plugins', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 70, label: __( 'Users', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 75, label: __( 'Tools', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 80, label: __( 'Settings', 'mb-custom-post-type' ) },
	{ name: 'menu_position', value: 100, label: __( 'Metabox', 'mb-custom-post-type' ) },
];

export const AdvancedDatas = [
	{ type: 'textarea', name: 'description', label: __( 'Description', 'mb-custom-post-type' ), placeholder: __( 'A short descriptive summary of what the post type is', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'public', label: __( 'Public', 'mb-custom-post-type' ), description: __( 'Controls how the type is visible to authors and readers.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'hierarchical', label: __( 'Hierarchical', 'mb-custom-post-type' ), description: __( 'Whether the post type is hierarchical.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'exclude_from_search', label: __( 'Exclude from search', 'mb-custom-post-type' ), description: __( 'Whether to exclude posts with this post type from frontend search results.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'publicly_queryable', label: __( 'Publicly queryable', 'mb-custom-post-type' ), description: __( 'Whether queries can be performed on the frontend.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'show_ui', label: __( 'Show UI', 'mb-custom-post-type' ), description: __( 'Whether to generate a default UI for managing this post type in the admin.', 'mb-custom-post-type' ) },
	{ type: 'select', name: 'show_in_menu', label: __( 'Show in menu', 'mb-custom-post-type' ), description: __( 'Where to show the post type in the admin menu. Show UI must be enabled.', 'mb-custom-post-type' ), options: ShowInMenuData },
	{ type: 'checkbox', name: 'show_in_nav_menus', label: __( 'Show in nav menus', 'mb-custom-post-type' ), description: __( 'Whether post type is available for selection in navigation menus.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'show_in_admin_bar', label: __( 'Show in admin bar', 'mb-custom-post-type' ), description: __( 'Whether to make this post type available in the WordPress admin bar.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'show_in_rest', label: __( 'Show in REST', 'mb-custom-post-type' ), description: __( 'Whether to expose this post type in the REST API. Must be true to enable the Gutenberg editor.', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'rest_base', label: __( 'REST API base slug', 'mb-custom-post-type' ), description: __( 'Leave empty to use the post type slug.', 'mb-custom-post-type' ), placeholder: __( 'Slug to use in REST API URL', 'mb-custom-post-type' ) },
	{ type: 'select', name: 'menu_position', label: __( 'Menu position after', 'mb-custom-post-type' ), options: MenuPosition, default: '' },
	{ type: 'radio', name: 'menu_icon', label: __( 'Menu icon', 'mb-custom-post-type' ), options: Icons },
	{ type: 'radio', name: 'capability_type', label: __( 'Capability type', 'mb-custom-post-type' ), description: __( 'The post type to use for checking read, edit, and delete capabilities.', 'mb-custom-post-type' ), options: CapabilityDatas, default: 'post' },
	// map_meta_cap
	// supports
	// taxonomies
	{ type: 'checkbox', name: 'has_archive', label: __( 'Has archive', 'mb-custom-post-type' ), description: __( 'Enables post type archives.', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'archive_slug', label: __( 'Custom archive slug', 'mb-custom-post-type' ), description: __( 'Default is the post type slug.', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'rewrite.slug', label: __( 'Custom rewrite slug', 'mb-custom-post-type' ), description: __( 'Leave empty to use the post type slug.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'rewrite.with_front', label: __( 'Prepended permalink structure', 'mb-custom-post-type' ), description: __( 'Example: if your permalink structure is /blog/, then your links will be: false -> /news/, true -> /blog/news/.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'query_var', label: __( 'Query var', 'mb-custom-post-type' ), description: __( 'Enables request the post via URL: example.com/?post_type=slug', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'can_export', label: __( 'Can export', 'mb-custom-post-type' ), description: __( 'Can this post type be exported', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'delete_with_user', label: __( 'Delete with user', 'mb-custom-post-type' ), description: __( 'Whether to move posts to Trash when deleting a user', 'mb-custom-post-type' ) },
];