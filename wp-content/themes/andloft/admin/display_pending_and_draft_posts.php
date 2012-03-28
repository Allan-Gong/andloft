<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<?php

require_once( '../../../../wp-load.php' );
require_once( ABSPATH . 'wp-includes/post.php' );

global $user_ID; 

if ( !$user_ID ) {
	die('No user found. Permission denied.');
}

if ( $user_ID ) {
	if ( !current_user_can('level_10') ) {
		die('Not admin. Permission denied.'); 
	}
}

if ( $_POST['submit'] ) {

	// var_dump($_POST);

	$posts = $_POST['post'];

	if ( !is_array($posts) ) {
		return;
	}

	foreach ( $posts as $post_data ) {
		$post_ID  = $post_data['ID'];
		$post_title  = $post_data['title'];
		$post_action = $post_data['action'];

		// print $post_ID . ': ' . $post_title . ' | ' . $post_action . '<br />';

		if ( $post_action == 'publish' ) {
			  $post_id = wp_update_post( array(
			    'ID'           => $post_ID,
			    'post_title'   => $post_title,
			    'post_status'  => 'publish',
			  ));

			  if ( $post_id ) {
				print 'Success publishing post: ' . $post_ID . '<br />';
			  } else {
			  	print 'Failed publishing post: ' . $post_ID . '<br />';
			  }
		}

		if ( $post_action == 'trash' ) {
			if( wp_delete_post( $post_ID, false ) ) {
				print 'Success trashing post: ' . $post_ID . '<br />';
			} else {
				print 'Failed trashing post: ' . $post_ID . '<br />';
			}
		}
	}

	print 'Finish processing pending posts<br />';
}

$pending_posts = get_posts(array(
	'post_status' => 'pending',
	'numberposts' => 1000,
	'order'       => 'ASC',
));

$draft_posts = get_posts(array(
	'post_status' => 'draft',
	'numberposts' => 1000,
	'order'       => 'ASC',
));

$posts = array_merge($pending_posts, $draft_posts);

if ( empty($posts) ) {
	echo 'No pending/draft post found!';
	return;
}

?>

<form action="<?php echo $PHP_SELF; ?>" method="post">

	<?php foreach ( $posts as $post ) : ?>

		<table border="2" style="width: 100%; height:100px; table-layout: fixed; margin-bottom: 2em; ">
			<tr>
				<th width="5%">ID</th>
				<th width="500">Content</th>
				<th width="5%">Title</th>
				<th>New Title</th>
				<th colspan="2" width="130">Action</th>
			</tr>
			<tr valign="middle" align="center">
				<td>
					<?php echo $post->ID ?>
					<input type="hidden" name="post[<?php echo $post->ID ?>][ID]" value="<?php echo $post->ID ?>" />
				</td>
				<td>
					<div style="width:500px; height:200px; overflow: auto;">
						<?php echo $post->post_content ?>
					</div>
				</td>
				<td><?php echo $post->post_title ?></td>
				<td><input type="text" name="post[<?php echo $post->ID ?>][title]" value="" size="35" /></td>
				<td><input id="id_publish_<?php echo $post->ID ?>" type="radio" name="post[<?php echo $post->ID ?>][action]" value="publish" /> <label for="id_publish_<?php echo $post->ID ?>">Publish</label></td>
				<td><input id="id_trash_<?php echo $post->ID ?>" type="radio" name="post[<?php echo $post->ID ?>][action]" value="trash" /> <label for="id_trash_<?php echo $post->ID ?>">Trash</label></td>
			</tr>
		</table>
	<?php endforeach; ?>

	<input type="submit" name="submit" value="Submit" /> 
</form>
</html>