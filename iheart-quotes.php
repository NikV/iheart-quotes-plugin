<?php
/**
 * Plugin Name: I ❤️️ Quotes Plugin
 * Description: A special plugin for developers who need some inspiration. With quotes.
 * Author: Nikhil Vimal
 * Author URI: http://nik.techvoltz.com
 * Version: 1.0
 * Plugin URI: https://github.com/NikV/chuck-norris-plugin
 * License: GNU GPLv2+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

Class Programming_Quotes_Plugin {

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'programming_quotes_dashboard_widget' ));
		add_shortcode('programming_quotes', array( $this, 'programming_quotes_shortcode' ));

	}

	/**
	 * The main Quotes Function
	 */
	public function programming_quotes_function() {
		if (! $joke = get_transient('programming_quotes') ) {

			// If there's no cached version, let's get a joke
			$jsonurl     = "http://www.iheartquotes.com/api/v1/random?format=json";
			$json        = wp_remote_get( $jsonurl );

			if ( is_wp_error( $json ) ) {
				return "Chuck Norris accidentally kicked the server, it will be up soon!";
			}

			else {
				// If everything's okay, parse the body and json_decode it
				$json_output = json_decode( wp_remote_retrieve_body( $json ));
				$quote = $json_output->quote;

				// Store the result in a transient, expires after 1 day
				// Also store it as the last successful using update_option


					set_transient( 'programming_quotes', $joke, 60 * 1 );

			}
		}
		echo esc_html($quote);

	}

	// The shortcode function for [chuck-norris-jokes]
	public function programming_quotes_shortcode() {
		return $this->programming_quotes_function();
	}

	/**
	 * Add dashboard widget. A Chuck Norris Dashboard Widget
	 */
	public function programming_quotes_dashboard_widget() {

		wp_add_dashboard_widget(
			'programming_quotes_dashboard_widget',         // Widget slug.
			'Chuck Norris Jokes',         // Champion Title.
			array( $this, 'programming_quotes_widget_function' ) // Roundhouse kick that function to another line.
		);
	}

	/**
	 * Callback for dashboard widget
	 */
	public function programming_quotes_widget_function() {
		return $this->programming_quotes_function();
	}

}
new Programming_Quotes_Plugin();
