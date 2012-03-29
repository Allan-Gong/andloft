<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php bloginfo('name'); ?>: <?php bloginfo('description'); ?></title>
        
        <!-- Usual WP META -->
        <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
        <link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
        <link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php bloginfo('atom_url'); ?>" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        
        <!-- CSS -->
        <link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" type="text/css" media="screen" />
		
		<!-- WP Head -->
    	<?php wp_head(); ?>

		<script type="text/javascript">
			jQuery.noConflict();
		</script>	

	</head>
    
    <body <?php body_class(); ?>>
    
    <div id="header">
        <div class="blogInfo">
            <?php
			// if header image has been uploaded, display that, else display title and description
			if(get_option('fgp_header_image')!='')
			{
				?><a href="<?php bloginfo('url'); ?>"><img src="<?=get_option('fgp_header_image');?>" alt="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>" /></a><?php	
			}
			else
			{
				?><p><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a><br /><?php bloginfo('description'); ?></p><?php
			}
			?>
		</div>
        <div class="stuffing">
            <div class="pages">
                <!-- <h3>Menu</h3> -->
                <ul>
                    <li class="first"><a href="<?php bloginfo('url'); ?>">Home</a></li>
                    <?php wp_list_pages('title_li=&depth=1'); ?>
                </ul>
            </div>

            <div class="categories">
                <h3>Categories</h3>
                <?php 
                    wp_dropdown_categories(array(
                        'show_option_none' => 'Select category',
                        'show_count'       => 1,
                        'name'             => 'category',
                        'taxonomy'         => 'category',
                    )); 
                ?>       
            </div>

            <div class="categories">
                <h3>Tags</h3>
                <?php 
                    // wp_dropdown_categories(array(
                    //     'show_option_none' => 'Select tag',
                    //     'show_count'       => 1,
                    //     'name'             => 'post_tag',
                    //     'taxonomy'         => 'post_tag',
                    // ));
                ?>

                <?php $tags = get_tags(); ?>

                 <select id="post_tag" name="post_tag" class="postform">
                    <option value="-1">Select tag</option>
                    
                    <?php foreach ( $tags as $tag ) : ?>
                    
                    <option class="level-0" value="<?php echo $tag->slug; ?>"><?php echo $tag->name; ?>&nbsp;&nbsp;(<?php echo $tag->count; ?>)</option>
                    
                    <?php endforeach; ?>
                </select>

            </div>

            <div class="categories">
                
                <h3>Archives</h3>
                <select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
                    <option value=""><?php echo attribute_escape(__('Select Month')); ?></option>
                    <?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?> </select>
            </div>
        </div>
        
    </div>

    <div id="foliogrid"></div>
	