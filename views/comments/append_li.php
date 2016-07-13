<li>
    <div class="comment">
        <div class="author">
            <?php echo $author; ?>
            <span class="date"><?php echo date('d.m.Y H:i', strtotime($addtime)); ?></span>            
       </div>
                
       <div class="comment_text"><?php echo $comment; ?></div>
	   <div class="recomment-link clearfix">
			<a href="#" class="" id="post-1_cid-<?= $comment_id ?>">Ответить</a>
		</div>
    </div>
  

</li>