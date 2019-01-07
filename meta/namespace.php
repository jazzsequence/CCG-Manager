<?php
/**
 * CCG Manager Meta
 *
 * @package CCGManager\Meta.
 */

namespace CCGManager\Meta;

use CCGManager as Main;

function bootstrap() {
	add_action( 'do_meta_boxes', __NAMESPACE__ . '\\card_image' );
	add_action( 'admin_menu', __NAMESPACE__ . '\\card_meta' );
	add_action( 'cmb2_init', __NAMESPACE__ . '\\add_cmb2_box' );
	add_filter( 'admin_post_thumbnail_html', __NAMESPACE__ . '\\set_featured_image' );
}

function card_image() {
	remove_meta_box( 'postimagediv', 'ccg_card', 'side' );
	add_meta_box( 'postimagediv', __( 'Card Image', 'ccg-manager' ), 'post_thumbnail_meta_box', 'ccg_card', 'side', 'default' );
}


function set_featured_image( $content ) {
	if ( 'ccg_card' === get_post_type() ) {
		$content = str_replace(
			esc_html__( 'Set featured image' ),
			esc_html__( 'Add card image', 'ccg-manager' ),
			$content
		);

		$content = str_replace(
			esc_html__( 'Remove featured image' ),
			esc_html__( 'Remove card image', 'ccg-manager' ),
			$content
		);
	}

	return $content;
}

function add_cmb2_box() {
	$prefix = '_ccg_man_';

	$cmb = new_cmb2_box( [
		'id'           => $prefix . 'metabox',
		'title'        => __( 'Card Information', 'ccg-manager' ),
		'object_types' => [ 'ccg_card' ],
		'priority'     => 'low',
	] );
}


function render_metabox() {
	global $post;

	ob_start();
	?>
	<input type="hidden" name="ccg_man_noncename" value="<?php wp_create_nonce( 'ccg_man_nonce' ); ?>">
	<p class="ccgman-cost">
		<label for="cost">
			<strong>
				<?php esc_html_e( 'Cost', 'ccg-manager' ); ?>
			</strong>
		</label>
		<br />
		<input size="5" type="text" name="cost" value="<?php echo esc_textarea( get_post_meta( $post->ID, 'cost', true ) ); ?>" />
	</p>

	<p class="ccgman-creature-type">
		<label for="creature-type">
			<strong>
				<?php esc_html_e( 'Card Type', 'ccg-manager' ); ?>
			</strong>
		</label>
		<br />
		<input size="15" type="text" name="creature-type" value="<?php echo esc_textarea( get_post_meta( $post->ID, 'creature-type', true ) ); ?>" />
	</p>

	<p class="ccgman-power">
		<label for="power">
			<strong>
				<?php esc_html_e( 'Power', 'ccg-manager' ); ?>
			</strong>
		</label>
		<br />
		<input size="5" type="text" name="power" value="<?php esc_textarea( get_post_meta( $post->ID, 'power', true ) ); ?>" />
	</p>

	<p class="ccgman-rarity">
		<label for="rarity">
			<strong>
				<?php esc_html_e( 'Rarity', 'ccg-manager' ); ?>
			</strong>
		</label>
		<br />
		<select name="rarity">
		<?php
		$selected = get_post_meta( $post->ID, 'rarity', true );
		foreach ( Main\rarity() as $rarity ) {
			$label = $rarity['label'];
			$value = $rarity['value'];
			?>
			<option value="<?php echo esc_textarea( $value ); ?>" <?php selected( $selected, $value ); ?>>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php } ?>
		</select>
	</p>

	<?php
	return ob_get_clean();
}

