<?php
function university_post_types() {
	// Event Post Type
	register_post_type( 'event', array(
		'public'          => true,
		'show_in_rest'    => true,
		'has_archive'     => true,

		// Sets custom permissions for the 'event' post type by defining a unique capability.
		// The 'capability' setting allows specifying a custom permission (in this case, 'event'),
		// so only users with the assigned role/permission can manage events.
		// 'map_meta_cap' ensures WordPress correctly maps this capability to specific user roles and permissions.
		'capability_type' => 'event',
		'map_meta_cap'    => true,

		'menu_icon' => 'dashicons-calendar',
		'rewrite'   => array( 'slug' => 'events' ),
		'supports'  => array( 'title', 'editor', 'excerpt', ),
		'labels'    => array(
			'name'          => 'Events',
			'add_new'       => 'Add New Event',
			'add_new_item'  => 'Add New Event',
			'edit_item'     => 'Edit Event',
			'all_items'     => 'All Events',
			'singular_name' => 'Event',
		),
	) );

	// Program Post Type
	register_post_type( 'program', array(
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-awards',
		'rewrite'      => array( 'slug' => 'programs' ),
		'supports'     => array( 'title' ),
		'labels'       => array(
			'name'          => 'Programs',
			'add_new'       => 'Add New Program',
			'add_new_item'  => 'Add New Program',
			'edit_item'     => 'Edit Program',
			'all_items'     => 'All Programs',
			'singular_name' => 'Program',
		),
	) );

	// Professor Post Type
	register_post_type( 'professor', array(
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-welcome-learn-more',
		'supports'     => array( 'title', 'thumbnail' ),
		'labels'       => array(
			'name'          => 'Professors',
			'add_new'       => 'Add New Professor',
			'add_new_item'  => 'Add New Professor',
			'edit_item'     => 'Edit Professor',
			'all_items'     => 'All Professors',
			'singular_name' => 'Professor',
		),
	) );

	// Campus Post Type
	register_post_type( 'campus', array(
		'public'          => true,
		'show_in_rest'    => true,
		'has_archive'     => true,

		// Sets custom permissions for the 'campus' post type by defining a unique capability.
		// The 'capability' setting allows specifying a custom permission (in this case, 'campus'),
		// so only users with the assigned role/permission can manage events.
		// 'map_meta_cap' ensures WordPress correctly maps this capability to specific user roles and permissions.
		'capability_type' => 'campus',
		'map_meta_cap'    => true,

		'menu_icon' => 'dashicons-location-alt',
		'rewrite'   => array( 'slug' => 'campuses' ),
		'supports'  => array( 'title', 'editor', 'excerpt', ),
		'labels'    => array(
			'name'          => 'Campuses',
			'add_new'       => 'Add New Campus',
			'add_new_item'  => 'Add New Campus',
			'edit_item'     => 'Edit Campus',
			'all_items'     => 'All Campuses',
			'singular_name' => 'Campus',
		),
	) );

	// Notes Post Type
	register_post_type( 'note', array(
		'public'          => false, // will hide the notes on the front-end
		'show_ui'         => true, // will show the notes in the admin dashboard
		'show_in_rest'    => true,
		'supports'        => array( 'title', 'editor' ),

		// Sets custom permissions for the 'note' post type by defining a unique capability.
		// The 'capability' setting allows specifying a custom permission (in this case, 'note'),
		// so only users with the assigned role/permission can manage events.
		// 'map_meta_cap' ensures WordPress correctly maps this capability to specific user roles and permissions.
		'capability_type' => 'note',
		'map_meta_cap'    => true,

		'menu_icon' => 'dashicons-welcome-write-blog',
		'labels'    => array(
			'name'          => 'Notes',
			'add_new'       => 'Add New Note',
			'add_new_item'  => 'Add New Note',
			'edit_item'     => 'Edit Note',
			'all_items'     => 'All Notes',
			'singular_name' => 'Note',
		),
	) );
}

add_action( 'init', 'university_post_types' );

function university_adjust_queries( $query ) {
	if ( ! is_admin() && is_post_type_archive( 'program' ) && $query->is_main_query() ) {
		$query->set( 'posts_per_page', - 1 );
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}

	if ( ! is_admin() && is_post_type_archive( 'campus' ) && $query->is_main_query() ) {
		$query->set( 'posts_per_page', - 1 );
	}

	if ( ! is_admin() && is_post_type_archive( 'event' ) && $query->is_main_query() ) {
		$today = date( 'Ymd' );
		$query->set( 'meta_key', 'event_date' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'ASC' );
		$query->set( 'meta_query', array(
			array(
				'key'     => 'event_date',
				'compare' => '>=',
				'value'   => $today,
				'type'    => 'NUMERIC'
			)
		) );
	}
}

add_action( 'pre_get_posts', 'university_adjust_queries' );