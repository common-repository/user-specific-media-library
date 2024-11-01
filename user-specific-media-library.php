<?php
/*
Plugin Name: User-specific Media Library
Description: Limit users access to media items that they have uploaded, except users with the edit_others_posts capability - generally Editors and Administrators.
Version: 0.1
Author: Eric Andrew Lewis
Author URI: http://ericandrewlewis.com
*/

add_action( 'pre_get_posts', 'usml_filter_gallery_posts' );
add_filter( 'map_meta_cap', 'usml_map_use_others_media_cap', 10, 4 );

/**
 * Filter the posts query on attachment pages, to ensure that only the
 * specific user's attachments show up in the Media Library
 *
 * Hooked to 'pre_get_posts'. Bail out if we're not in a Media Library
 * request.
 *
 * @since 0.1
 */
function usml_filter_gallery_posts( $query ) {
	// This is also a surrogate check for DOING_AJAX
	if ( ! isset( $_POST['action'] ) || 'query-attachments' !== $_POST['action'] )
		return;

	// If the current user has our custom capability use_others_media, bail.
	if ( current_user_can( 'use_others_media' ) )
		return;

	// Return attachments with the author set to the current user
	$query->set( 'author', wp_get_current_user()->ID );
}

/**
 * Map the custom cap use_others_media to the manage_options cap
 *
  * @since 0.1
 */
function usml_map_use_others_media_cap( $caps, $cap, $user_id, $args ) {
	if ( $cap != 'use_others_media' )
		return $caps;
	$caps = array( 'edit_others_posts' );
	return $caps;
}