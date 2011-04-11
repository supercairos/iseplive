<?php

$user_url = Config::URL_ROOT.Routes::getPage('student', array('username' => $username));

?>

<div id="post-comment-<?php echo $id; ?>" class="post-comment<?php
echo ' post-comment-attachment'.(isset($attachment_id) ? $attachment_id: '0');
?>">
	<a href="<?php echo $user_url; ?>" class="avatar"><img src="<?php echo $avatar_url; ?>" alt="" /></a>
	<a href="<?php echo Config::URL_ROOT.Routes::getPage('post_comment_delete', array('id' => $id)); ?>" class="post-comment-delete">x</a>
	<div class="post-comment-message">
		<a href="<?php echo $user_url; ?>" class="post-comment-username"><?php echo htmlspecialchars($firstname.' '.$lastname); ?></a>
		<?php echo Text::inHTML($message); ?>
		<div class="post-comment-info">
			<?php echo Date::easy(time()); ?>
                        &#183; <a href="javascript:;" onclick="Like.initPostComLike(<?php echo $post_id ?>, <?php echo $id ?>)" id="post-com-like-link-<?php echo $id ?>"><?php echo __('POST_LIKE_LINK'); ?></a>
                            <a href="javascript:;" onclick="Like.initPostComUnlike(<?php echo $post_id ?>, <?php echo $id ?>)" class="hidden" id="post-com-unlike-link-<?php echo $id ?>"><?php echo __('POST_UNLIKE_LINK'); ?></a>
                        &#183; <span id="post-com-like-new-<?php echo $id ?>" class="hidden"><?php echo __('POST_LIKE_USER').' '.__('POST_LIKE_END_SING_2') ?></span>
		</div>
	</div>
</div>
