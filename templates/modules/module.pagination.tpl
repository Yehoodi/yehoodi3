<div class="div_pagination">
{if $totalResults}
	<p class="p_paginationControl">
    {if $totalResults > $pageResultNum}    	
    	
    	Page(s):
         <!-- Previous page link --> 
         {if $pageCurrent == 1}
            {* Do nothing? I don't like this... *}
         {else}
         	<a class="a_pagePrevious" href="{$pagePrevious}">&lt; Previous</a> 
         {/if}
    	
         <!-- Numbered page links -->
         {if !empty($pageCounterTop)}
              {foreach from=$pageCounterTop key=pageNum item=pageUrl name=resources}
                   {if $pageNum == $pageCurrent}
                   	<span class="span_pageCurrent">{$pageCurrent}</span>
                   {else}
                   	<a class="a_pageNum" href="{$pageUrl}">{$pageNum}</a>
                   {/if}
              {/foreach}
         {/if}
         
         {if $pageCounterTop|@count == 6}
         	{* Display no dots *}
         { elseif ($pageCounterTop|@count > 6)}
         	...
         { elseif ($pageCounterTop|@count == 2)}
         	...
         { elseif ($pageCounterTop|@count == 2) && ($pageCounterMiddle)}
         	...
         {/if}
         
         {if !empty($pageCounterMiddle)}
              {foreach from=$pageCounterMiddle key=pageNum item=pageUrl name=resources}
                   {if $pageNum == $pageCurrent}
                   	<span class="span_pageCurrent">{$pageCurrent}</span> 
                   {else}
                   	<a class="a_pageNum" href="{$pageUrl}">{$pageNum}</a>
                   {/if}
              {/foreach}
              ...
         {/if}
         
         {if !empty($pageCounterEnd)}
              {foreach from=$pageCounterEnd key=pageNum item=pageUrl name=resources}
                   {if $pageNum == $pageCurrent}
                   	<span class="span_pageCurrent">{$pageCurrent}</span>
                   {else}
                   	<a class="a_pageNum" href="{$pageUrl}">{$pageNum}</a>
                   {/if}
              {/foreach}
         {/if}
        
         <!-- Next page link -->      
         {if $pageCurrent == $pageLast}
         {else}
         	<a class="a_pageNext" href="{$pageNext}">Next &gt;</a>
         {/if}
    	<span class="span_pagesTotal">({$totalResults} item{if $totalResults > 1}s{/if} total{if $pageCurrent == 1}, {$pageResultNum} per page){else}){/if}</span>
    	</p> <!-- p_paginationControl -->
    {else}
    	<span class="span_pagesTotal">({$totalResults} item{if $totalResults > 1}s{/if} total{if $pageCurrent == 1}, {$pageResultNum} per page){else}){/if}</span>
    	</p> <!-- p_paginationControl -->
    {/if}
{else}
&nbsp;
{/if}
</div> <!-- div_pagination -->