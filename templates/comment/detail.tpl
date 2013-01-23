{foreach from=$resources item=rsrc name=resources}	
       
    <div class="div_detailsContent" id="details{$rsrc->getId()}">
        <ul class="meta">
            <li id="type{$rsrc->getId()}">
                <a class="tags" href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/all/">{$rsrc->meta->resourceName|capitalize}</a> &gt;
                <a class="tags" href="{geturl controller='discussion'}{$rsrc->meta->resourceName}/{$rsrc->meta->categoryUrl}/">{$rsrc->meta->categoryName|capitalize}</a>
            </li>
            {* This shows if the post was edited*}
            {if $rsrc->date_edited > '0000-00-00 00:00:00' && $rsrc->isLive()}
                <li><strong>Edited {$rsrc->getEditDate()}<strong></li>
            {else}
                <li>Posted <strong>{$rsrc->meta->neatPostedDate}</strong></li>
            {/if}   
            <li><span class="highlight iconText iconBalloonSmall">{$rsrc->meta->numOfCommnets}</span><span class="hidden"> comments</span></li>
            <li><span class="highlight iconText iconThumbSmall">{$rsrc->meta->voteNum}</span><span class="hidden"> votes</span></li>
            <li><span class="highlight iconText iconEyeSmall">{$rsrc->meta->viewsLifetime}</span><span class="hidden"> views</span></li>
        </ul>
        
        {if $rsrc->meta->resourceName == 'event'} 
        	<h2 id="h2_date">
                {* If this variable exists, the user is coming from the Calendar page *}
                {if !$calendarLink}
                    {if $rsrc->calendarLink }
                        <a title="Find event on calendar" href="{geturl controller='calendar'}{$rsrc->calendarLink}"><img src="/images/buttons/calendar-search-result.png" alt="Find event on the calendar" /></a>
                    {/if }
                {/if}
                <a href="{geturl controller='calendar'}{$rsrc->calendarLink}">{$rsrc->neatStartDate}{if !empty($rsrc->neatEndDate)}&ndash;{$rsrc->neatEndDate}{/if}</a>
            </h2>
        {/if} 
          
        {if $rsrc->url}
            <h3><a href="{$rsrc->url}" target="_blank">{$rsrc->url}</a></h3>
        {elseif $rsrc->extended->internal_page_url}
            <h3><a href="{$rsrc->extended->internal_page_url}">{$rsrc->extended->internal_page_link_text}</a></h3>
        {/if}
        
        {if $rsrc->image->getId()}
            <div class="div_resourceImage">
            	<a href="{imagefilename id=$rsrc->image->getId()}" rel="lightbox" title="{$rsrc->image->getCaption()}"><img src="{imagefilename id=$rsrc->image->getId() w=200}" alt="{$rsrc->title}" id="img{$rsrc->meta->_id}" /></a>
                <p id="p_resourceImageCaption">{$rsrc->image->getCaption()}</p>
            </div>
        { /if}
        
        <p>{$rsrc->descrip|markdown}</p>
        
        {* This shows the user's signature *}
        {if $rsrc->userMeta->signature}
        	<span class="span_signature"><p>{$rsrc->userMeta->signature}</p></span>
        {/if}        
     </div> <!-- .div_detailsContent -->
{/foreach}