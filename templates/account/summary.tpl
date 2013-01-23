{include file='header.tpl' section='account' maps=false}

<h1>Manage your Yehoodi.com account</h1>

<div class="grid_3 alpha">{include file='account/tabs.tpl'}</div>	
<div id="div_accountDetails" class="grid_13 omega text">
    <h2>Your account: {$user->user_name|escape}</h2>
    <p id="p_joinedDate">Joined Yehoodi <strong>{$user->getDateFirstVisit()|escape}</strong>.<br />Last login <strong>{$user->getLastLogin()|escape}</strong>.</p>
    <div class="grid_4 alpha">
        <h3>Your Activity</h3>
        <ul>
            <li>Topics Submitted: <strong>{$userSubmitted}</strong>{if $userSubmitted > 0} [<a href="{geturl controller='search'}?q={$user->user_name|escape}&amp;type=resources&amp;user=true">See All</a>]{/if}</li>
            <li>Drafts saved: <strong>{$userDrafts}</strong>{if $userDrafts > 0} [<a href="{geturl controller = 'account' action = 'summary'}/drafts"/>See all</a>]{/if}</li>
            <li>Comments: <strong>{$userComments}</strong>{if $userComments > 0} [<a href="{geturl controller='search'}?q={$user->user_name|escape}&amp;type=comments&amp;user=true">See All</a>]{/if}</li>
            <!--<li>Events: <strong>{$userEvents}</strong>{if $userEvents > 0} [<a href="{geturl controller='search'}?q={$user->user_name|escape}&amp;type=events&amp;user=true">See All</a>]{/if}</li>-->
        </ul>
    </div>
    <div class="grid_5">
        <h3>Your Actions</h3>
        <ul>
            <li class="iconText iconStarLarge"> Bookmarks: <strong>{$userBookmarks}</strong>
            {if $userBookmarks > 0}
            [<a href="{geturl controller = 'discussion' action = 'bookmarks'}"/>View</a>]
            {/if}</li>
            <li class="iconText iconBellLarge">Email me topics: <strong>{$userWatches}</strong>
            {if $userWatches > 0}
            [<a href="{geturl controller = 'account' action = 'summary'}/watched"/>View</a>]
            {/if}</li>
            <li class="iconText iconThumbSmall">Votes: <strong>{$userVotes}</strong></li>
        </ul>
    </div>
    <div class="grid_4 omega">
        <h3>Relationships</h3>
        <ul>
            <li class="iconText iconUserIgnored">Ignored users: <strong>{$userIgnore}</strong>
            {if $userIgnore > 0}
            [<a href="{geturl controller = 'account' action = 'summary'}/ignored"/>View</a>]
            {/if}</li>
        </ul>
    </div>
</div>
<div class="clear">&nbsp;</div>
    
{if $actionType == 'bookmarks' || $actionType == 'watched' || $actionType == 'ignored' || $actionType == 'drafts' }
<div class="grid_16 div_lists">
    {if $actionType == 'bookmarks'}
        {include file='account/bookmarks-see-all.tpl'}
    {elseif $actionType == 'watched'}
        {include file='account/watched-see-all.tpl'}
    {elseif $actionType == 'ignored'}
        {include file='account/ignored-see-all.tpl'}
    {elseif $actionType == 'drafts'} 
        {include file='account/drafts-see-all.tpl'}
    {/if}
</div>
<div class="clear">&nbsp;</div>
{/if}

{include file='footer.tpl'}
