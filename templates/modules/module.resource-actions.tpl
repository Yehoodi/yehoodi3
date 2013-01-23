{* This is a logged in user, then we show this stuff... *}
{ if $authenticated }
    <ul class="actions">
    {* ...and if the current user owns this resource, then show the Edit link *}
    { if $controller == "comment" && $identity->user_id == $rsrc->user_id}
    	<li>
    		<a href="{$rsrc->editLink}" class="resource-edit" id="edit{$rsrc->getId()}" style="background-position: 0 -48px" title="Edit"></a>
    	</li>
    { /if }
    
    { if $controller == "index" || $controller == "comment" || $controller == "discussion" || $controller == "search"}
        <li id="li_bookmark_{$rsrc->getId()}">
        {* Bookmarked? *}
        {if $rsrc->bookmark}
            <a href="javascript:void(0);" class="resource-bookmark" id="a_bookmark_{$rsrc->getId()}" style="background-position: 0 -24px;" title="Remove from Bookmarks"></a>
        { else}
            <a href="javascript:void(0);" class="resource-bookmark" id="a_bookmark_{$rsrc->getId()}" style="background-position: 0 0;" title="Add to Bookmarks"></a>
        {/if}
        </li>
    { /if }
    
    { if $controller == "index" || $controller == "comment" || $controller == "discussion" || $controller == "search" || $controller == "account" }
        <li id="li_watch_{$rsrc->getId()}">
        {* Watched? *}
        {if $rsrc->is_active != '2'}{* If the topic is still in DRAFT mode, you can't put a watch on it *}
            {if $rsrc->notify}
                <a href="javascript:void(0);" class="resource-notify" id="a_notify_{$rsrc->getId()}" style="background-position: -24px -24px;" title="Stop emailing me about this topic"></a>
            { else }
                <a href="javascript:void(0);" class="resource-notify" id="a_notify_{$rsrc->getId()}" style="background-position: -24px 0;" title="Email me when someone comments about this topic"></a>
            {/if}
        {/if}
        </li>
    { /if }
    
    { if $controller == "index" || $controller == "comment" || $controller == "discussion" || $controller == "search" }    
        <li id="li_vote_{$rsrc->getId()}">
        {* Voted? *}
        {if $rsrc->vote}
            <a href="javascript:void(0);" class="resource-vote" id="a_vote_{$rsrc->getId()}" style="background-position: -48px -24px;" title="Remove Vote"></a>
        { else}
            <a href="javascript:void(0);" class="resource-vote" id="a_vote_{$rsrc->getId()}" style="background-position: -48px 0;" title="Add Vote"></a>
        {/if}
        </li>
    { /if }
    	
    	
    <!-- Removed until we want the I am attending feature
    	{* Only show "Add to Calendar" for actual events and not the event talk category *}
    	{if $rsrc->meta->cat_id == '9' || $rsrc->meta->cat_id == '10' || $rsrc->meta->cat_id == '11' || $rsrc->meta->cat_id == '13' || $rsrc->meta->cat_id == '14'}
    	<li id="li_calendar_{$rsrc->getId()}">
    	{* Add to calendar? *}	
    		{if $rsrc->calendar}
    			<a href="javascript:void(0);" class="resource-calendar" id="a_calendar_{$rsrc->getId()}" style="background-position: -168px -24px;" title="On Calendar"></a>
    		{ else }
    			<a href="javascript:void(0);" class="resource-calendar" id="a_calendar_{$rsrc->getId()}" style="background-position: -168px 0;" title="Add to Calendar"></a>
    		{/if}	
    	</li>
    	{/if}
    -->	
    
    	<li id="li_delete_{$rsrc->getId()}">
    	{* Delete Draft? *}
    	{ if $controller == "account" && $rsrc->is_active == 2 }
    		<a href="{geturl controller='account' action='summary'}/drafts?rsrc_id={$rsrc->getId()}" class="resource-delete" id="a_delete_{$rsrc->getId()}" style="background-image: url('/images/buttons/cancel.png');" title="Delete Draft"></a>
    	{/if}
    	</li>
    	
    { if $controller != "index" && $controller != "account" }
    { * Only mods can post featured topics, and only featured topics will have these buttons on the landing page, so this button is not necessary on the home page *}
        <li>
        {if $rsrc->report}
            <a href="javascript:void(0);" class="resource-report" id="a_report_{$rsrc->getId()}" style="background-position: -72px -24px;" title="Topic Reported to Yehoodistrators!"></a>
        { else }
            <a href="javascript:void(0);" class="resource-report" id="a_report_{$rsrc->getId()}" style="background-position: -72px 0;" title="Report this to moderators"></a>
        {/if}
        </li>
    {/if}
    
    { if $identity->mod == "true" && $controller == "comment"}
        <li>
            <a href="{geturl controller='submit'}{$rsrc->getId()}" class="resource-mod" id="edit{$rsrc->getId()}" style="background-position: -24px -48px" title="Moderator Edit this Topic"></a>
        </li>
        <li>
        {if $rsrc->isClosed()}
            <a href="{geturl controller='moderatorajax'}?rsrc_id={$rsrc->getId()}" class="resource-mod-closed" id="a_closed_{$rsrc->getId()}" style="background-position: -96px -24px;" title="Open this Topic" name="Open"></a>
        {else}
            <a href="{geturl controller='moderatorajax'}?rsrc_id={$rsrc->getId()}" class="resource-mod-closed" id="a_closed_{$rsrc->getId()}" style="background-position: -96px 0;" title="Close this Topic" name="Close"></a>
        {/if}
        </li>
    { /if }
    
    </ul>
{ /if }