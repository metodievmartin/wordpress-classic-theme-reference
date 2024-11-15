<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="//gmpg.org/xfn/11"/>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap">
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <div class="container">
        <h1 class="school-logo-text float-left">
            <a href="<?php echo site_url(); ?>"><strong>Fictional</strong> University</a>
        </h1>
        <span class="js-search-trigger site-header__search-trigger">
            <i class="fa fa-search" aria-hidden="true"></i>
        </span>
        <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
        <div class="site-header__menu group">
            <nav class="main-navigation">
                <ul>
                    <li <?php echo get_active_classes_for_page( 'about-us' ); ?>>
                        <a href="<?php echo site_url( '/about-us' ); ?>">About Us</a>
                    </li>
                    <li <?php echo get_active_classes_for_post( 'program' ); ?>>
                        <a href="<?php echo get_post_type_archive_link( 'program' ); ?>">Programs</a>
                    </li>
                    <li <?php echo get_active_classes_for_post( 'event', 'past-events' ); ?>>
                        <a href="<?php echo get_post_type_archive_link( 'event' ); ?>">Events</a>
                    </li>
                    <li <?php echo get_active_classes_for_post( 'campus' ); ?>>
                        <a href="<?php echo get_post_type_archive_link( 'campus' ); ?>">Campuses</a>
                    </li>
                    <li <?php echo get_active_classes_for_post( 'post' ); ?>>
                        <a href="<?php echo site_url( '/blog' ); ?>">Blog</a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( site_url( '/search' ) ); ?>">Search</a>
                    </li>
                </ul>
				<?php
				//				wp_nav_menu( array(
				//					'theme_location' => 'header-menu',
				//					'depth'          => 2,
				//				) );
				?>
            </nav>
            <div class="site-header__util">

				<?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( site_url( '/my-notes' ) ); ?>"
                       class="btn btn--small btn--orange float-left push-right">My Notes</a>
                    <a href="<?php echo wp_logout_url(); ?>"
                       class="btn btn--small btn--dark-orange float-left btn--with-photo">
                        <span class="site-header__avatar"><?php echo get_avatar( get_current_user_id(), 60 ); ?></span>
                        <span class="btn__text">Logout</span>
                    </a>
				<?php else: ?>
                    <a href="<?php echo wp_login_url() ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
                    <a href="<?php echo wp_registration_url(); ?>"
                       class="btn btn--small btn--dark-orange float-left">Sign Up</a>
				<?php endif; ?>

                <span class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
            </div>
        </div>
    </div>
</header>

