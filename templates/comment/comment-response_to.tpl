<div id="depth_{$depth}" class="parent_comment" style="display: none;">
     <ul>
          {if $replyToId > 0}
          	<div class="quote-source">
				<em>Response to {$replyAuthorName} in post #{$replyCommentNumber}</em> 
				<a onclick="showCommentReply(this,{$replyToId}); return false;" class="tags parent_comment_reference" id="{$commentId}" href="{geturl controller='comment'}{$rsrcId}/{$rsrcUrl}/{$pageNumber}#comment_{$commentNum}">Show</a>
			</div>
          {/if}
          <!--li>by {$commentUserName}</li-->
          <div class="thread"> </div>
          <li>{$commentText|markdown}</li>
     </ul>
</div>