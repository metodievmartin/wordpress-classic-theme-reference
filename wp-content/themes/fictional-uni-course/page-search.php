<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php page_banner(); ?>

    <div class="container container--narrow page-section">

		<?php
		$parent_id = wp_get_post_parent_id( get_the_ID() );

		if ( $parent_id ) : ?>

            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo esc_url( get_permalink( $parent_id ) ); ?>">
                        <i class="fa fa-home" aria-hidden="true"></i>
                        Back to <?php echo esc_html( get_the_title( $parent_id ) ); ?>
                    </a>
                    <span class="metabox__main"><?php the_title(); ?></span>
                </p>
            </div>

		<?php endif; ?>

		<?php
		// Check if the page is a parent page by retrieving its children.
		$child_pages = get_pages( array(
			'child_of' => get_the_ID(),
		) );

		if ( $parent_id || $child_pages ) : ?>

            <div class="page-links">
                <h2 class="page-links__title">
                    <a href="<?php echo esc_url( get_permalink( $parent_id ) ); ?>">
						<?php echo esc_html( get_the_title( $parent_id ) ); ?>
                    </a>
                </h2>
                
                <ul class="min-list">
					<?php
					$find_children_of = $parent_id ? $parent_id : get_the_ID();

					wp_list_pages( array(
						'title_li'    => null,
						'child_of'    => $find_children_of,
						'sort_column' => 'menu_order', // Sorts the pages by menu order as assigned in the admin panel
					) );
					?>
                </ul>
            </div>

		<?php endif; ?>

        <div class="generic-content">
			<?php get_search_form() ?>
        </div>
    </div>

<?php endwhile; ?>

<?php get_footer(); ?>
