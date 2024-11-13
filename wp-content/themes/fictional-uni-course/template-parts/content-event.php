<div class="event-summary">
	<?php $event_date = new DateTime( get_field( 'event_date' ) ); ?>

    <a class="event-summary__date event-summary__date t-center" href="<?php the_permalink(); ?>">
        <span class="event-summary__month"><?php echo $event_date->format( 'M' ) ?></span>
        <span class="event-summary__day"><?php echo $event_date->format( 'd' ) ?></span>
    </a>
    <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h5>
        <p>
			<?php echo get_excerpt_or_first_n_words( 18 ); ?>
            <a href="<?php the_permalink(); ?>" class="nu gray">Read more</a>
        </p>
    </div>
</div>