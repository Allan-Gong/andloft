<?php
/*
Single Post Template: Full Width without sidebar
 */

get_header(); ?>

<div id="coreContent">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

      <div class="singlepost clearfix">
        <div>
             <h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
             <?php the_content(); ?>
    
            <p><small>Published on <?php the_time('M d, Y'); ?><br />
            Filed under: <?php the_category(',') ?><br />
            <?php the_tags('Tags: ', ', ', ''); ?> | <a href="<?php comments_link(); ?>"><?php comments_number('No Comments','1 Comment','% Comments'); ?></a>
            </small></p>
        </div>
    </div>
    
    <div class="pagination clearfix">
        <div class="prevpost"><?php previous_post_link('%link') ?></div>
        <div class="nextpost"><?php next_post_link('%link') ?></div>
    </div>
    
   <div class="singlepost clearfix">
        <div>
            <?php comments_template(); ?>
     	</div>
     </div>
     
	<?php endwhile; else: ?>
    
	<div class="singlepost clearfix">
        <div>
        	<h1>Oops!</h1>
			<p>Sorry, no posts matched your criteria.</p>
        </div>
    </div>

	<?php endif; ?>

</div>
     <div id="footer" class="clearfix">
     	<div class="left">
            <h3>Menu</h3>
            <ul>
                <li class="first"><a href="<?php bloginfo('url'); ?>">Home</a></li>
                <?php wp_list_pages('title_li=&depth=1'); ?>
            </ul>
            <p><?php if(get_option('fgp_footer_text')!=''){ echo get_option('fgp_footer_text'); }else{ ?>All work copyright <a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); } ?></a></p>
        </div>
        <p>FolioGrid - a <a href="http://www.frogsthemes.com">Premium Wordpress Theme</a> by FrogsThemes.com</p>
	</div>

<?php get_footer(); ?>
