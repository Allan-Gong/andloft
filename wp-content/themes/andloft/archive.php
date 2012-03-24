<?php get_header(); ?>

	<div id="postwrapper">
	
		<?php /* If this is a category archive */ if (is_category()) { ?>
        <div class="post archiveTitle">
            <div>
                <h2 class="currentCat">You are currently browsing: <span><?php single_cat_title(); ?></span></h2>
                <?php echo category_description( $category ); ?>
                
                <h3>Browse categories:</h3>
                
                <ul class="list">
                <?php 
                wp_list_categories('title_li='); ?>
                </ul>
                
                <p class="back"><a href="<?php bloginfo('url'); ?>">&larr; Back to Homepage</a></p>
            </div>
        </div>
        
        <?php /* If this is a category archive */ } elseif (is_tag()) { ?>
        <div class="post archiveTitle">
            <div>
                <h2>Tagged <?php single_tag_title(); ?></h2>
                <p class="back"><a href="<?php bloginfo('url'); ?>">&larr; Back to Homepage</a></p>
            </div>
        </div>
        
        <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
        <div class="post archiveTitle">
            <div>
                <h2 class="spaced">Collection from <?php the_time('F, Y'); ?></h2>
                
                <h3>Browse archive(s):</h3>
                
                <ul class="list">
                <?php wp_get_archives(); ?>
        
                </ul>
                
                <p class="back"><a href="<?php bloginfo('url'); ?>">&larr; Back to Homepage</a></p>
            </div>
        </div> <?php } ?>
        
        <?php 
		
		if (have_posts()) : ?>

    		<?php while (have_posts()) : the_post(); ?>
            
            <div class="post <?php echo frogs_column_width($post->ID); ?> infinite" id="post-<?=$post->ID;?>">
                <div>
                    <div class="post-header">
    					<?php frogs_media($post->ID); ?>
                    </div>
            
                    <div class="post-content">
                        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
                        <p><?php frog_the_excerpt_reloaded(20, 'none', TRUE, '...', FALSE, 1); ?></p>
                        <p><a href="<?php the_permalink() ?>" class="bubble">View <?php the_title(); ?> <span>&rarr;</span></a></p>
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
