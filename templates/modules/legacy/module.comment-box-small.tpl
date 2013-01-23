<table id="comments">
	<thead><tr><th>Author</th><th>Comment</th></tr></thead>			
	<tbody>
		{foreach from=$comments item=com name=comments}
		{cycle values=',alt' assign='class'}
		<tr class="{$class}">
               <td class="comment-author stamp">				
				<img src="{avatarfilename id=$com->avatar->getId()}" alt="" class="avatar-tiny" />
				<a href="#" class="link-user">{$com->meta->postedBy}</a>
			</td>
               <td class="comment">
                    <ul class="meta">
					<li>
						From <a href="{geturl controller='comment'}{$com->meta->resourceUrl}"><strong>{$com->meta->resourceTitle|truncate:50:"&hellip;"}</strong></a>
						on <a href="{geturl controller='comment'}{$com->meta->resourceUrl}{$com->meta->commentPageNum}#comment_{$com->comment_num}">Page {$com->meta->commentPageNum}</a>
					</li>
					<li class="timestamp">Posted {$com->meta->neatPostedDate} ({$com->meta->relativeDate})</li>	
                    </ul>
                    <div id="comment_body">
					{highlighttext|truncate:255:"&hellip;" text=$com->comment words=$words}
				</div>
               </td>        
          </tr>
		{/foreach}
	</tbody>
</table>
