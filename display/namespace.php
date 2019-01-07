<?php
/**
 * CCG Card Manager Display
 *
 * Handles rendering the card meta on the front-end.
 *
 * @package CCGManager
 * @author  Chris Reynolds <me@chrisreynolds.io>
 */

namespace CCGManager\Display;

use CCGManager as Main;

function bootstrap() {
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_css' );
	add_filter( 'the_content', __NAMESPACE__ . '\\render_meta', 30 );
}

function enqueue_css() {
	wp_enqueue_style( 'ccgman-front', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/main.css' );
}

function bootstrap() {
	add_filter( 'the_content', __NAMESPACE__ . '\\render_meta', 30 );
}

function render_meta( $content ) {
	if ( is_singular( 'ccg_card' ) ) {
		foreach ( [ 'cost', 'rarity', 'creature-type', 'power' ] as $meta ) {
			$content .= wp_kses_post( render_meta_item( $meta ) );
		}
	}

	return $content;
}

function render_meta_item( $meta_key ) {
	$value = get_post_meta( get_the_ID(), $meta_key, true );
	ob_start();
	?>
	<div <?php echo esc_textarea( sprintf( 'class="ccgman-%1$s" id="ccgman-card-%2$s-%1$s"', $meta_key, $value ) ); ?>>
		<span><?php echo esc_html( wptexturize( $value ) ); ?></span>
	</div>
	<?php
	return ob_get_clean();
}
