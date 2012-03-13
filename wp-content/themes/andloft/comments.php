<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments">This post is password protected. Enter the password to view comments.</p>

			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	$oddcomment = 'class="alt" ';
?>

<!-- You can start editing here. -->
<div id="comments" class="clearfix">
<?php if ($comments) : ?>

  <h4>Comments</h4>

<?php foreach ($comments as $comment) : ?>

    <div class="commentEntry clearfix">
      <?php echo get_avatar( $comment->comment_author_email, $size = '80', $comment->comment_author_link);?>
        <div class="commentContent" id="comment-<?php comment_ID() ?>">
        <?php if ($comment->comment_approved == '0') : ?>
  			<em>Your comment is awaiting moderation.</em>
  			<?php endif; ?>
  			
        <?php comment_text() ?>
        
       
     </div>
     <div class="commentMeta clearfix clear">
        posted by <cite><?php comment_author_link() ?></cite> on <a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('m.d.y') ?></a> at <?php comment_time() ?> <?php edit_comment_link('edit','&nbsp;&nbsp;',''); ?>
      </div>
    </div>
      
	<?php endforeach; /* end for each comment */ ?>
     
  
  <?php if ('closed' == $post->comment_status) : ?>
	  <div class="nocomments">Comments are closed.</div>
		<?php endif; ?>
  	

 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<div class="nocomments">Comments are closed.</div>

	<?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>


<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">


  <div class="leaveComment clearfix">
    <?php if ( $user_ID ) : ?>

    <p class="loggedin">Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Logout &raquo;</a></p>


    <?php endif; ?>
    
    <h4>Leave a comment</h4>
      <div class="commentForm clearfix">
        <div class="commentFormLeft">
            <label>Your Name: <em>Required</em></label> <input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" />
            <label>Your Email: <em>Required, not published</em></label> <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" />
            <label>Your Website:</label> <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" />
        </div>
        <div class="commentFormRight">
            <label>Comment:</label>
            <textarea name="comment" id="comment" cols="50" rows="20"></textarea>
            <input type="submit" value="Post Comment" /> <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
        </div>
      </div>
  </div>

<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>


<?php endif; // if you delete this the sky will fall on your head ?>
  </div>
