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
	Copyright (C) 2019 Chris Reynolds | hello@chrisreynolds.io

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
