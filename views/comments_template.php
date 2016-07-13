<li id="comment-container-<?= $comment['id'] ?>">
    <div class="comment">
        <div class="author">
            <?= $comment['author'] ?>
            <span class="date"><?= date('d.m.Y H:i', strtotime($comment['addtime'])) ?></span>            
       </div>
                
       <div class="comment_text"><?= $comment['comment'] ?></div>
	   <div class="recomment-link clearfix">
			<a href="#" class="" id="post-1_cid-<?= $comment['id'] ?>">Ответить</a>
		</div>
    </div>
    
    <?php if(!empty($comment['childs'])):?>
    <ul>
        <?php echo getCommentsTemplate($comment['childs']);?>
    </ul>   
    <?php endif;?>
</li>
