<?php get_header(); ?>
	
   <div id="postwrapper">

	   <?php 
	   if (have_posts()) : ?>
	
			<?php while (have_posts()) : the_post(); ?>
			
			<div class="post <?php echo frogs_column_width($post->ID); ?> infinite removeonceloaded" id="post-<?=$post->ID;?>">
				<div>
					<div class="post-header">
						<?php frogs_media($post->ID); ?>
					</div>
			
					<div class="post-content">
						<h2><a href="<?php the_permalink() ?>"><?php echo post_title_excerpt( the_title('','',false) ); ?></a></h2>
						<?php if ( show_chinese() ) : ?>
						<!-- <p><?php frog_the_excerpt_reloaded(20, 'none', TRUE, '...', FALSE, 1); ?></p> -->
						<?php endif ;?>
					</div>
			
					<div class="post-footer">
						<small>Published on <?php the_time('M d, Y'); ?><br />
						Category: <?php the_category(' | ') ?>
						</small>
					</div>
				</div>
		
			</div>
	
			<?php 
			endwhile; ?>
		
			<div class="nextPrev">
				<div class="post archiveTitle older">
					<?php next_posts_link('&larr; Older') ?>
				</div>
			</div>
	
		<?php else : ?>
		
			<div class="post">
				<div>
					<h1>Not Found</h1>
					<p>Sorry, but you are looking for something that isn't here.</p>
				</div>
			 </div>
	
		<?php endif; ?>
	
	</div>

<?php get_footer(); ?>
