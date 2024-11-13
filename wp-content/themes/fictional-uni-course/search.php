<?php get_header(); ?>

<?php
page_banner(
	array(
		'title'    => 'Search Results',
		'subtitle' => 'You search results for: "' . esc_html( get_search_query( false ) ) . '"',
	)
);
?>

<div class="container container--narrow page-section">

	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', get_post_type() ) ?>

		<?php endwhile; ?>

		<?php echo paginate_links() ?>

	<?php else : ?>

        <h2 class="headline headline--small-plus">Sorry, no results matched your criteria.</h2>

	<?php endif; ?>

	<?php get_search_form() ?>

</div>

<?php get_footer(); ?>
