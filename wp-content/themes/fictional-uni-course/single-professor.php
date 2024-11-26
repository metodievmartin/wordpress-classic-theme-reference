<?php get_header(); ?>


<?php while ( have_posts() ) : the_post(); ?>

	<?php page_banner(); ?>

    <div class="container container--narrow page-section">

        <div class="generic-content">
            <div class="row group">
                <div class="one-third">
					<?php the_post_thumbnail( 'professor-portrait' ); ?>
                </div>

                <div class="two-thirds">
					<?php
					$like_count = new WP_Query( array(
						'post_type'  => 'like',
						'meta_query' => array(
							array(
								'key'     => 'liked_professor_id',
								'compare' => '=',
								'value'   => get_the_ID(),
							),
						),
					) );

					$exists_status = 'no';
					$like_id       = '';

					if ( is_user_logged_in() ) {
						$exists_query = new WP_Query( array(
							'author'     => get_current_user_id(),
							'post_type'  => 'like',
							'meta_query' => array(
								array(
									'key'     => 'liked_professor_id',
									'compare' => '=',
									'value'   => get_the_ID(),
								),
							),
						) );

						if ( $exists_query->found_posts ) {
							$exists_status = 'yes';
							$like_id       = $exists_query->posts[0]->ID;
						}
					}
					?>

                    <span class="like-box" data-professor-id="<?php the_ID(); ?>"
                          data-exists="<?php echo $exists_status ?>"
                          data-like-id="<?php echo $like_id ?>">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <span class="like-count"><?php echo $like_count->found_posts; ?></span>
                    </span>
					<?php echo get_field( 'main_body_content' ) ?>
                </div>
            </div>
        </div>

		<?php $related_programs = get_field( 'related_programs' ); ?>

		<?php if ( $related_programs ) : ?>

            <hr class="section-break">
            <h2 class="headline headline--medium">Subject(s) Taught</h2>
            <ul class="link-list min-list">

				<?php foreach ( $related_programs as $program ) : ?>
                    <li>
                        <a href="<?php echo get_the_permalink( $program ); ?>"><?php echo get_the_title( $program ); ?></a>
                    </li>
				<?php endforeach; ?>

            </ul>

		<?php endif; ?>
    </div>

<?php endwhile; ?>

<?php get_footer(); ?>

