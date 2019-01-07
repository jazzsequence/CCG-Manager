<?php
/**
 * Main CCG Manager Namespace
 */

namespace CCGManager;

function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_taxonomies' );
	// add_action( 'manage_ccg_card_posts_custom_column', __NAMESPACE__ . '\\render_card_columns', 10, 2 );
	// add_filter( 'manage_edit-ccg_card_columns', 'register_columns' );
}

function register_post_type() {
	$labels = [
		'name'               => __( 'CCG Manager', 'ccg-manager' ),
		'singular_name'      => __( 'Card', 'ccg-manager' ),
		'add_new'            => __( 'Add New Card', 'ccg-manager' ),
		'add_new_item'       => __( 'Add New Card', 'ccg-manager' ),
		'edit_item'          => __( 'Edit Card', 'ccg-manager' ),
		'new_item'           => __( 'New Card', 'ccg-manager' ),
		'view'               => __( 'View Card', 'ccg-manager' ),
		'view_item'          => __( 'View Card', 'ccg-manager' ),
		'search_items'       => __( 'Search Cards', 'ccg-manager' ),
		'not_found'          => __( 'No cards found', 'ccg-manager' ),
		'not_found_in_trash' => __( 'No cards found in the trash', 'ccg-manager' ),
	];

	register_extended_post_type( 'ccg_card', [
		'public'   => true,
		'rewrite'  => [
			'slug'       => 'card',
			'with_front' => 'false',
		],
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
}

function register_taxonomies() {
	register_taxonomy( 'ccg_collection', 'ccg_card', [
		'hierarchical' => true,
		'labels'       => [
			'name'          => __( 'Collections', 'ccg-manager' ),
			'singular_name' => __( 'Collection', 'ccg-manager' ),
			'edit_item'     => __( 'Edit Collection', 'ccg-manager' ),
			'update_item'   => __( 'Update Collection', 'ccg-manager' ),
			'add_new_item'  => __( 'Add New Collection', 'ccg-manager' ),
		],
		'query_var'    => 'ccg_collection',
		'rewrite'      => [ 'slug' => 'collection' ],
	] );

	register_taxonomy('ccg_series', 'ccg_card', [
		'hierarchical' => true,
		'labels'       => [
			'name'          => __( 'Series', 'ccg-manager' ),
			'singular_name' => __( 'Series', 'ccg-manager' ),
			'edit_item'     => __( 'Edit Series', 'ccg-manager' ),
			'update_item'   => __( 'Update Series', 'ccg-manager' ),
			'add_new_item'  => __( 'Add New Series', 'ccg-manager' ),
		],
		'query_var'    => 'ccg_series',
		'rewrite'      => [ 'slug' => 'series' ],
	] );
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
