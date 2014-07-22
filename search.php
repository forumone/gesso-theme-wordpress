<?php get_header(); ?>

	<main id="main" class="main" role="main">	
    <?php if (has_visible_widgets('widget-area-1')) { $sidebarclasses = 'sidebar'; } else { $sidebarclasses = 'no-sidebar'; }?>
		<div class="l-main l-constrain <?php echo $sidebarclasses; ?>">
			
			<div class="l-main__content">
				<section>
					<h1 class="page-title"><?php echo sprintf( __( '%s Search Results for ', 'f1ux' ), $wp_query->found_posts ); echo get_search_query(); ?></h1>
					<?php get_template_part('templates/loop'); ?>
					<?php get_template_part('templates/pagination'); ?>
				</section>
			</div>

			<div class="l-main__sidebar">
				<?php get_sidebar(); ?>
			</div>
		
		</div>
	</main>

<?php get_footer(); ?>