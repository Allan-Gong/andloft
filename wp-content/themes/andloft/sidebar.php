<div id="sidebar" class="floatR">

	<div class="tabbed clearfix">
        <!-- The tabs -->
        <ul class="tabs">
			<li class="t1"><a class="t1 tab" title="<?php _e('Recent'); ?>"><?php _e('Recent'); ?></a></li>
            <?php if (function_exists('wpp_get_mostpopular')) : ?><li class="t2"><a class="t2 tab" title="<?php _e('Popular'); ?>"><?php _e('Popular'); ?></a></li><?php endif; ?>
        </ul>
    
		<!-- tab 1 -->
        <div class="t1">
        	<ul>
				<?php wp_get_archives('type=postbypost&limit=5'); ?>
			</ul>
        </div>		
		
		<?php if (function_exists('wpp_get_mostpopular')) : ?>
        <!-- tab 2 -->
        <div class="t2">
		    
            <?php wpp_get_mostpopular('stats_comments=0&limit=10'); ?>
           
        </div>
    	<?php endif; ?>
    
    </div>
	
	<div class="sidebar_list">
		
		<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')) : ?>

		<?php endif; ?>
	</div>
</div>