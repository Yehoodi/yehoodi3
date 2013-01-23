{include file='discussion/discussion-control-box-tabs.tpl'}
{include file='discussion/discussion-control-box.tpl'}

{ if $resources|@count > 0 }

    <div class="div_pageControl grid_16">
        {include file='modules/module.pagination.tpl'}    	
        {include file='modules/module.add-link.tpl'} 
        <div class="clear">&nbsp;</div>
    </div> <!-- div_pageControl -->

    {if $viewType == 'collapsed'}
    	{include file='modules/module.results-resource-small.tpl'}
    { else }
    	{include file='modules/module.results-resource-large.tpl'}
    {/if}
    
    <div class="div_pageControl grid_16">
        {include file='modules/module.pagination.tpl'}    	
        {include file='modules/module.add-link.tpl'}
		<div class="clear">&nbsp;</div>
    </div> <!-- div_pageControl -->
	
    <div class="clear">&nbsp;</div>
	
{else}

    {* If we are here then we got NO resources back from the DB. We need to tell the user what happened *}
    <p>
    {if $action == 'event'}		
        We didn't find any topics based on the sort parameters. If you are searching for Local upcoming events, make
        sure to set your location in your <a href="{geturl controller='account' action='settings'}">account settings</a>.		
    {elseif $action == 'bookmarks'}
    	You don't have any topics bookmarked.
    {else}
    	We didn't find any topics based on the sort parameters. Change your sort options or pick a different tab.
    {/if}
    </p>    
    
{/if}