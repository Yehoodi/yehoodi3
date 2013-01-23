{include file='search/search-control-box-tabs.tpl'}
{include file='search/search-control-box.tpl'}

{if $search.total == 0}
    {if $q != ''}<p>No results were found for this search.</p>{/if}
{else}
    <div class="div_pageControl" class="grid_16">
        {include file='modules/module.pagination.tpl'}
        <div class="clear">&nbsp;</div>
    </div> <!-- div_pageControl -->
    
    { if $type == 'resources' }               
        {include file='modules/module.results-resource-large.tpl'}  
    { elseif $type == 'comments' }
        {include file='modules/module.results-comment-small.tpl'}
    { elseif $type == 'events' }
        {include file='modules/module.results-resource-large.tpl'}  
    { elseif $type == 'users' }
        {include file='modules/module.results-user-small.tpl'}               
    { /if } 
    
    <div class="div_pageControl" class="grid_16">
        {include file='modules/module.pagination.tpl'}
        <div class="clear">&nbsp;</div>
    </div> <!-- div_pageControl -->     
{/if}
