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

	if ( 'cost' === $meta_key ) {
		$value = transform_cost( $value );
	}

	if ( 'rarity' === $meta_key ) {
		$value = transform_rarity( $value );
	}

	ob_start();
	?>
	<div <?php echo sprintf( 'class="ccgman-%1$s" id="ccgman-card-%2$s-%1$s"', $meta_key, sanitize_title( $value ) ); // WPCS: XSS ok, sanitized on output. ?>>
		<span><?php echo wptexturize( $value ); // WPCS: XSS ok, sanitized on output. ?></span>
	</div>
	<?php
	return ob_get_clean();
}

function transform_cost( $value ) {
	$converted_value = '';
	preg_match_all( '/{([^}]*)}/', $value, $matches );
	$cost = $matches[1];

	foreach ( $cost as $mana_symbol ) {
		$converted_value .= mana_symbol( $mana_symbol );
	}

	return $converted_value;
}

function transform_rarity( $value ) {
	$rarities = Main\rarity();

	return $rarities[ $value ];
}

function mana_symbol( $value ) {
	return sprintf(
		'<img src="%1$s" alt="%2$s" />',
		sprintf( plugin_dir_url( dirname( __FILE__ ) ) . 'vendor/scryfall/manamoji-slack/emojis/mana-%s.png', $value ),
		$value
	);
}
