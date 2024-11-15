<?php
///**
// * Plugin Name: Events Plugin
// * Description: This plugin will add a Custom Post Type for Events
// * Plugin URI: https://fictional-uni.com
// * Author: Victor Rusu
// * Version: 1
// **/
//
////* Don't access this file directly
//defined( 'ABSPATH' ) or die();
//
///*------------------------------------*\
//	Create Custom Post Types
//\*------------------------------------*/
//add_action( 'init', 'event_post_type' );
//function event_post_type() {
//	register_post_type( 'event', array(
//		'labels'             => array(
//			'name'                     => __( 'Events', 'fictional-uni' ),
//			'singular_name'            => __( 'Event', 'fictional-uni' ),
//			'add_new'                  => __( 'Add New', 'fictional-uni' ),
//			'add_new_item'             => __( 'Add New Event', 'fictional-uni' ),
//			'edit_item'                => __( 'Edit Event', 'fictional-uni' ),
//			'new_item'                 => __( 'New Event', 'fictional-uni' ),
//			'view_item'                => __( 'View Event', 'fictional-uni' ),
//			'view_items'               => __( 'View Events', 'fictional-uni' ),
//			'search_items'             => __( 'Search Events', 'fictional-uni' ),
//			'not_found'                => __( 'No events found.', 'fictional-uni' ),
//			'not_found_in_trash'       => __( 'No events found in trash.', 'fictional-uni' ),
//			'all_items'                => __( 'All Events', 'fictional-uni' ),
//			'archives'                 => __( 'Event Archives', 'fictional-uni' ),
//			'insert_into_item'         => __( 'Insert into Event', 'fictional-uni' ),
//			'uploaded_to_this_item'    => __( 'Uploaded to this Event', 'fictional-uni' ),
//			'filter_items_list'        => __( 'Filter Events list', 'fictional-uni' ),
//			'items_list_navigation'    => __( 'Events list navigation', 'fictional-uni' ),
//			'items_list'               => __( 'Events list', 'fictional-uni' ),
//			'item_published'           => __( 'Event published.', 'fictional-uni' ),
//			'item_published_privately' => __( 'Event published privately.', 'fictional-uni' ),
//			'item_reverted_to_draft'   => __( 'Event reverted to draft.', 'fictional-uni' ),
//			'item_scheduled'           => __( 'Event scheduled.', 'fictional-uni' ),
//			'item_updated'             => __( 'Event updated.', 'fictional-uni' ),
//		),
//		'has_archive'        => true,
//		'public'             => true,
//		'publicly_queryable' => true,
//		'show_ui'            => true,
//		'show_in_menu'       => true,
//		'show_in_rest'       => true,
//		'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'revisions' ),
//		'can_export'         => true,
//		'menu_icon'          => 'dashicons-calendar',
//	) );
//}
//
//
//// add event date field to events post type
//function add_post_meta_boxes() {
//	add_meta_box(
//		"post_metadata_events_post", // div id containing rendered fields
//		"Event Date", // section heading displayed as text
//		"post_meta_box_events_post", // callback function to render fields
//		"event", // name of post type on which to render fields
//		"side", // location on the screen
//		"low" // placement priority
//	);
//}
//
//add_action( "admin_init", "add_post_meta_boxes" );
//
//// save field value
//function save_post_meta_boxes() {
//	global $post;
//	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
//		return;
//	}
//	// if ( get_post_status( $post->ID ) === 'auto-draft' ) {
//	//     return;
//	// }
//	update_post_meta( $post->ID, "_event_date", sanitize_text_field( $_POST["_event_date"] ) );
//}
//
//add_action( 'save_post', 'save_post_meta_boxes' );
//
//// callback function to render fields
//function post_meta_box_events_post() {
//	global $post;
//	$custom              = get_post_custom( $post->ID );
//	$advertisingCategory = '';
//
//	if ( isset( $custom["_event_date"][0] ) ) {
//		$advertisingCategory = $custom["_event_date"][0];
//	}
//
//	echo "<input type=\"date\" name=\"_event_date\" value=\"" . esc_attr( $advertisingCategory ) . "\" placeholder=\"Event Date\">";
//}
//
//
//// generate shortcode
////add_shortcode('events-list', 'vm_events');
//function vm_events() {
//	global $post;
//	$args  = array(
//		'post_type'      => 'event',
//		'post_status'    => 'publish',
//		'posts_per_page' => 50,
//		'orderby'        => 'meta_value',
//		'meta_key'       => '_event_date',
//		'order'          => 'ASC'
//	);
//	$query = new WP_Query( $args );
//
//	$content = '<ul>';
//	if ( $query->have_posts() ):
//		while ( $query->have_posts() ): $query->the_post();
//
//
//			// trash event if old
//			$exp_date = get_post_meta( get_the_ID(), '_event_date', true );
//			// set the correct timezone
//			date_default_timezone_set( 'America/New_York' );
//			$today = new DateTime();
//			if ( $exp_date < $today->format( 'Y-m-d h:i:sa' ) ) {
//				// Update post
//				$current_post                = get_post( get_the_ID(), 'ARRAY_A' );
//				$current_post['post_status'] = 'trash';
//				wp_update_post( $current_post );
//			}
//
//
//			// display event
//			$content .= '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a> - ' . date_format( date_create( get_post_meta( $post->ID, '_event_date', true ) ), 'jS F' ) . '</li>';
//		endwhile;
//	else:
//		_e( 'Sorry, nothing to display.', 'fictional-uni' );
//	endif;
//	$content .= '</ul>';
//
//	return $content;
//}
//
///* Assign custom template to event post type*/
//function load_event_template( $template ) {
//	global $post;
//	if ( 'event' === $post->post_type && locate_template( array( 'single-event.php' ) ) !== $template ) {
//		return plugin_dir_path( __FILE__ ) . 'single-event.php';
//	}
//
//	return $template;
//}
//
//add_filter( 'single_template', 'load_event_template' );