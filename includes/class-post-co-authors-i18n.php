<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://acquaintsoft.com/
 * @since      1.0.0
 *
 * @package    Post_Co_Authors
 * @subpackage Post_Co_Authors/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Post_Co_Authors
 * @subpackage Post_Co_Authors/includes
 * @author     Post Co-Authors <post-co-author@gmail.com>
 */
class Post_Co_Authors_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'post-co-authors',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
