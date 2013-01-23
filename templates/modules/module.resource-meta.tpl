{* This file should be deleted - A.B *}

<ul class="meta">
	<li id="type{$rsrc->getId()}">
          <a class="tags" href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/all/">{$rsrc->meta->resourceName|capitalize}</a> &gt;
          <a class="tags" href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/{$rsrc->meta->categoryUrl}/">{$rsrc->meta->categoryName|capitalize}</a>
     </li>
	
{ if $controller == 'account' }

	<li>
          {$rsrc->meta->shortDate}
          { if $viewType == 'normal' }
          	by <span class="iconText iconUserSmall"><span class="icon">&nbsp;</span> <a href="{geturl controller = 'profile'}{$rsrc->meta->postedBy}">{$rsrc->meta->postedBy}</a></span>
          {/if}
     </li>   
     <li id="comments{$rsrc->getId()}" class="comments iconText iconBalloonSmall"><span class="icon">&nbsp;</span>
     {if $rsrc->meta->numOfCommnets == 1}
     	<a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->meta->numOfCommnets} comment</a>
     { else }  
     	<a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->meta->numOfCommnets} comments</a>
     { /if }     	     
     {if $rsrc->meta->numOfCommnets != 0}                    	
          {if $rsrc->meta->lastCommentUserName}
               ... <a href="{geturl controller = 'comment'}{$rsrc->meta->lastCommentUrl}">Last</a> by
               <a href="{geturl controller = 'profile'}{$rsrc->meta->lastCommentUserName}">{$rsrc->meta->lastCommentUserName}</a>
          {/if}
     {/if}
     </li>
	{if $action == 'event'}
     	<li>{$rsrc->locationsArray.0.description}</li>
    {/if}

{ elseif $controller == 'comment' }  
                  
    {* This shows if the post was edited*}
    {if $rsrc->date_edited > '0000-00-00 00:00:00' && $rsrc->isLive()}
        <li><strong>Edited {$rsrc->getEditDate()}<strong></li>
    {else}
        <li>Posted <strong>{$rsrc->meta->neatPostedDate}</strong></li>
    {/if}          
	
{ elseif $controller == 'search' }

	<li>&nbsp;Posted {$rsrc->meta->neatPostedDate} by <span class="iconText iconUserSmall"><span class="icon">&nbsp;</span> <a href="{geturl controller = 'profile'}{$rsrc->meta->postedBy}">{$rsrc->meta->postedBy}</a></span></li>
	{if $rsrc->meta->numOfCommnets != 0}
	<li id="comments{$rsrc->getId()}" class="comments iconText iconBalloonSmall"><span class="icon">&nbsp;</span>
		{if $rsrc->meta->numOfCommnets == 1}
          	<a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->meta->numOfCommnets} comment</a>
          { else }  
          	<a href="{geturl controller='comment'}{$rsrc->getId()}/{$rsrc->resourceSeoUrlString}/">{$rsrc->meta->numOfCommnets} comments</a>
          { /if }
	</li>
	{/if}

{/if}
	
	<li id="views{$rsrc->getId()}" class="final"><span class="highlight iconText iconEyeSmall"><span class="icon">&nbsp;</span>{$rsrc->meta->viewsLifetime}</span></li>
</ul>