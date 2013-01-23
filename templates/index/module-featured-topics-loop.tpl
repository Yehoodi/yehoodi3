	{foreach from=$resources item=rsrc name=resources}
    {cycle values='result alt,result' assign='class'}
	{if $rsrc->meta->resourceName == 'admin' && !$identity->mod}
	   {* Skip display if it's in the admin section and the user isn't a moderator *}
	{else}
        <div class="{$class}">
    		<h3><a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->title}</a></h3>
            
            <ul class="meta">
                <li>Posted <strong>{$rsrc->meta->relativeDate}</strong></li> 
                <li><a class="iconText iconUserSmall" href="{geturl controller = 'profile'}{$rsrc->meta->postedBy}">{$rsrc->meta->postedBy}</a></li>
                <li class="final">
    				<a class="tags" href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/all/">{$rsrc->meta->resourceName|capitalize}</a> &gt;
              		<a class="tags" href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/{$rsrc->meta->categoryUrl}/">{$rsrc->meta->categoryName|capitalize}</a>
    			</li>
            </ul>
            
            <!-- Event information -->
            {if $rsrc->meta->resourceName == 'event'}
            <h4>            
                {if $rsrc->calendarLink}
                    <a class="iconText iconCalendarSearchLarge" title="Find event on calendar" href="{geturl controller='calendar'}{$rsrc->calendarLink}">{$rsrc->neatStartDate}{if !empty($rsrc->neatEndDate)}&ndash;{$rsrc->neatEndDate}{/if}</a>
                {else}
                    {$rsrc->neatStartDate}{if !empty($rsrc->neatEndDate)}&ndash;{$rsrc->neatEndDate}{/if}
                {/if}            
                {if !empty($rsrc->locationsArray.0)}
                    | <span>{$rsrc->locationsArray.0.description}</span>
                {/if}
            </h4>
            {/if}
            <!-- End event information -->
            
    		<p class="excerpt">
    			{ if $rsrc->image->getId() > 0 }
    				<a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/"><img class="img_thumbnail" src="{resourcethumbnail id=$rsrc->image->getId() w=100 h=100}" alt="{$rsrc->title}" id="img{$rsrc->meta->_id}" /></a>
    			{/if}
    			{if !empty($search)}
    				{highlighttext|truncate:255:"&hellip;" text=$rsrc->descrip words=$words}
    			{else}
    				{$rsrc->descrip|markdown|resourcefilter|strip|truncateclosetags:255}
    			{/if}
    		</p>
            
    		<ul class="meta topicFooter">
                {if $rsrc->meta->numOfCommnets == 0}
                    <li class="iconText {if $rsrc->meta->newComments}iconRedBalloonSmall{else}iconBalloonSmall{/if}">{$rsrc->meta->numOfCommnets}<span class="hidden"> comments</span></li>
                {else}
                    <li class="iconText {if $rsrc->meta->newComments}iconRedBalloonSmall{else}iconBalloonSmall{/if}"><a href="{geturl controller = 'comment'}{$rsrc->meta->lastCommentUrl}" title="Go to last comment">{$rsrc->meta->numOfCommnets}<span class="hidden"> comment{if $rsrc->meta->numOfCommnets != 1}s{/if}</span></a></li>
                {/if}                
                <li class="iconText iconEyeSmall">{$rsrc->meta->viewsLifetime}<span class="hidden"> view{if $rsrc->meta->viewsLifetime != 1}s{/if}</span></li>
                <li class="iconText iconThumbSmall">{$rsrc->meta->voteNum}<span class="hidden"> vote{if $rsrc->meta->voteNum != 1}s{/if}</span></li>                
    		</ul>
        </div>
    {/if}
    {/foreach}