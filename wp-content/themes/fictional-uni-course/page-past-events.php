<?php get_header(); ?>

<?php
page_banner(
	array(
		'title'    => 'Past Events',
		'subtitle' => 'A recap of our past events.',
	)
);
?>

<div class="container container--narrow page-section">

	<?php
	$today       = date( 'Ymd' );
	$past_events = new WP_Query( array(
		'paged'      => get_query_var( 'paged', 1 ),
		'post_type'  => 'event',
		'meta_key'   => 'event_date',
		'orderby'    => 'meta_value_num',
		'order'      => 'ASC',
		'meta_query' => array(
			array(
				'key'     => 'event_date',
				'compare' => '<',
				'value'   => $today,
				'type'    => 'NUMERIC'
			)
		)
	) );
	?>

	<?php if ( $past_events->have_posts() ) : while ( $past_events->have_posts() ) : $past_events->the_post(); ?>

		<?php get_template_part( 'template-parts/content', 'event' ); ?>

	<?php endwhile; ?>

		<?php echo paginate_links( array(
			'total' => $past_events->max_num_pages,
		) ) ?>

	<?php else : ?>
        <p><?php esc_html__( 'Sorry, no events matched your criteria.' ); ?></p>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
