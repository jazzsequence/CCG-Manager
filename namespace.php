<?php
/**
 * Main CCG Manager Namespace
 *
 * @package CCGManager
 * @author  Chris Reynolds <me@chrisreynolds.io>
 */

namespace CCGManager;

/**
 * Kick off all the things.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_post_type_and_taxonomies' );
	add_action( 'init', __NAMESPACE__ . '\\change_post_type_labels' );
	add_action( 'admin_menu', __NAMESPACE__ . '\\change_menu_labels' );
	add_filter( 'dashboard_glance_items', __NAMESPACE__ . '\\change_dashboard_glance_label' );
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

/**
 * Change the menu labels.
 *
 * Since extended cpts only changes the singular and plural names of the post type, we need to manually edit the $menu and $submenu globals to override the defaults.
 */
function change_menu_labels() {
	global $menu, $submenu;

	$pos = false;

	foreach ( $menu as $index => $menu_list ) {
		if ( false === $pos ) {
			if ( 'Ccg Cards' === $menu_list[0] ) {
				$pos = $index;
				break;
			}
		}
	}

	$menu[ $pos ][0] = esc_html__( 'CCG Manager', 'ccg-manager' );
	$submenu['edit.php?post_type=ccg_card'][5][0] = esc_html__( 'Cards', 'ccg-manager' );
	$submenu['edit.php?post_type=ccg_card'][10][0] = esc_html__( 'Add Card', 'ccg-manager' );
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

/**
 * Change dashboard glance label
 *
 * This overrides the dashboard glance name for the Cards post type since it defaults to CCG Manager because we overrode it earlier.
 *
 * @param  array $elements An array of elements.
 * @return array           The filtered array of elements.
 */
function change_dashboard_glance_label( $elements ) {
	foreach ( $elements as $pos => $element ) {
		if ( 0 !== stripos( $element, 'CCG Manager' ) ) {
			$elements[ $pos ] = str_replace( 'CCG Manager', __( 'Cards', 'ccg-manager' ), $element );
		}
	}

	return $elements;
}

/**
 * Return a filterable array of card rarities.
 *
 * @return array Array of rarities.
 */
function rarity() {
	/**
	 * Allow rarity to be filtered for different games.
	 *
	 * @var array
	 */
	$rarity = apply_filters( 'ccg_man_filter_rarity', [
		'c' => __( 'Common', 'ccg-manager' ),
		'u' => __( 'Uncommon', 'ccg-manager' ),
		'r' => __( 'Rare', 'ccg-manager' ),
		'm' => __( 'Mythic Rare', 'ccg-manager' ),
	] );

	return $rarity;
}
