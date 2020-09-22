import { Icons } from './Icons';
const { __ } = wp.i18n;

export const BasicDatas = [
	{ type: 'text', name: 'name', label: __( 'Plural name', 'mb-custom-post-type' ), required: true },
	{ type: 'text', name: 'singular_name', label: __( 'Singular name', 'mb-custom-post-type' ), required: true },
	{ type: 'text', name: 'slug', label: __( 'Slug', 'mb-custom-post-type' ), required: true, updateFrom: 'singular_name' },
];

export const CodeDatas = [
	{ type: 'text', name: 'function_name', label: __( 'Function name', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'text_domain', label: __( 'Text domain', 'mb-custom-post-type' ) },
];

export const LabelDatas = [
	{ type: 'text', name: 'add_new', label: __( 'Add new', 'mb-custom-post-type' ), defaultValue: 'Add %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'add_new_item', label: __( 'Add new item', 'mb-custom-post-type' ), defaultValue: 'Add new %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'edit_item', label: __( 'Edit item', 'mb-custom-post-type' ), defaultValue: 'Edit %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'new_item', label: __( 'New item', 'mb-custom-post-type' ), defaultValue: 'New %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'view_item', label: __( 'View item', 'mb-custom-post-type' ), defaultValue: 'View %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'view_items', label: __( 'View items', 'mb-custom-post-type' ), defaultValue: 'View %name%', updateFrom: 'name' },
	{ type: 'text', name: 'search_items', label: __( 'Search items', 'mb-custom-post-type' ), defaultValue: 'Search %name%', updateFrom: 'name' },
	{ type: 'text', name: 'not_found', label: __( 'Not found', 'mb-custom-post-type' ), defaultValue: 'No %name% found', updateFrom: 'name' },
	{ type: 'text', name: 'not_found_in_trash', label: __( 'Not found in trash', 'mb-custom-post-type' ), defaultValue: 'No %name% found in Trash', updateFrom: 'name' },
	{ type: 'text', name: 'parent_item_colon', label: __( 'Parent items', 'mb-custom-post-type' ), defaultValue: 'Parent %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'all_items', label: __( 'All items', 'mb-custom-post-type' ), defaultValue: 'All %name%', updateFrom: 'name' },
	{ type: 'text', name: 'menu_name', label: __( 'Menu name', 'mb-custom-post-type' ), defaultValue: '%name%', updateFrom: 'name' },
];

export const SupportDatas = [
	{ name: 'title', description: __( 'Title', 'mb-custom-post-type' ), checked: true },
	{ name: 'editor', description: __( 'Editor', 'mb-custom-post-type' ), checked: true },
	{ name: 'author', description: __( 'Author', 'mb-custom-post-type' ), checked: false },
	{ name: 'thumbnail', description: __( 'Thumbnail', 'mb-custom-post-type' ), checked: true },
	{ name: 'trackbacks', description: __( 'Trackbacks', 'mb-custom-post-type' ), checked: false },
	{ name: 'custom-fields', description: __( 'Custom fields', 'mb-custom-post-type' ), checked: false },
	{ name: 'comments', description: __( 'Comments', 'mb-custom-post-type' ), checked: false },
	{ name: 'revisions', description: __( 'Revisions', 'mb-custom-post-type' ), checked: false },
	{ name: 'page-attributes', description: __( 'Page attributes', 'mb-custom-post-type' ), checked: false },
];

export const TaxonomyDatas = [
	{ name: 'category', description: __( 'Category', 'mb-custom-post-type' ), checked: false },
	{ name: 'tag', description: __( 'Tag', 'mb-custom-post-type' ), checked: false },
];

const CapabilityDatas = [
	{ value: 'post', label: __( 'Post', 'mb-custom-post-type' ) },
	{ value: 'page', label: __( 'Page', 'mb-custom-post-type' ) },
	{ value: 'custom', label: __( 'Custom', 'mb-custom-post-type' ) }
];

const ShowInMenuData = [
	{ name: 'show_in_menu', value: '', label: __( 'Select an item', 'mb-custom-post-type' ) },
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
	{ type: 'checkbox', name: 'public', label: __( 'Public?', 'mb-custom-post-type' ), description: __( 'Controls how the type is visible to authors and readers.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'exclude_from_search', label: __( 'Exclude from search?', 'mb-custom-post-type' ), description: __( 'Whether to exclude posts with this post type from frontend search results.', 'mb-custom-post-type' ), checked: false },
	{ type: 'checkbox', name: 'publicly_queryable', label: __( 'Publicly queryable?', 'mb-custom-post-type' ), description: __( 'Whether queries can be performed on the frontend.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_ui', label: __( 'Show UI?', 'mb-custom-post-type' ), description: __( 'Whether to generate a default UI for managing this post type in the admin.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_nav_menus', label: __( 'Show in nav menus?', 'mb-custom-post-type' ), description: __( 'Whether post type is available for selection in navigation menus.', 'mb-custom-post-type' ), checked: true },
	{ type: 'select', name: 'show_in_menu', label: __( 'Show in menu?', 'mb-custom-post-type' ), description: __( 'Where to show the post type in the admin menu. show_ui must be true.', 'mb-custom-post-type' ), values: ShowInMenuData, defaultValue: 0 },
	{ type: 'checkbox', name: 'show_in_admin_bar', label: __( 'Show in admin bar?', 'mb-custom-post-type' ), description: __( 'Whether to make this post type available in the WordPress admin bar.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_rest', label: __( 'Show in rest?', 'mb-custom-post-type' ), description: __( 'Whether to add the post type in the REST API.', 'mb-custom-post-type' ), checked: true },
	{ type: 'text', name: 'rest_base', label: __( 'REST API base slug', 'mb-custom-post-type' ), description: __( 'Leave empty to use the post type slug.', 'mb-custom-post-type' ), placeholder: __( 'Slug to use in REST API URL', 'mb-custom-post-type' ) },
	{ type: 'select', name: 'menu_position', label: __( 'Menu position after', 'mb-custom-post-type' ), values: MenuPosition, defaultValue: '' },
	{ type: 'radio', name: 'menu_icon', label: __( 'Menu icon', 'mb-custom-post-type' ), values: Icons },
	{ type: 'radio', name: 'capability_type', label: __( 'Capability type', 'mb-custom-post-type' ), description: __( 'The post type to use for checking read, edit, and delete capabilities.', 'mb-custom-post-type' ), values: CapabilityDatas, defaultValue: 'post' },
	{ type: 'checkbox', name: 'hierarchical', label: __( 'Hierarchical?', 'mb-custom-post-type' ), description: __( 'Whether the post type is hierarchical.', 'mb-custom-post-type' ), checked: false },
	{ type: 'checkbox', name: 'has_archive', label: __( 'Has archive?', 'mb-custom-post-type' ), description: __( 'Enables post type archives.', 'mb-custom-post-type' ), checked: true },
	{ type: 'text', name: 'archive_slug', label: __( 'Custom archive slug', 'mb-custom-post-type' ), description: __( 'Default is the post type slug.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'query_var', label: __( 'Query var', 'mb-custom-post-type' ), description: __( 'Enables request the post via URL: example.com/?post_type=slug', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'can_export', label: __( 'Can export?', 'mb-custom-post-type' ), description: __( 'Can this post type be exported?', 'mb-custom-post-type' ), checked: true },
	{ type: 'text', name: 'rewrite_slug', label: __( 'Custom rewrite slug', 'mb-custom-post-type' ), description: __( 'Leave empty to use the post type slug.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'rewrite_no_front', label: __( 'No prepended permalink structure?', 'mb-custom-post-type' ), description: __( 'Do not prepend the permalink structure with the front base.', 'mb-custom-post-type' ), checked: false },
];