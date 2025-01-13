<?php
/*
 * Plugin Name: CCG Manager
 * Description: A WordPress plugin to manage your <abbr title="Collectable Card Game">CCG</abbr> collection.
 * Author: Chris Reynolds
 * Author URI: http://chrisreynolds.io
 * Plugin URI: http://museumthemes.com
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Version: 0.2.1
 * GitHub Plugin URI: jazzsequence/CCG-Manager
 * Primary Branch: main
 */

/*
 	Copyright (C) 2013 Chris Reynolds | hello@chrisreynolds.io

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

function ccg_man_register_post_type() {
	$labels = array(
		'name' => __( 'CCG Manager', 'ccg-manager' ),
		'singular_name' => __( 'Card', 'ccg-manager' ),
		'add_new' => __( 'Add New Card', 'ccg-manager' ),
		'add_new_item' => __( 'Add New Card', 'ccg-manager' ),
		'edit_item' => __( 'Edit Card', 'ccg-manager' ),
		'new_item' => __( 'New Card', 'ccg-manager' ),
		'view' => __( 'View Card', 'ccg-manager' ),
		'view_item' => __( 'View Card', 'ccg-manager' ),
		'search_items' => __( 'Search Cards', 'ccg-manager' ),
		'not_found' => __( 'No cards found', 'ccg-manager' ),
		'not_found_in_trash' => __( 'No cards found in the trash', 'ccg-manager' )
	);
	register_post_type( 'ccg_card',
		array(
			'labels' => $labels,
			'public' => true,
			'rewrite' => array( 'slug' => 'card', 'with_front' => 'false' ),
			'supports' => array( 'title', 'editor', 'revisions', 'thumbnail' )
		)
	);
}
add_action( 'init', 'ccg_man_register_post_type' );

function ccg_man_taxonomies() {
	register_taxonomy( 'ccg_collection', 'ccg_card',
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => __( 'Collections', 'ccg-manager' ),
				'singular_name' => __( 'Collection', 'ccg-manager' ),
				'edit_item' => __( 'Edit Collection', 'ccg-manager' ),
				'update_item' => __( 'Update Collection', 'ccg-manager' ),
				'add_new_item' => __( 'Add New Collection', 'ccg-manager' )
			),
			'query_var' => 'ccg_collection',
			'rewrite' => array( 'slug' => 'collection' )
		)
	);

	register_taxonomy('ccg_series', array('ccg_card'), array(
		'hierarchical' => true,
		'labels' => array(
			'name' => __( 'Series', 'ccg-manager' ),
			'singular_name' => __( 'Series', 'ccg-manager' ),
			'edit_item' => __( 'Edit Series', 'ccg-manager' ),
			'update_item' => __( 'Update Series', 'ccg-manager' ),
			'add_new_item' => __( 'Add New Series', 'ccg-manager' )
		),
		'query_var' => 'ccg_series',
		'rewrite' => array( 'slug' => 'series' ),
	));
}
add_action( 'init', 'ccg_man_taxonomies' );

function ccg_man_card_image() {
    remove_meta_box( 'postimagediv', 'ccg_card', 'side' );
    add_meta_box('postimagediv', __('Card Image', 'ccg-manager'), 'post_thumbnail_meta_box', 'ccg_card', 'side', 'default');
}
add_action('do_meta_boxes', 'ccg_man_card_image');

function ccg_man_card_image_link() {
		add_filter( 'admin_post_thumbnail_html', 'ccg_man_set_featured' );
		add_filter( 'admin_post_thumbnail_html', 'ccg_man_remove_featured' );
}
add_action( 'init', 'ccg_man_card_image_link' );

function ccg_man_set_featured( $content ) {
	if ( 'ccg_card' == get_post_type() )
		return str_replace(__('Set featured image'), __('Add card image', 'ccg-manager'),$content);
}

function ccg_man_remove_featured( $content ) {
	if ( 'ccg_card' == get_post_type() )
		return str_replace(__('Remove featured image'), __('Remove card image', 'ccg-manager'),$content);
}

function ccg_man_card_meta() {
	add_meta_box( 'ccg-man-meta', __( 'Card Information', 'ccg-manager' ), 'ccg_man_card_metabox', 'ccg_card', 'normal', 'low' );
}
add_action( 'admin_menu', 'ccg_man_card_meta' );

function ccg_man_card_metabox() {
	global $post;

	echo '<input type="hidden" name="ccg_man_noncename" id="ccg_man_noncename" value="' . wp_create_nonce( plugin_basename( __FILE__) ) . '" />';

	echo '<p><label for="cost"><strong>' . __( 'Cost', 'ccg-manager' ) . '</strong></label><br />';
	echo '<input size="5" type="text" name="cost" value="' . wp_filter_nohtml_kses(get_post_meta( $post->ID, 'cost', true )) . '" /></p>';

	echo '<p><label for="creature-type"><strong>' . __( 'Card Type', 'ccg-manager' ) . '</strong></label><br />';
	echo '<input size="15" type="text" name="creature-type" value="' . wp_filter_nohtml_kses( get_post_meta( $post->ID, 'creature-type', true ) ) . '" /></p>';

	echo '<p><label for="power"><strong>' . __( 'Power', 'ccg-manager' ) . '</strong></label><br />';
	echo '<input size="5" type="text" name="power" value="' . wp_filter_nohtml_kses( get_post_meta( $post->ID, 'power', true ) ) . '" /></p>';

	echo '<p><label for="rarity"><strong>' . __( 'Rarity', 'ccg-manager' ) . '</strong></label><br />';
	echo '<select name="rarity">';
	$selected = get_post_meta( $post->ID, 'rarity', true );
	foreach ( ccg_man_rarity() as $rarity ) {
		$label = $rarity['label'];
		$value = $rarity['value'];
		echo '<option value="' . $value . '" ' . selected( $selected, $value ) . '>' . $label . '</option>';
	}
	echo '</select>';
}

function ccg_man_save_card( $post_id, $post ) {
	$nonce = isset( $_POST['ccg_man_noncename'] ) ? $_POST['ccg_man_noncename'] : 'HhZjy5aDQ9utXM$CSN*sU*Y46tmBfCqskwzgg6CQK#te!^afgUnjfTTrWQ@2DN^zMv4VPw*kbNKUcNfubt^Pe%q5Fq#nmavJTTxa';

	if ( !wp_verify_nonce( $nonce, plugin_basename( __FILE__ ) ) ) {
		return $post->ID;
	}

	if ( 'ccg_card' == get_post_type() ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return $post->ID;
	}

	$meta_keys = array(
		'cost' => 'text',
		'creature-type' => 'text',
		'power' => 'text',
		'rarity' => 'rarity'
	);

	foreach ( $meta_keys as $meta_key => $type ) {
		if ( $post->post_type == 'revision' )
			return;
		if( isset( $_POST[ $meta_key ] ) ) {
			if ( $type == 'text' ) {
				$value = wp_kses( $_POST[ $meta_key ], array() );
			}
			if ( $type == 'rarity' ) {
				foreach ( ccg_man_rarity() as $rarity ) {
					$r_value = $rarity['value'];
					if ( $_POST[ $meta_key ] == $r_value ) {
						$value = wp_kses( $_POST[ $meta_key ], array() );
					}
				}
			}
			update_post_meta( $post->ID, $meta_key, $value );
		} else {
			delete_post_meta( $post->ID, $meta_key );
		}
	}
}
add_action( 'save_post', 'ccg_man_save_card', 1, 2 );

function ccg_man_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Card', 'ccg-manager' ),
		'ccg_series' => __( 'Series', 'ccg-manager' ),
		'ccg_collection' => __( 'Collection', 'ccg-manager' ),
		'creature-type' => __( 'Card Type', 'ccg-manager' ),
		'cost' => __( 'Cost', 'ccg-manager' ),
		'rarity' => __( 'Rarity', 'ccg-manager' )
	);
	return $columns;
}
add_filter( 'manage_edit-ccg_card_columns', 'ccg_man_columns' );

function ccg_man_card_columns( $column, $post_id ) {
	global $post;
	switch( $column ) {
		case 'ccg_series' :
			$terms = get_the_terms( $post_id, 'ccg_series' );

			if ( !empty($terms) ) {
				$out = array();
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
					esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'ccg_series' => $term->slug ), 'edit.php' ) ),
					esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'ccg_series', 'display' ) )
					);
				}
				echo join (', ', $out);
			} else {
				_e( 'No series found', 'ccg-manager' );
			}
			break;
		case 'ccg_collection' :
			$terms = get_the_terms( $post_id, 'ccg_collection' );

			if ( !empty($terms) ) {
				$out = array();
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
					esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'ccg_collection' => $term->slug ), 'edit.php' ) ),
					esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'ccg_collection', 'display' ) )
					);
				}
				echo join (', ', $out);
			} else {
				_e( 'No collections found', 'ccg-manager' );
			}
			break;
		case 'creature-type' :
			$type = get_post_meta( $post->ID, 'creature-type', true );

			if ( $type ) {
				$type = wp_filter_nohtml_kses( $type );
				echo $type;
			}
			break;
		case 'cost' :
			$cost = get_post_meta( $post->ID, 'cost', true );

			if ( $cost ) {
				$cost = wp_filter_nohtml_kses( $cost );
				echo $cost;
			}
			break;
		case 'rarity' :
			$rarity = get_post_meta( $post->ID, 'rarity', true );

			if ( $rarity ) {
				foreach ( ccg_man_rarity() as $r ) {
					$label = $r['label'];
					$value = $r['value'];

					if ( $rarity == $value ) {
						$label == wp_filter_nohtml_kses( $label );
						if ( $rarity == 'r' ) {
							echo '<span style="color: purple; font-weight: bold;">' . $label . '</span>';
						}
						elseif ( $rarity == 'u' ) {
							echo '<span style="color: gold; font-weight: bold;">' . $label . '</span>';
						}
						else {
							echo '<span style="font-weight: bold;">' . $label . '</span>';
						}
					}
				}
			} else {
				_e( 'No rarity set', 'ccg-manager' );
			}
			break;
	}
}
add_action( 'manage_ccg_card_posts_custom_column', 'ccg_man_card_columns', 10, 2 );

function ccg_man_rarity() {
	$rarity = array(
		'common' => array(
			'label' => __( 'Common', 'ccg-manager' ),
			'value' => 'c'
		),
		'uncommon' => array(
			'label' => __( 'Uncommon', 'ccg-manager' ),
			'value' => 'u'
		),
		'rare' => array(
			'label' => __( 'Rare', 'ccg-manager' ),
			'value' => 'r'
		)
	);
	return $rarity;
}

function ccg_man_admin_head_css() {
	global $post_type;
	?>
	<style type="text/css" media="screen">
		#menu-posts-ccg_card .wp-menu-image {
			background: url(<?php echo plugins_url( 'images/card-mini.png', __FILE__ ); ?>) no-repeat 6px -17px !important;
		}
		#menu-posts-ccg_card:hover .wp-menu-image, #menu-posts-ccg_card.wp-has-current-submenu .wp-menu-image {
			background: url(<?php echo plugins_url( 'images/card-mini.png', __FILE__ ); ?>) no-repeat 6px 6px !important;
		}
	<?php
		if ( $post_type == 'ccg_card' ) :
			echo '#icon-edit {
				background: url(' . plugins_url( 'images/card-icon-32.png', __FILE__ ) . ') no-repeat !important;
			}';
		endif;
}
add_action( 'admin_head', 'ccg_man_admin_head_css' );