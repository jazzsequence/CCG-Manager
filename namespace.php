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

/**
 * Change the post type labels.
 *
 * Since extended cpts only changes the singular and plural names of the post type, we need to manually adjust the labels if we want them to be customized.
 */
function change_post_type_labels() {
	global $wp_post_types;

	$labels = &$wp_post_types['ccg_card']->labels;
	$labels->name                     = esc_html__( 'CCG Manager', 'ccg-manager' );
	$labels->singular_name            = esc_html__( 'Card', 'ccg-manager' );
	$labels->add_new                  = esc_html__( 'Add New Card', 'ccg-manager' );
	$labels->add_new_item             = esc_html__( 'Add New Card', 'ccg-manager' );
	$labels->edit_item                = esc_html__( 'Edit Card', 'ccg-manager' );
	$labels->new_item                 = esc_html__( 'New Card', 'ccg-manager' );
	$labels->view                     = esc_html__( 'View Card', 'ccg-manager' );
	$labels->view_item                = esc_html__( 'View Card', 'ccg-manager' );
	$labels->view_items               = esc_html__( 'View Cards', 'ccg-manager' );
	$labels->search_items             = esc_html__( 'Search Cards', 'ccg-manager' );
	$labels->not_found                = esc_html__( 'No cards found', 'ccg-manager' );
	$labels->not_found_in_trash       = esc_html__( 'No cards found in the trash', 'ccg-manager' );
	$labels->all_items                = esc_html__( 'All cards', 'ccg-manager' );
	$labels->archives                 = esc_html__( 'All cards', 'ccg-manager' );
	$labels->insert_into_item         = esc_html__( 'Insert into card', 'ccg-manager' );
	$labels->uploaded_to_this_item    = esc_html__( 'Uploaded to this card', 'ccg-manager' );
	$labels->item_published           = esc_html__( 'Card published.', 'ccg-manager' );
	$labels->item_published_privately = esc_html__( 'Card published privately.', 'ccg-manager' );
	$labels->item_reverted_to_draft   = esc_html__( 'Card reverted to draft.', 'ccg-manager' );
	$labels->item_scheduled           = esc_html__( 'Card scheduled.', 'ccg-manager' );
	$labels->item_updated             = esc_html__( 'Card updated.', 'ccg-manager' );
	$labels->menu_name                = esc_html__( 'CCG Manager', 'ccg-manager' );
	$labels->name_admin_bar           = esc_html__( 'Card', 'ccg-manager' );
	$labels->featured_image           = esc_html__( 'Card Image', 'ccg-manager' );
	$labels->set_featured_image       = esc_html__( 'Add card image', 'ccg-manager' );
	$labels->remove_featured_image    = esc_html__( 'Remove card image', 'ccg-manager' );
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
