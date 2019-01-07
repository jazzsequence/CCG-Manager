<?php
/**
 * CCG Manager Meta
 *
 * @package CCGManager\Meta.
 */

namespace CCGManager\Meta;

use CCGManager as Main;

function bootstrap() {
	add_action( 'cmb2_init', __NAMESPACE__ . '\\add_cmb2_box' );
}

function add_cmb2_box() {
	$cmb = new_cmb2_box( [
		'id'           => 'ccg_man_metabox',
		'title'        => __( 'Card Information', 'ccg-manager' ),
		'object_types' => [ 'ccg_card' ],
		'priority'     => 'low',
	] );

	$cmb->add_field( [
		'name'       => __( 'Rarity', 'ccg-manager' ),
		'id'         => 'rarity',
		'type'       => 'select',
		'options'    => Main\rarity(),
	] );

	$cmb->add_field( [
		'name'       => __( 'Cost', 'ccg-manager' ),
		'id'         => 'cost',
		'type'       => 'text_small',
		'desc'       => __( 'Casting cost of card. E.G. in Magic: the Gathering an item costing 2 black and 2 colorless would be listed as <code>BB2</code>.', 'ccg-manager' ),
	] );

	$cmb->add_field( [
		'name'       => __( 'Card Type', 'ccg-manager' ),
		'id'         => 'creature-type',
		'type'       => 'text',
		'desc'       => __( 'The type of creature or card (e.g. Artifact, Sorcery, Enchantment, Planeswalker, etc).', 'ccg-manager' ),
	] );

	$cmb->add_field( [
		'name'       => __( 'Power / Toughness', 'ccg-manager' ),
		'id'         => 'power',
		'type'       => 'text_small',
		'desc'       => __( 'The power and toughness (or strength relative to the game) for the creature, if applicable.', 'ccg-manager' ),
	] );
}
