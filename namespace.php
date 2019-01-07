<?php
/**
 * Main CCG Manager Namespace
 */

namespace CCGManager;

function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_post_type_and_taxonomies' );
	add_action( 'init', __NAMESPACE__ . '\\change_post_type_labels' );
	// add_action( 'manage_ccg_card_posts_custom_column', __NAMESPACE__ . '\\render_card_columns', 10, 2 );
	// add_filter( 'manage_edit-ccg_card_columns', 'register_columns' );
}

/**
 * Register the custom post type and taxonomies.
 */
function register_post_type_and_taxonomies() {
	register_extended_post_type( 'ccg_card', [
		'public'   => true,
		'rewrite'  => [
			'slug'       => 'card',
			'with_front' => 'false',
		],
		'menu_icon' => plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/images/card-mini.png',
		'supports' => [ 'title', 'editor', 'revisions', 'thumbnail' ],
		'admin_cols' => [
			'title' => [
				'title' => __( 'Card', 'ccg-manager' ),
			],
			'ccg_series' => [
				'taxonomy' => 'ccg_series',
			],
			'ccg_collection' => [
				'taxonomy' => 'ccg_collection',
			],
			'card_type' => [
				'title' => __( 'Card Type', 'ccg-manager' ),
				'meta_key' => 'creature-type',
			],
			'cost' => [
				'title' => __( 'Cost', 'ccg-manager' ),
				'meta_key' => 'cost',
			],
			'rarity' => [
				'title' => __( 'Rarity', 'ccg-manager' ),
				'meta_key' => 'rarity',
			],
		],
		[
			'singular' => __( 'Card', 'ccg-manager' ),
			'plural'   => __( 'Cards', 'ccg-manager' ),
		],
	] );

	register_extended_taxonomy( 'ccg_collection', 'ccg_card',
		[
			'dashboard_glance' => true,
		], [
			'singular'      => __( 'Collection', 'ccg-manager' ),
			'plural'        => __( 'Collections', 'ccg-manager' ),
			'slug'          => 'collection',
		]
	);

	register_extended_taxonomy( 'ccg_series', 'ccg_card',
		[
			'dashboard_glance' => true,
		], [
			'singular'      => __( 'Series', 'ccg_manager' ),
			'plural'        => __( 'Series', 'ccg_manager' ),
			'slug'          => 'series',
		]
	);
}




function render_card_columns( $column, $post_id ) {
	global $post;

	switch ( $column ) {
		case 'ccg_series':
			$terms = get_the_terms( $post_id, 'ccg_series' );

			if ( ! empty( $terms ) ) {
				$out = [];
				foreach ( $terms as $term ) {
					$out[] = sprintf(
						'<a href="%s">%s</a>',
						esc_url( add_query_arg( [
							'post_type'  => $post->post_type,
							'ccg_series' => $term->slug,
						], 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'ccg_series', 'display' ) )
					);
				}
				echo join( ', ', $out ); // XSS ok, already escaped.
			} else {
				esc_html_e( 'No series found', 'ccg-manager' );
			}
			break;

		case 'ccg_collection':
			$terms = get_the_terms( $post_id, 'ccg_collection' );

			if ( ! empty( $terms ) ) {
				$out = [];
				foreach ( $terms as $term ) {
					$out[] = sprintf(
						'<a href="%s">%s</a>',
						esc_url( add_query_arg( [
							'post_type'      => $post->post_type,
							'ccg_collection' => $term->slug,
						], 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'ccg_collection', 'display' ) )
					);
				}
				echo join( ', ', $out ); // XSS ok, already sanitized.
			} else {
				esc_html_e( 'No collections found', 'ccg-manager' );
			}
			break;

		case 'creature-type':
			$type = get_post_meta( $post->ID, 'creature-type', true );

			if ( $type ) {
				echo esc_html( $type );
			}

			break;

		case 'cost':
			$cost = get_post_meta( $post->ID, 'cost', true );

			if ( $cost ) {
				echo esc_html( $cost );
			}

			break;

		case 'rarity':
			$rarity = get_post_meta( $post->ID, 'rarity', true );

			if ( $rarity ) {
				foreach ( rarity() as $r ) {
					$label = $r['label'];
					$value = $r['value'];

					if ( $rarity === $value ) {
						$label = esc_html( $label );
						if ( 'r' === $rarity ) {
							echo wp_kses_post( '<span style="color: purple; font-weight: bold;">' . $label . '</span>' );
						} elseif ( 'u' === $rarity ) {
							echo wp_kses_post( '<span style="color: gold; font-weight: bold;">' . $label . '</span>' );
						} else {
							echo wp_kses_post( '<span style="font-weight: bold;">' . $label . '</span>' );
						}
					}
				}
			} else {
				esc_html_e( 'No rarity set', 'ccg-manager' );
			}
			break;

		default:
			break;
	}
}

function rarity() {
	$rarity = [
		'common' => [
			'label' => __( 'Common', 'ccg-manager' ),
			'value' => 'c',
		],
		'uncommon' => [
			'label' => __( 'Uncommon', 'ccg-manager' ),
			'value' => 'u',
		],
		'rare' => [
			'label' => __( 'Rare', 'ccg-manager' ),
			'value' => 'r',
		],
	];

	return $rarity;
}
