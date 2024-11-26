<?php

add_action( 'rest_api_init', 'university_like_routes' );

/**
 * Registers the REST API routes for managing "likes".
 *
 * REST API Endpoints:
 * - `POST /university/v1/manage-like: Create a "like"`
 * - `DELETE /university/v1/manage-like: Delete a "like"`
 *
 * @return void
 */
function university_like_routes() {
	register_rest_route( 'university/v1', 'manage-like', array(
		'methods'  => 'POST',
		'callback' => 'university_rest_create_like',
	) );

	register_rest_route( 'university/v1', 'manage-like', array(
		'methods'  => 'DELETE',
		'callback' => 'university_rest_delete_like',
	) );
}

/**
 * Handles the creation of a "like" for a professor.
 *
 * REST API Endpoint:
 * - `POST /university/v1/manage-like`
 *
 * @param WP_REST_Request $data The request object containing the `professor_id`.
 *
 * @return WP_REST_Response The response object containing success or error details.
 */
function university_rest_create_like( $data ) {
	$response = array(
		'success' => false,
	);

	if ( ! is_user_logged_in() ) {
		$response['error'] = 'Only logged in users can place a like.';

		return new WP_REST_Response( $response, 401 );
	}

	$professor_id = absint( $data['professor_id'] );

	// makes sure the ID is a valid integer and the ID belongs to a professor post type
	if ( ! $professor_id || get_post_type( $professor_id ) !== 'professor' ) {
		$response['error'] = 'Invalid professor ID';

		return new WP_REST_Response( $response, 400 );
	}

	// checks if the current user has already liked a professor with that ID
	$like_exists_query = new WP_Query( array(
		'author'     => get_current_user_id(),
		'post_type'  => 'like',
		'meta_query' => array(
			array(
				'key'     => 'liked_professor_id',
				'compare' => '=',
				'value'   => $professor_id,
			),
		),
	) );

	// checks for DB errors
	if ( is_wp_error( $like_exists_query ) ) {
		$response['error'] = 'Internal error';

		return new WP_REST_Response( $response, 500 );
	}

	// makes sure there's no previous like from this user
	if ( $like_exists_query->found_posts > 0 ) {
		$response['error'] = 'Already liked this professor';

		return new WP_REST_Response( $response, 400 );
	}

	$insert_result = wp_insert_post( array(
		'post_type'   => 'like',
		'post_status' => 'publish',
		'meta_input'  => array(
			'liked_professor_id' => $professor_id,
		),
	) );

	// checks for errors during insertion
	if ( $insert_result === 0 ) {
		$response['error'] = 'Error placing a like.';

		return new WP_REST_Response( $response, 500 );
	}

	$response['success'] = true;
	$response['like_id'] = $insert_result;

	return new WP_REST_Response( $response, 201 );
}

/**
 * Handles the deletion of a "like".
 *
 * REST API Endpoint:
 * - `DELETE /university/v1/manage-like`
 *
 * @param WP_REST_Request $data The request object containing the `like_id`.
 *
 * @return WP_REST_Response The response object containing success or error details.
 */
function university_rest_delete_like( $data ) {
	$response = array(
		'success' => false,
	);

	$like_id = absint( $data['like_id'] );

	// makes sure the ID is a valid integer and the ID belongs to a like post type
	if ( ! $like_id || get_post_type( $like_id ) != 'like' ) {
		$response['error'] = 'Invalid Like ID';

		return new WP_REST_Response( $response, 400 );
	}

	// makes sure the user is logged-in, and it's the author of the like
	if (
		! is_user_logged_in()
		|| get_current_user_id() != get_post_field( 'post_author', $like_id )
	) {
		$response['error'] = 'You do not have permissions to perform this action.';

		return new WP_REST_Response( $response, 403 );
	}

	$delete_result = wp_delete_post( $like_id, true );

	// checks for errors during deletion
	if ( ! $delete_result ) {
		$response['error'] = 'Error deleting the like.';

		return new WP_REST_Response( $response, 500 );
	}

	$response['success'] = true;

	return new WP_REST_Response( $response, 200 );
}