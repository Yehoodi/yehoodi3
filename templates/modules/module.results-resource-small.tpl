{ if isset($resources) }

<div class="results resourceSmall">
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
                    {/if}
                {else}        
                    <a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/" title="{$rsrc->title|escape}">
                {/if}
                <!-- Highlighted metadata -->
                {if $order == 'views' && $rsrc->meta->viewsLifetime > 0}
                    <span class="highlight iconText iconEyeSmall">
                        {$rsrc->meta->viewsLifetime}<span class="hidden"> view{if $rsrc->meta->viewsLifetime != 1}s{/if}</span>
                    </span>
                {elseif $order == 'popular' && $rsrc->meta->voteNum > 0}
                    <span class="highlight iconText iconThumbSmall">    
                        {$rsrc->meta->voteNum}<span class="hidden"> vote{if $rsrc->meta->voteNum != 1}s{/if}</span>
                    </span>
                {elseif ($order == 'comment' || $order == 'activity' || $order == 'date') && $rsrc->meta->numOfCommnets > 0}
                    <span class="highlight {if $rsrc->meta->newComments}callout{/if} iconText {if $rsrc->meta->newComments}iconRedBalloonSmall{else}iconBalloonSmall{/if}">
                        {$rsrc->meta->numOfCommnets}<span class="hidden"> comment{if $rsrc->meta->numOfCommnets != 1}s{/if}</span>
                    </span>
                {else}
                    &nbsp;
                {/if}
                <!-- End highlight -->
                </a>
                
                <!-- Title of resource -->
                { if $rsrc->image->getId() > 0 }
                <a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/" title="{$rsrc->title|escape}">
                    <img class="img_thumbnail" src="{resourcethumbnail id=$rsrc->image->getId() w=50}" alt="{$rsrc->title}" id="img{$rsrc->meta->_id}" />
                </a>
                {/if}
                <a {if $rsrc->isClosed()}class="iconText iconLockSmall"{/if} href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/" title="{$rsrc->title|escape}">
                    {$rsrc->title|truncate:50:"&hellip;"}
                </a>
                <!-- End title -->
            </h3>
            			
            <ul class="meta">
                <li>
                {if $rsrc->meta->resourceName == 'event' && $rsrc->calendarLink }
                    <a class="iconText iconCalendarSearchLarge" title="Find event on calendar" href="{geturl controller='calendar'}{$rsrc->calendarLink}">{$rsrc->meta->categoryName|capitalize}</a>
                {else}
                    <a href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/{$rsrc->meta->categoryUrl}/" title="{$rsrc->meta->resourceName|capitalize} &gt; {$rsrc->meta->categoryName|capitalize}">{$rsrc->meta->categoryName|capitalize}</a>
                {/if}
                </li>
                
                {if $rsrc->meta->numOfCommnets > 0 && $order != 'date'}
                    {if $rsrc->meta->newComments}
                    <li class="iconText iconRedBalloonSmall">
                        {if $rsrc->meta->lastReadComment}
                            <a title="Continue from comment #{$rsrc->meta->lastReadComment}" href="{geturl controller='comment'}{$rsrc->meta->_id}/{$rsrc->resourceSeoUrlString}/{$rsrc->meta->lastReadPage}#comment_{$rsrc->meta->lastReadComment}">Last comment read (#{$rsrc->meta->lastReadComment})</a>
                            posted {$rsrc->meta->lastReadCommentDate} by <a href="{geturl controller = 'profile'}{$rsrc->meta->lastReadCommentUser}">{$rsrc->meta->lastReadCommentUser|truncate:18:"&hellip;"}</a>
                        {else}
                            <a title="There are new comments" href="{geturl controller='comment'}{$rsrc->meta->_id}/{$rsrc->resourceSeoUrlString}">There are new comments</a>
                        {/if}
                    </li>       			
                    {else}
                        {if $rsrc->meta->lastCommentUserName}
                        <li class="iconText iconBalloonSmall">
                            <a href="{geturl controller = 'comment'}{$rsrc->meta->lastCommentUrl}">Last comment</a> added 
                            <strong>{$rsrc->meta->lastCommentRelativeDate}</strong> by 
                            <a href="{geturl controller = 'profile'}{$rsrc->meta->lastCommentUserName}">{$rsrc->meta->lastCommentUserName|truncate:18:"&hellip;"}</a>
                        </li>
                        {/if}
                    {/if}
                {else}
                    <li>
                        Posted <strong>{$rsrc->meta->relativeDate}</strong> by 
                       <a href="{geturl controller = 'profile'}{$rsrc->meta->postedBy}">{$rsrc->meta->postedBy|truncate:18:"&hellip;"}</a>
                    </li>
                {/if}                      
            </ul>
            {include file='modules/module.resource-actions.tpl'}
        </div>
        {/if}
	{/foreach}
</div>

{/if}
