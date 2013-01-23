<table class="results commentTiny">
	<thead></thead>			
	<tbody>
		{foreach from=$comments item=com name=comments}
		{cycle values=',alt' assign='class'}
		<tr class="{$class}">
			<th class="th_avatar" rowspan="2">
				{if ($com->avatar->getId() != 0)}
					<img src="{avatarfilename id=$com->avatar->getId()}" alt="" class="avatar-tiny" />
				{else}
					<img src="/images/avatar-blank.jpg" alt="" class="avatar-tiny" width="80" height="80" />
				{/if}
			</th>
               <th><strong><a href="{geturl controller = 'profile'}{$com->meta->postedBy}">{$com->meta->postedBy}</a></strong> {$com->meta->relativeDate}</th>
			<th class="th_readMore">
				<a href="{geturl controller='comment'}{$com->meta->resourceUrl}{$com->meta->commentPageNum}#comment_{$com->comment_num}">More...</a>
			</th>
		</tr>
		<tr class="{$class}">
            <td colspan="2">
				{$com->comment|strip|strip_tags|truncate:255:"&hellip;":false}
			</td>
          </tr>
		{/foreach}
	</tbody>
</table>
