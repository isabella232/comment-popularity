<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove plugin settings
delete_option( 'comment_popularity_prefs' );

// Remove User meta
$args = array(
	'meta_query' => array(
		array(
			'key'     => 'hmn_user_expert_status',
			'compare' => 'EXISTS',
		),
	),
	'fields'     => 'all',
);

// Delete user expert status
$user_query = new WP_User_Query( $args );

if ( ! empty( $user_query->results ) ) {

	foreach ( $user_query->results as $user ) {

		delete_user_meta( $user->ID, 'hmn_user_expert_status' );

	}

}

$args = array(
	'meta_query' => array(
		array(
			'key'     => 'hmn_user_karma',
			'compare' => 'EXISTS',
		),
	),
	'fields'     => 'all',
);

$user_query = new WP_User_Query( $args );

if ( ! empty( $user_query->results ) ) {

	foreach ( $user_query->results as $user ) {

		delete_user_meta( $user->ID, 'hmn_user_karma' );

	}

}

$args = array(
	'meta_query' => array(
		array(
			'key'     => 'hmn_comments_voted_on',
			'compare' => 'EXISTS',
		),
	),
	'fields'     => 'all',
);

$user_query = new WP_User_Query( $args );

if ( ! empty( $user_query->results ) ) {

	foreach ( $user_query->results as $user ) {

		delete_user_meta( $user->ID, 'hmn_comments_voted_on' );

	}

}

// Delete comment meta.
$args = array(
	'karma' => '',
);

// Select all comments with karma > 0, and reset value to zero.
global $wpdb;

$wpdb->query(
	$wpdb->prepare(
		"UPDATE wp_comments SET comment_karma=0 WHERE comment_karma > %d", 0
	)
);

// Remove custom capabilities
$cp_plugin = HMN_Comment_Popularity::get_instance();

$roles = $cp_plugin->get_roles();

foreach ( $roles as $role ) {

	$role = get_role( $role );

	if ( ! empty( $role ) ) {

		if ( 'administrator' === $role ) {
			$role->remove_cap( 'manage_user_karma_settings' );
		}

		$role->remove_cap( 'vote_on_comments' );
	}

}