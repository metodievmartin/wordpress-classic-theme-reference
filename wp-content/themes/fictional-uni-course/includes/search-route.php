<?php

/**
 * Registers a custom REST API endpoint for searching within the WordPress university site.
 *
 * Endpoint: `/wp-json/university/v1/search`
 * Method: GET
 * Params:
 * - `q` (string, optional) - The search term to query.
 * - `post_type` (string, optional) - Type of posts to search within (e.g., `post`, `page`, `course`).
 *
 * Example Request:
 * GET /wp-json/university/v1/search?q=biology&post_type=course
 *
 * @return void
 */
function university_custom_search_route() {
	register_rest_route( 'university/v1', 'search', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'university_search_results',
		'permission_callback' => '__return_true'
	) );
}

add_action( 'rest_api_init', 'university_custom_search_route' );

function university_search_results( $data ) {
	$query_args = [
		'post_type'      => [ 'professor', 'page', 'post', 'campus', 'event', 'program' ],
		's'              => sanitize_text_field( $data['q'] ),
		'posts_per_page' => - 1
	];

	$raw_search_results = new WP_Query( $query_args );

	$results = [
		'general_info' => [],
		'professors'   => [],
		'programs'     => [],
		'campuses'     => [],
		'events'       => [],
	];

	while ( $raw_search_results->have_posts() ) {
		$raw_search_results->the_post();

		$post_type = get_post_type();

		// Create a common structure for all post types
		$post_data = [
			'ID'        => get_the_ID(),
			'title'     => get_the_title(),
			'permalink' => get_permalink(),
			'post_type' => $post_type,
		];

		// Assign post data to appropriate section in $results array
		if ( $post_type == 'post' || $post_type == 'page' ) {
			$post_data['author_name']  = get_the_author();
			$results['general_info'][] = $post_data;
		}

		if ( $post_type == 'professor' ) {
			$post_data['image']      = get_the_post_thumbnail_url( 0, 'professor-landscape' );
			$results['professors'][] = $post_data;
		}

		if ( $post_type == 'program' ) {
			$related_campuses = get_field( 'related_campus' );

			if ( $related_campuses ) {
				foreach ( $related_campuses as $campus ) {
					$results['campuses'][] = [
						'ID'        => $campus->ID,
						'title'     => get_the_title( $campus ),
						'permalink' => get_the_permalink( $campus ),
					];
				}
			}

			$results['programs'][] = $post_data;
		}

		if ( $post_type == 'campus' ) {
			$results['campuses'][] = $post_data;
		}

		if ( $post_type == 'event' ) {
			$event_date               = new DateTime( get_field( 'event_date' ) );
			$post_data['month']       = $event_date->format( 'M' );
			$post_data['day']         = $event_date->format( 'd' );
			$post_data['description'] = get_excerpt_or_first_n_words( 18 );
			$results['events'][]      = $post_data;
		}
	}

	// Reset post data to avoid conflicts
	wp_reset_postdata();

	if ( $results['programs'] ) {
		$programs_meta_query = [ 'relation' => 'OR' ];

		foreach ( $results['programs'] as $program ) {
			$programs_meta_query[] = [
				'key'     => 'related_programs',
				'compare' => 'LIKE',
				'value'   => '"' . $program['ID'] . '"'
			];
		}

		$program_relationship_query = new WP_Query( [
			'post_type'  => [ 'professor', 'event' ],
			'meta_query' => $programs_meta_query
		] );

		while ( $program_relationship_query->have_posts() ) {
			$program_relationship_query->the_post();

			if ( get_post_type() == 'event' ) {
				$event_date          = new DateTime( get_field( 'event_date' ) );
				$results['events'][] = [
					'title'       => get_the_title(),
					'permalink'   => get_the_permalink(),
					'month'       => $event_date->format( 'M' ),
					'day'         => $event_date->format( 'd' ),
					'description' => get_excerpt_or_first_n_words(),
				];
			}

			if ( get_post_type() == 'professor' ) {
				$results['professors'][] = [
					'ID'        => get_the_ID(),
					'title'     => get_the_title(),
					'permalink' => get_the_permalink(),
					'post_type' => get_post_type(),
					'image'     => get_the_post_thumbnail_url( 0, 'professorLandscape' )
				];
			}

		}

		// removes duplicates entries
		$results['professors'] = array_values( array_unique( $results['professors'], SORT_REGULAR ) );
		$results['events']     = array_values( array_unique( $results['events'], SORT_REGULAR ) );
	}

	return $results;
}
