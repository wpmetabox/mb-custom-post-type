<?php
/**
 * Requires Meta Box plugin activated to work.
 *
 * @see        http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 * @version    1.0.0
 * @author     Tran Ngoc Tuan Anh <rilwis@gmail.com>
 */

// Include the TGM_Plugin_Activation class.
require_once MB_CPT_DIR . 'lib/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'mb_cpt_register_required_plugins' );

/**
 * Register the Meta Box plugin for this plugin.
 * @return void
 */
function mb_cpt_register_required_plugins()
{
	$plugins = array(
		array(
			'name'     => 'Meta Box',
			'slug'     => 'meta-box',
			'required' => true,
		),
	);
	$config  = array(
		'id'           => 'mb-cpt-tgmpa',
		'default_path' => '',
		'menu'         => 'mb-cpt-install-plugins', // Menu slug.
		'parent_slug'  => 'plugins.php',
		'capability'   => 'manage_options',
		'has_notices'  => true,
		'dismissable'  => false,
		'dismiss_msg'  => '',
		'is_automatic' => true,
		'message'      => '',
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'mb-custom-post-type' ),
			'menu_title'                      => __( 'Install Plugins', 'mb-custom-post-type' ),
			'installing'                      => __( 'Installing Plugin: %s', 'mb-custom-post-type' ), // %s = plugin name.
			'oops'                            => __( 'Something went wrong with the plugin API.', 'mb-custom-post-type' ),
			'notice_can_install_required'     => _n_noop(
				'The MB Custom Post Type plugin requires the following plugin: %1$s.',
				'The MB Custom Post Type plugin requires the following plugins: %1$s.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'notice_can_install_recommended'  => _n_noop(
				'The MB Custom Post Type plugin recommends the following plugin: %1$s.',
				'The MB Custom Post Type plugin recommends the following plugins: %1$s.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'notice_cannot_install'           => _n_noop(
				'Sorry, but you do not have the correct permissions to install the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to install the %1$s plugins.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'notice_ask_to_update'            => _n_noop(
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with The MB Custom Post Type plugin: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with The MB Custom Post Type plugin: %1$s.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'notice_ask_to_update_maybe'      => _n_noop(
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'notice_cannot_update'            => _n_noop(
				'Sorry, but you do not have the correct permissions to update the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to update the %1$s plugins.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'notice_can_activate_required'    => _n_noop(
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'notice_can_activate_recommended' => _n_noop(
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'notice_cannot_activate'          => _n_noop(
				'Sorry, but you do not have the correct permissions to activate the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to activate the %1$s plugins.',
				'mb-custom-post-type'
			), // %1$s = plugin name(s).
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'mb-custom-post-type'
			),
			'update_link'                     => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'mb-custom-post-type'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'mb-custom-post-type'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'mb-custom-post-type' ),
			'plugin_activated'                => __( 'The Meta Box plugin has been activated successfully.', 'mb-custom-post-type' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'mb-custom-post-type' ),
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'mb-custom-post-type' ),  // %1$s = plugin name(s).
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for The MB Custom Post Type plugin. Please update the plugin.', 'mb-custom-post-type' ),  // %1$s = plugin name(s).
			'complete'                        => __( 'The Meta Box plugin has been successfully installed and activated. %1$s', 'mb-custom-post-type' ), // %s = dashboard link.
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'tgmpa' ),

			'nag_type'                        => 'error', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
		),
	);

	tgmpa( $plugins, $config );
}
