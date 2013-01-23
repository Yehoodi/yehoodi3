<div class="controlBox{if $action == "event"} controlBoxEvent{/if}">
    <h2>
    	{if $action == 'all'}All Topics{elseif $action == 'bookmarks'}{$identity->user_name}'s Bookmarks{else}{$action|capitalize|escape}{/if} 
		{if $categoryUrl != 'all'}&middot; {$categoryText|capitalize|escape}{/if}
	</h2>
    <span class="span_discussionFormat">
        Format: 
        <a class="a_sortLink {if $viewType == "collapsed"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/{$order}/?view=collapsed&amp;eFilter={$eFilter}">Collapsed</a>
   		<a class="a_sortLink {if $viewType == "normal"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/{$order}/?view=normal&amp;eFilter={$eFilter}">Normal</a>
    </span>  
    <span class="span_discussionFilter">
        {if $selectedRsrc != 'bookmarks'}
            View:
            {if $authenticated}
                <a class="a_sortLink {if $range == "lastvisit"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/lastvisit/{$order}/?view={$viewType}&amp;eFilter={$eFilter}" title="Between now and  your last visit {$identity->last_visit}.">Last Visit</a>
            {/if}
            <a class="a_sortLink {if $range == "7days"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/7days/{$order}/?view={$viewType}&amp;eFilter={$eFilter}" title="Between now and 7 days ago.">7 days</a>
            <a class="a_sortLink {if $range == "30days"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/30days/{$order}/?view={$viewType}&amp;eFilter={$eFilter}" title="Between now and 30 days ago.">30 days</a>
            <a class="a_sortLink {if $range == "90days"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/90days/{$order}/?view={$viewType}&amp;eFilter={$eFilter}" title="Between now and 90 days ago.">90 days</a>
            {if $authenticated}
                <a class="a_sortLink {if $range == "allTime"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/allTime/{$order}/?view={$viewType}&amp;eFilter={$eFilter}" title="Between now and back in the day.">All Time</a>
            {/if}
		{/if}
		{if $action == "event"}
        	<span id="span_eventLocation">
            {if $authenticated}                
                Event location: 
                <a class="a_sortLink {if $eFilter == "all"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/{$order}/?view={$viewType}&amp;eFilter=all">Anywhere</a>         
                <a class="a_sortLink {if $eFilter == "local"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/{$order}/?view={$viewType}&amp;eFilter=local">Local</a>
                {if $identity->location}                	
                	to <strong>{$identity->location|escape}</strong>. [<a href="{geturl controller='account' action='settings'}" class="a_homeLocation">Change</a>]
                { else }
                	<strong>You have not <a href="{geturl controller='account' action='settings'}" class="a_homeLocation">set a home location</a>.</strong>
                {/if}
			{else}
            	<strong><a href="{geturl controller='account' action='login'}{if $controller == 'calendar'}?redirect=/calendar{elseif $controller == 'discussion'}?redirect=/discussion{/if}" class="a_homeLocation">Log in</a> to set a home location for events.</strong>
            {/if}
            </span>
        {/if}
    </span>          
    <span class="span_discussionSort">
        Sort by:
        {if $selectedRsrc == 'bookmarks'}
            <a class="a_sortLink {if $order == "activity"}currentSelection{/if}" href="/discussion/bookmarks/all/all/activity/?view={$viewType}&amp;eFilter={$eFilter}" title="Sorted by the topic activity.">Activity</a>
            <a class="a_sortLink {if $order == "date"}currentSelection{/if}" href="/discussion/bookmarks/all/all/date/?view={$viewType}&amp;eFilter={$eFilter}" title="Sorted by the date the Topic was started.">Topic added</a>
            <a class="a_sortLink {if $order == "comment"}currentSelection{/if}" href="/discussion/bookmarks/all/all/comment/?view={$viewType}&amp;eFilter={$eFilter}" title="Topics with the most chatter."># Comments</a>
            <a class="a_sortLink {if $order == "views"}currentSelection{/if}" href="/discussion/bookmarks/all/all/views/?view={$viewType}&amp;eFilter={$eFilter}" title="Topics that users took a peek at."># Views</a>
            <a class="a_sortLink {if $order == "popular"}currentSelection{/if}" href="/discussion/bookmarks/all/all/popular/?view={$viewType}&amp;eFilter={$eFilter}" title="Topics with the most votes rise to the top!"># Votes</a>
        {else}
            <a class="a_sortLink {if $order == "activity"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/activity/?view={$viewType}&amp;eFilter={$eFilter}" title="Sorted by the topic activity.">Activity</a>
            <a class="a_sortLink {if $order == "date"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/date/?view={$viewType}&amp;eFilter={$eFilter}" title="Sorted by the date the Topic was started.">Topic added</a>
            <a class="a_sortLink {if $order == "comment"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/comment/?view={$viewType}&amp;eFilter={$eFilter}" title="Topics with the most chatter."># Comments</a>
            <a class="a_sortLink {if $order == "views"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/views/?view={$viewType}&amp;eFilter={$eFilter}" title="Topics that users took a peek at."># Views</a>
            <a class="a_sortLink {if $order == "popular"}currentSelection{/if}" href="/discussion/{$action}/{$categoryUrl}/{$range}/popular/?view={$viewType}&amp;eFilter={$eFilter}" title="Topics with the most votes rise to the top!"># Votes</a>
        {/if}
    </span>   
</div>