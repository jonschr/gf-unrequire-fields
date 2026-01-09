<?php
/**
 * Plugin Name: GF Unrequire Fields
 * Plugin URI: http://gravitywiz.com/speed-up-gravity-forms-testing-unrequire-required-fields/
 * Description: Unrequire all required Gravity Forms fields for testing purposes. Add ?unrequire=1 to the URL to bypass required fields (admin only).
 * Version: 1.0.0
 * Author: David Smith / Gravity Wiz
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

	var $_args = null;

	public function __construct( $args = array() ) {

		$this->_args = wp_parse_args( $args, array(
			'admins_only'         => true,
			'require_query_param' => true,
		) );

		add_filter( 'gform_pre_validation', array( $this, 'unrequire_fields' ) );

	}

	function unrequire_fields( $form ) {

		if ( $this->_args['admins_only'] && ! current_user_can( 'activate_plugins' ) ) {
			return $form;
		}

		if ( $this->_args['require_query_param'] && ! isset( $_GET['unrequire'] ) ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {
			$field['isRequired'] = false;
		}

		return $form;
	}

}

# Basic Usage
# requires that the user be logged in as an administrator and that a 'unrequire' parameter be added to the query string
# http://youurl.com/your-form-page/?unrequire=1

new GWUnrequire();