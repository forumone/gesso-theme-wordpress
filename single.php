<?php get_header(); ?>

	<main id="main" class="main" role="main">	
    <?php if (has_visible_widgets('widget-area-1')) { $sidebarclasses = 'sidebar'; } else { $sidebarclasses = 'no-sidebar'; }?>
		<div class="l-main l-constrain <?php echo $sidebarclasses; ?>">
			
			<div class="l-main__content">
				<section>

					<?php if (have_posts()): while (have_posts()) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<header>
								<?php if ( has_post_thumbnail()) : // Check if Thumbnail exists ?>
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
										<?php the_post_thumbnail(); // Fullsize image for the single post ?>
									</a>
								<?php endif; ?>
								<h1 class="page-title">
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
								</h1>		
								<span class="date"><?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?></span>
								<span class="author"><?php _e( 'Published by', 'f1ux' ); ?> <?php the_author_posts_link(); ?></span>
								<span class="comments"><?php comments_popup_link( __( 'Leave a comment', 'f1ux' ), __( '1 Comment', 'html5blank' ), __( '% Comments', 'html5blank' )); ?></span>
							</header>							
							<?php the_content(); // Dynamic Content ?>
							<footer>
								<?php the_tags( __( 'Tags: ', 'f1ux' ), ', ', '<br>'); // Separated by commas with a line break at the end ?>
								<p><?php _e( 'Categorized in: ', 'f1ux' ); the_category(', '); // Separated by commas ?></p>
								<p><?php _e( 'Written by ', 'f1ux' ); the_author(); ?></p>
								<?php edit_post_link(); // Always handy to have Edit Post Links available ?>
								<?php comments_template(); ?>
							</footer>
						</article>

					<?php endwhile; ?>

					<?php else: ?>

						<article>
							<h1 class="page-title"><?php _e( 'Sorry, nothing to display.', 'f1ux' ); ?></h1>
						</article>

					<?php endif; ?>

				</section>
			</div>

			<div class="l-main__sidebar">
				<?php get_sidebar(); ?>
			</div>
		
		</div>
	</main>

<?php get_footer(); ?>