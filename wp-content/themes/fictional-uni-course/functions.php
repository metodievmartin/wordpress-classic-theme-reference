<?php

require get_theme_file_path( '/includes/search-route.php' );

function university_custom_rest() {
	register_rest_field( 'post', 'author_name', array(
		'get_callback' => function () {
			return get_the_author();
		},
	) );

	register_rest_field( 'note', 'user_note_count', array(
		'get_callback' => function () {
			return count_user_posts( get_current_user_id(), 'note' );
		},
	) );
}

add_action( 'rest_api_init', 'university_custom_rest' );

function boilerplate_load_assets() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'theme-main-js', get_theme_file_uri( '/build/index.js' ), array( 'jquery' ), '1.1', true );
	wp_enqueue_script( 'google-map', '//maps.googleapis.com/maps/api/js?key=AIzaSyBeNEWg2Hssx68WzCxIj71JkgExM4mlbUo', null, '1.0' );

//	wp_enqueue_style( 'custom-google-fonts', '//fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap', array(), null );
//	wp_enqueue_style( 'font-awesome', get_theme_file_uri( '/assets/lib/font-awesome/css/fontawesome.min.css' ) );
//	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
//	wp_enqueue_style( 'theme-main-css', get_theme_file_uri( '/build/index.css' ) );
	wp_enqueue_style( 'theme-main-css', get_theme_file_uri( '/build/index.css' ) );
	wp_enqueue_style( 'theme-extra-css', get_theme_file_uri( '/build/style-index.css' ) );

	wp_localize_script( 'theme-main-js', 'universityData', array(
		'root_url' => get_site_url(),
		'nonce'    => wp_create_nonce( 'wp_rest' ),
	) );
}

add_action( 'wp_enqueue_scripts', 'boilerplate_load_assets' );

function dequeue_jquery_migrate( $scripts ) {
	if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
		$scripts->registered['jquery']->deps = array_diff(
			$scripts->registered['jquery']->deps,
			[ 'jquery-migrate' ]
		);
	}
}

add_action( 'wp_default_scripts', 'dequeue_jquery_migrate' );

function university_features() {
	register_nav_menus( array(
		'header-menu'           => __( 'Header Menu', 'university' ),
		'footer-primary-menu'   => __( 'Footer Primary Menu', 'university' ),
		'footer-secondary-menu' => __( 'Footer Secondary Menu', 'university' )
	) );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	add_image_size( 'professor-landscape', 400, 260, true );
	add_image_size( 'professor-portrait', 480, 650, true );
	add_image_size( 'page-banner', 1500, 350, true );
}

add_action( 'after_setup_theme', 'university_features' );

// Redirect subscriber accounts out of admin and onto homepage
add_action( 'admin_init', 'redirect_subs_to_frontpage' );

function redirect_subs_to_frontpage() {
	$our_current_user = wp_get_current_user();

	if ( count( $our_current_user->roles ) === 1 && $our_current_user->roles[0] === 'subscriber' ) {
		wp_redirect( site_url( '/' ) );
		exit;
	}
}

// Removes the admin bar for subscribers only
add_action( 'wp_loaded', 'remove_admin_bar_for_subscribers' );

function remove_admin_bar_for_subscribers() {
	$our_current_user = wp_get_current_user();

	if ( count( $our_current_user->roles ) === 1 && $our_current_user->roles[0] === 'subscriber' ) {
		show_admin_bar( false );
	}
}

// Customise Login Screen
add_filter( 'login_headerurl', 'our_header_url' );

function our_header_url() {
	return esc_url( site_url( '/' ) );
}

//Tells WordPress to load style scripts in the Login Screen
add_action( 'login_enqueue_scripts', 'our_login_css' );

function our_login_css() {
	wp_enqueue_style( 'theme-main-css', get_theme_file_uri( '/build/index.css' ) );
	wp_enqueue_style( 'theme-extra-css', get_theme_file_uri( '/build/style-index.css' ) );
}

// Changes the the default title in the Login Screen
add_filter( 'login_headertext', 'our_login_title' );

function our_login_title() {
	// Fetches the name from DB
	return get_bloginfo( 'name' );
}

// HTML Content
function page_banner( $args = null ) {
//	$page_banner_image = get_field( 'page_banner_background_image' );
//	$page_banner_image['sizes']['page-banner']
	$page_banner_image = get_theme_file_uri( '/images/ocean.jpg' );
	$banner_title      = get_the_title();
	$banner_subtitle   = get_field( 'page_banner_subtitle' );

	if ( isset( $args['title'] ) ) {
		$banner_title = $args['title'];
	}

	if ( isset( $args['subtitle'] ) ) {
		$banner_subtitle = $args['subtitle'];
	}

	if ( isset( $args['photo'] ) ) {
		$banner_subtitle = $args['photo'];
	} else if ( get_field( 'page_banner_background_image' ) && ! is_archive() && ! is_home() ) {
		$page_banner_image = get_field( 'page_banner_background_image' )['sizes']['page-banner'];
	}

	?>

    <div class="page-banner">
        <div class="page-banner__bg-image"
             style="background-image: url(<?php echo $page_banner_image; ?>)"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $banner_title ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $banner_subtitle ?></p>
            </div>
        </div>
    </div>

	<?php
}

function university_map_api( $api ) {
	$api['key'] = 'AIzaSyBeNEWg2Hssx68WzCxIj71JkgExM4mlbUo';

	return $api;
}

add_filter( 'acf/fields/google_map/api', 'university_map_api' );

// Force note posts to be private
add_filter( 'wp_insert_post_data', 'make_note_private', 10, 2 );

function make_note_private( $data, $post_data ) {
	if ( $data['post_type'] === 'note' ) {
		// Enforces a limit how many Notes a user can have.
		// Checking for ID to make sure it's applied only for post creation and not for editing or deleting a note.
		if ( count_user_posts( get_current_user_id(), 'note' ) >= 5 && ! $post_data['ID'] ) {
			die( 'You have reached your note limit.' );
		}

		// Sanitizing the content before saving it in the DB
		$data['post_title']   = sanitize_text_field( $data['post_title'] );
		$data['post_content'] = sanitize_textarea_field( $data['post_content'] );
	}

	// Makes every created note private
	if ( $data['post_type'] === 'note' && $data['post_status'] !== 'trash' ) {
		$data['post_status'] = 'private';
	}

	return $data;
}

// === helper functions ====

function get_active_classes_for_page( $page_slug ) {
	$page    = get_page_by_path( $page_slug );
	$page_id = $page?->ID;

	if ( is_page( $page_slug ) || wp_get_post_parent_id( 0 ) === $page_id ) {
		return 'class="current-menu-item"';
	}
}

function get_active_classes_for_post( $post_type, $additional_check_for_page = - 1 ) {
	if ( get_post_type() === $post_type || is_page( $additional_check_for_page ) ) {
		return 'class="current-menu-item"';

	}
}

function get_excerpt_or_first_n_words( $number_of_words = 10 ) {
	if ( has_excerpt() ) {
		return get_the_excerpt();
	}

	return wp_trim_words( get_the_content(), $number_of_words );
}
