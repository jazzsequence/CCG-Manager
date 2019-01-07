<?php
/**
 * Plugin Name: CCG Manager
 * Description: A WordPress plugin to manage your <abbr title="Collectable Card Game">CCG</abbr> collection.
 * Author: Chris Reynolds
 * Author URI: http://chrisreynolds.io
 * Plugin URI: https://github.com/jazzsequence/CCG-Manager
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Version: 0.3
 *
 * @package CCGManager
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

namespace CCGManager;

require_once __DIR__ . '/namespace.php';
require_once __DIR__ . '/meta/namespace.php';
require_once __DIR__ . '/vendor/cmb2/cmb2/init.php';
require_once __DIR__ . '/vendor/johnbillion/extended-cpts/extended-cpts.php';

add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Meta\\bootstrap' );









// TODO: do this the right way in register_post_type.
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
