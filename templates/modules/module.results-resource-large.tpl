{ if $resources|@count > 0 }
<div class="results resourceLarge">
	{foreach from=$resources item=rsrc name=resources}
	{if $rsrc->meta->resourceName == 'admin' && !$identity->mod}
	   {* Skip display if it's in the admin section and the user isn't a moderator *}
	{else}
	{cycle values=',alt' assign='class'}
	<div class="result {$class}">
		<h3>
            {if $rsrc->meta->numOfCommnets > 0}
                {if $rsrc->meta->newComments}
                    <a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/{if $rsrc->meta->lastReadPage > 0}{$rsrc->meta->lastReadPage}#comment_{$rsrc->meta->lastReadComment}{/if}" title="Continue from comment #{$rsrc->meta->lastReadComment}">
				{else}
					<a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}" title="{$rsrc->meta->numOfCommnets} comments">
                {/if}
            {else}        
                <a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/" title="{$rsrc->title|escape}">
            {/if}
            <!-- Highlighted metadata -->
			{if $order == 'views' && $rsrc->meta->viewsLifetime > 0}
				<span class="highlight iconText iconEyeLarge">{$rsrc->meta->viewsLifetime}<span class="hidden"> views</span></span>
			{elseif $order == 'popular' && $rsrc->meta->voteNum > 0}
				<span class="highlight iconText iconThumbLarge">{$rsrc->meta->voteNum}<span class="hidden"> votes</span></span>
			{elseif ($controller == 'search' || $order == 'comment' || $order == 'activity' || $order == 'date') && $rsrc->meta->numOfCommnets > 0}
				<span class="highlight {if $rsrc->meta->newComments}callout{/if} iconText {if $rsrc->meta->newComments}iconRedBalloonLarge{else}iconBalloonLarge{/if}">{$rsrc->meta->numOfCommnets}<span class="hidden"> comments</span></span>
            {else}
                &nbsp;
            {/if}
            <!-- End highlight -->
            </a>
            
            <!-- Title of resource -->
            {if $rsrc->isClosed()}
			     <a class="iconText iconLockLarge" href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->title}</a>
            {else}
			     <a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->title}</a>
            {/if}
            <!-- End title -->
		</h3>
		{include file='modules/module.resource-actions.tpl'}
        <ul class="meta subheading">
            <li>Posted <strong>{$rsrc->meta->relativeDate}</strong></li> 
            <li>by <a href="{geturl controller = 'profile'}{$rsrc->meta->postedBy}">{$rsrc->meta->postedBy|truncate:18:"&hellip;"}</a></li>
        </ul>
        
        <!-- Event information -->
        {if $rsrc->meta->resourceName == 'event'}
        <h4>
            {if $rsrc->calendarLink}
                <a class="iconText iconCalendarSearchLarge" title="Find event on calendar" href="{geturl controller='calendar'}{$rsrc->calendarLink}">{$rsrc->neatStartDate}{if !empty($rsrc->neatEndDate)}&ndash;{$rsrc->neatEndDate}{/if}</a>
            {else}
                <a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->neatStartDate}{if !empty($rsrc->neatEndDate)}&ndash;{$rsrc->neatEndDate}{/if}</a>
            {/if}
            {if !empty($rsrc->locationsArray.0)}
                | <span>{$rsrc->locationsArray.0.description}</span>
            {/if}
        </h4>
        {/if}
        <!-- End event information -->
        
		<p class="excerpt">
			{ if $rsrc->image->getId() > 0 }
				<img class="img_thumbnail" src="{resourcethumbnail id=$rsrc->image->getId() w=50 h=50}" alt="{$rsrc->title}" id="img{$rsrc->meta->_id}" />
			{/if}
			{if !empty($search)}
				{highlighttext|truncate:255:"&hellip;" text=$rsrc->descrip words=$words}
			{else}
				{$rsrc->descrip|markdown|resourcefilter|strip|truncateclosetags:300:"&hellip;"}
			{/if}
		</p>
		<ul class="meta resultFooter">
			<li>
				<a href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/all/">{$rsrc->meta->resourceName|capitalize}</a> &gt;
          		<a href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/{$rsrc->meta->categoryUrl}/">{$rsrc->meta->categoryName|capitalize}</a>
			</li>
            {if $rsrc->meta->numOfCommnets > 0}
                {if $rsrc->meta->newComments}
                    <li class="iconText iconRedBalloonSmall">
                        {if $rsrc->meta->lastReadComment}
                            <a title="Continue from comment #{$rsrc->meta->lastReadComment}" href="{geturl controller='comment'}{$rsrc->meta->_id}/{$rsrc->resourceSeoUrlString}/{$rsrc->meta->lastReadPage}#comment_{$rsrc->meta->lastReadComment}">Last comment read (#{$rsrc->meta->lastReadComment})</a>
                            posted {$rsrc->meta->lastReadCommentDate} by <a href="{geturl controller = 'profile'}{$rsrc->meta->lastReadCommentUser}">{$rsrc->meta->lastReadCommentUser|truncate:18:"&hellip;"}</a>
                        {else}
                            <a title="There are new comments" href="{geturl controller='comment'}{$rsrc->meta->_id}/{$rsrc->resourceSeoUrlString}">There are new comments</a>
                        {/if}
                    </li> 
                {/if}            
    			{if $rsrc->meta->lastCommentUserName}
                   <li class="iconText iconBalloonSmall">
                        <a href="{geturl controller = 'comment'}{$rsrc->meta->lastCommentUrl}">Last comment</a> added 
                        <strong>{$rsrc->meta->lastCommentRelativeDate}</strong> by 
                        <a href="{geturl controller = 'profile'}{$rsrc->meta->lastCommentUserName}">{$rsrc->meta->lastCommentUserName|truncate:18:"&hellip;"}</a>
                   </li>
                {/if}
            {/if}
            {if $order != 'popular'}
			    <li class="highlight iconText iconThumbSmall">{$rsrc->meta->voteNum}<span class="hidden"> votes</span></li>
            {/if}
            {if $order != 'views'}
			    <li class="highlight iconText iconEyeSmall">{$rsrc->meta->viewsLifetime}<span class="hidden"> views</span></li>
            {/if}
		</ul>
	</div>
	{/if}
     {/foreach}
</div>
{/if}