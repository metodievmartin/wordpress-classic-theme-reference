<?php get_header(); ?>


<?php while ( have_posts() ) : the_post(); ?>

	<?php page_banner(); ?>

    <div class="container container--narrow page-section">

        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link( 'program' ); ?>">
                    <i class="fa fa-home" aria-hidden="true"></i> All Programs
                </a>
                <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        </div>

        <div class="generic-content">
			<?php the_field( 'main_body_content' ); ?>
        </div>

        <!-- Related Professors Section -->

		<?php
		$related_professors = new WP_Query( array(
			'post_type'      => 'professor',
			'posts_per_page' => - 1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => 'related_programs',
					'compare' => 'LIKE',
					'value'   => '"' . get_the_ID() . '"',
				)
			)
		) );
		?>

		<?php if ( $related_professors->have_posts() ): ?>

            <hr class="section-break">
            <h2 class="headline headline--medium"><?php the_title(); ?> Professors</h2>

            <ul class="professor-cards">

				<?php while ( $related_professors->have_posts() ) : $related_professors->the_post(); ?>

                    <li class="professor-card__list-item">
                        <a class="professor-card" href="<?php the_permalink(); ?>">
                            <img src="<?php the_post_thumbnail_url( 'professor-landscape' ); ?>" alt=""
                                 class="professor-card__image">
                            <span class="professor-card__name"><?php the_title(); ?></span>
                        </a>
                    </li>

				<?php endwhile; ?>

            </ul>

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

        <!-- Related Upcoming Events Section -->

		<?php
		$today                   = date( 'Ymd' );
		$related_upcoming_events = new WP_Query(
			array(
				'post_type'      => 'event',
				'posts_per_page' => 2,
				'meta_key'       => 'event_date',
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
				'meta_query'     => array(
					array(
						'key'     => 'event_date',
						'compare' => '>=',
						'value'   => $today,
						'type'    => 'NUMERIC'
					),
					array(
						'key'     => 'related_programs',
						'compare' => 'LIKE',
						'value'   => '"' . get_the_ID() . '"',
					)
				)
			) );
		?>

		<?php if ( $related_upcoming_events->have_posts() ): ?>

            <hr class="section-break">
            <h2 class="headline headline--medium">Upcoming <?php the_title(); ?> Events</h2>

			<?php while ( $related_upcoming_events->have_posts() ) : $related_upcoming_events->the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'event' ); ?>

			<?php endwhile; ?>

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

        <!-- ========  Related Campuses Section  ======== -->

		<?php $related_campuses = get_field( 'related_campus' ) ?>

		<?php if ( $related_campuses ): ?>
            <hr class="section-break">

            <h2 class="headline headline--medium"><?php the_title(); ?> is Available At These Campuses:</h2>

            <ul class="min-list link-list">

				<?php foreach ( $related_campuses as $campus ) : ?>
                    <li>
                        <a href="<?php echo get_the_permalink( $campus ); ?>"><?php echo get_the_title( $campus ); ?></a>
                    </li>
				<?php endforeach; ?>

            </ul>

		<?php endif; ?>

    </div>

<?php endwhile; ?>

<?php get_footer(); ?>

