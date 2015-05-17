<?php
/**
 * Plugin Name: I ❤️️ Quotes Plugin
 * Description: A special plugin for developers who need some inspiration. With quotes.
 * Author: Nikhil Vimal
 * Author URI: http://nik.techvoltz.com
 * Version: 1.0
 * Plugin URI: N/A
 * License: GNU GPLv2+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

Class iheart_Quotes_Plugin {

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'iheart_quotes_dashboard_widget' ));
		add_shortcode('iheart_quotes', array( $this, 'iheart_quotes_shortcode' ));

	}

	/**
	 * The main Quotes Function
	 */
	public function iheart_quotes_function() {
		if (! $fun_quote = get_transient('iheart_quotes') ) {
			// If there's no cached version, let's get a joke
			$jsonurl     = "http://www.iheartquotes.com/api/v1/random?format=json";
			$json        = wp_remote_get( $jsonurl );

			if ( is_wp_error( $json ) ) {
				return "Chuck Norris accidentally kicked the server, it will be up soon!";
			}

			else {
				// If everything's okay, parse the body and json_decode it
				$json_output = json_decode( wp_remote_retrieve_body( $json ));
				$fun_quote = $json_output->quote;

				// Store the result in a transient, expires after 1 day
				// Also store it as the last successful using update_option
				if ( $json_output->type = "success" ) {
					set_transient( 'iheart_quotes', $fun_quote, 60 * 1 );
				}
			}
		}
		echo esc_html( $fun_quote );
	}


	// The shortcode function for [chuck-norris-jokes]
	public function iheart_quotes_shortcode() {
		return $this->iheart_quotes_function();
	}

	/**
	 * Add dashboard widget. A Chuck Norris Dashboard Widget
	 */
	public function iheart_quotes_dashboard_widget() {

		wp_add_dashboard_widget(
			'iheart_quotes_dashboard_widget',
			'Quotes from I ❤️️ Quotes',
			array( $this, 'iheart_quotes_widget_function' )
		);
	}

	/**
	 * Callback for dashboard widget
	 */
	public function iheart_quotes_widget_function() {
		return $this->iheart_quotes_function();
	}

}
new iheart_Quotes_Plugin();


class iHeart_Quotes_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'iiheart_quotes_widget', // Base ID
			__( 'Quotes Widget', 'text_domain' ), // Name
			array( 'description' => __( 'A widget that displays a new quote every few minutes', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];


		$classb = new iheart_Quotes_Plugin();
		$classb->iheart_quotes_function();

		echo $args['after_widget'];

	}

}

add_action( 'widgets_init', function(){
	register_widget( 'iHeart_Quotes_Widget' );
});