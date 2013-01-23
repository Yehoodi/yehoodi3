{if $controller == 'calendar'}

    <div class="div_calendarSubmit">
        <a class="buttonLink iconText iconCalendarPlusLarge" href="{geturl controller='submit'}?cid={if $catId}{$catId}{else}9{/if}">Add New Event</a>
    </div>

{elseif $controller == 'comment'}

    <div class="div_addLink div_commentReply">
        {* Funky foreach here because we are always allowing for a collection of resources and I need to access this method *}
        {foreach from=$resources item=rsrc name=resources}
            {* This is checking for a logged in user AND showing the button if the topic is active (not a draft) *}
            {if $rsrc->isClosed()}
                <span class="inactiveButtonLink iconText iconLockSmall">Topic is Closed</span>
            { elseif $identity && $rsrc->isLive()}
                <a class="comment_reply buttonLink iconText iconBalloonPlusLarge" id="r_{$id}" href="#div_replyForm">Add Your Comment</a>
            { elseif $rsrc->isLive() }
                <a class="comment_login buttonLink iconText iconDoorClosed" href="{geturl controller='account' action='login'}?redirect={geturl controller='comment'}{$id}/{$rsrcUrl}/{$pageCurrent}">Login to Comment</a>
            { /if }
        {/foreach}
    </div> <!-- div_addLink -->
    
{elseif $controller == 'discussion'}

    <div class="div_addLink div_browseSubmit">
        {* No submit link allowed for the Featured or Bookmarks sections *}
        {if $action != 'featured' && $action !='bookmarks'}
            {if $action == 'event'}                
                <a class="buttonLink iconText iconCalendarPlusLarge" href="{geturl controller='submit'}{if $categoryId}?cid={$categoryId}{/if}">Add New Event</a>
            {else}
                <a class="buttonLink iconText iconDocumentPlusLarge" href="{geturl controller='submit'}{if $categoryId}?cid={$categoryId}{/if}">Add New Topic</a>
            {/if}              	
        {/if}        
    </div> <!-- div_addLink -->
    
{elseif $controller == 'mail' }

    <div class="div_addLink">
         <a class="buttonLink iconText iconMailSmall" href="{geturl controller='mail' action='message'}">New Message</a>
    </div> <!-- div_addLink -->

{/if}