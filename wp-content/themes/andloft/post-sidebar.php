<?php
/*
@package WordPress
@subpackage FolioGrid Pro
Single Post Template: Post with Sidebar
 */

get_header(); ?>

<div id="coreContent" class="clearfix">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>">

      <div id="content" class="floatL">
            <div class="singlepost">
                <h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
				<?php the_content(); ?>
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
     </div>
     
	<?php endwhile; else: ?>
    
	<div class="content clearfix">
        <div>
        	<h1>Oops!</h1>
			<p>Sorry, no posts matched your criteria.</p>
        </div>
    </div>

	<?php endif; ?>
    </div>
    
<?php get_sidebar(); ?>
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
