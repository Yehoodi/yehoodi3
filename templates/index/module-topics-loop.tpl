<div class="results resourceTiny">
	{foreach from=$resources item=rsrc name=resources}
	{cycle values='result,result alt' assign='class'}
    <div class="{$class}">
        <a class="title" href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->title|truncate:40}</a>             
        <ul class="meta">
            <li class="iconText iconBalloonSmall">
                {if $rsrc->meta->numOfCommnets == 0}
                    {$rsrc->meta->numOfCommnets}<span class="hidden"> comments</span> ({$rsrc->meta->lastCommentRelativeDate})
                {else}
                    <a href="{geturl controller = 'comment'}{$rsrc->meta->lastCommentUrl}" title="Go to last comment"> {$rsrc->meta->numOfCommnets}<span class="hidden"> comments</span> ({$rsrc->meta->lastCommentRelativeDate})</a>
                {/if}                
            </li>            
            <li class="iconText iconThumbSmall">{$rsrc->meta->voteNum}<span class="hidden"> votes</span></li>
            <li class="iconText iconEyeSmall">{$rsrc->meta->viewsLifetime}<span class="hidden"> views</span></li>    
        </ul>
    </div>
    {/foreach}
</div>