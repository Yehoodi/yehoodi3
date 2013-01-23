<div class="grid_4 text">
    <h2 class="iconText iconGraph">Site Statistics</h2>
    <ul class="ul_noSpace">
        <li>Number of Topics: {$topicCount}</li>
        <li>Number of Comments: {$commentCount}</li>
        <li>Number of Users (Total): {$userTotalCount}</li>
        <li>Number of Users (Active): {$userActiveCount}</li>
        <li>Database Size: {$dbSize} MB</li>
        <li>Topics Per Day: {$y3TopicsPerDay}</li>
        <li>Comments Per Day: {$y3CommentsPerDay}</li>
        <li>Avatar Directory Size: {$avatarDirSize}</li>
    </ul>
</div>
<div class="grid_10 text omega">
    <h2 class="iconText iconUsersLarge">Who is Online</h2>
    	{if $usersOnlineTotal > 0}
            {foreach from=$usersOnline item=user}
               <span class="iconText iconUser"><a href="{geturl controller = 'profile'}{$user.user_name}">{$user.user_name}</a></span>
            {/foreach}
            {if $userInvisibleCount}&nbsp;<em class="iconText iconUserHidden">({$userInvisibleCount}) anonymous user{if $userInvisibleCount > 1}s{/if}</em>{/if}
    	{else}
        	Unable to list online users.
        {/if}
</div>