<?php
/*
 * Plugin Name: WSUWP Platform Global
 * Plugin URI: http://web.wsu.edu
 * Description: Controls WSUWP Platform global version
 * Author: washingtonstateuniversity, jeremyfelt
 * Author URI: http://web.wsu.edu
 * Version: 1.0.7
 * Network: true
 */

$wsuwp_global_version = '1.0.7';
$wsuwp_wp_changeset = '29709';

add_filter( 'spine_enable_builder_module', '__return_true' );

/**
 * Returns the current deployment version of WSUWP Platform
 *
 * @return string Combines the WSUWP global version and WordPress changeset.
 */
function wsuwp_global_version() {
	global $wsuwp_global_version, $wsuwp_wp_changeset;
	return $wsuwp_global_version . '-' . $wsuwp_wp_changeset;
}