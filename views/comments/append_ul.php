<ul>
	<li id="comment-container-<?= $comment_id ?>">
		<div class="comment">
			<div class="author">
				<?= $author ?>
				<span class="date"><?= date('d.m.Y H:i', strtotime($addtime)) ?></span>            
		   </div>
					
		   <div class="comment_text"><?= $comment ?></div>
		   <div class="recomment-link clearfix">
				<a href="#" class="" id="post-1_cid-<?= $comment_id ?>">Ответить</a>
			</div>
		</div>

	</li>
</ul>