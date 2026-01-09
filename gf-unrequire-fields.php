<?php
/**
 * Plugin Name: GF Unrequire Fields
 * Plugin URI: http://gravitywiz.com/speed-up-gravity-forms-testing-unrequire-required-fields/
 * Description: For admin users, all Gravity Forms fields are unrequired by default. Add ?require to the URL to test with required fields enabled.
 * Version: 1.1.0
 * Author: David Smith / Gravity Wiz (made into a plugin and augmented a bit by Jon Schroeder)
 * Author URI: http://gravitywiz.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: gf-unrequire-fields
 * Requires at least: 5.0
 * Requires PHP: 7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GWUnrequire {

	public function __construct() {
		add_filter( 'gform_pre_validation', array( $this, 'unrequire_fields' ) );
		add_filter( 'gform_get_form_filter', array( $this, 'add_admin_notice' ), 10, 2 );
	}

	function unrequire_fields( $form ) {

		// Only applies to admin users
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return $form;
		}

		// If ?require is in the URL, keep fields required (for testing full form experience)
		if ( isset( $_GET['require'] ) ) {
			return $form;
		}

		// For admins without ?require, unrequire all fields
		foreach ( $form['fields'] as &$field ) {
			$field['isRequired'] = false;
		}

		return $form;
	}

	function add_admin_notice( $form_string, $form ) {

		// Only show to admin users
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return $form_string;
		}

		// Build the require URL
		$require_url = add_query_arg( 'require', '1' );

		// Determine current state and message
		if ( isset( $_GET['require'] ) ) {
			$current_url = remove_query_arg( 'require' );
			$message = sprintf(
				'<strong>Admin Notice:</strong> Required fields are <strong>enabled</strong>. <a href="%s">Disable required fields</a>',
				esc_url( $current_url )
			);
		} else {
			$message = sprintf(
				'<strong>Admin Notice:</strong> Required fields are <strong>disabled</strong> for testing. <a href="%s">Enable required fields</a>',
				esc_url( $require_url )
			);
		}

		// Build the notice HTML
		$notice = '<div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 10px 15px; margin-bottom: 2.5rem; font-size: 14px; color: #856404;">';
		$notice .= wp_kses_post( $message );
		$notice .= '</div>';

		// Prepend notice to the form HTML
		return $notice . $form_string;
	}

}

# Usage:
# - Admin users: fields are unrequired by default
# - Admin users: add ?require to the URL to test with required fields
# - Non-admin users: fields are always required (normal behavior)

new GWUnrequire();