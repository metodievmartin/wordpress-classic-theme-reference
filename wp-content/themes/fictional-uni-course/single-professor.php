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

