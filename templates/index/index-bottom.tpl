<div class="grid_6 div_stats alpha">
    <h3 class="iconText iconUsersLarge">Users Online</h3>
    {if $usersOnlineTotal > 0}
        <ul class="users">
        {foreach from=$usersOnline item=user}
            {cycle values=',alt' assign='class'}
            <li class="iconText iconUser {$class}"><a href="{geturl controller = 'profile'}{$user.user_name}">{$user.user_name}</a></li>
        {/foreach}
        {if $userInvisibleCount}<li class="iconText iconUserHidden">({$userInvisibleCount}) anonymous user{if $userInvisibleCount > 1}s{/if}</li>{/if}
        </ul>
    {else}
        <p>Unable to list online users.</p>
    {/if}
</div>
<div class="grid_5 div_stats">
    <h3 class="iconText iconCake">Happy Birthday!</h3>
    {if $birthdays|@count > 0}
        <ul class="users">
        {foreach from=$birthdays item=user}
            {cycle values=',alt' assign='class'}
            <li class="iconText iconUser {$class}"><a href="{geturl controller = 'profile'}{$user.user_name}">{$user.user_name}</a></li>
        {/foreach}
        </ul>
    {else}
        <p>Unable to list birthdays.</p>
    {/if}
</div>
<div class="grid_5 div_stats omega">
    <h3 class="iconText iconGraph">Yehoodi Stats</h3>
    <ul class="stats">
        <li><strong>Topics:</strong> {$topicTotal}</li>
        <li class="alt"><strong>Comments:</strong> {$commentTotal}</li>
        <li><strong>Users:</strong> {$userTotal}</li>
        <li class="alt">Newest user: <ul><li><a class="iconText iconUser" href="{geturl controller = 'profile'}{$newestUser.0.user_name}">{$newestUser.0.user_name}</a></li></ul></li>
    </ul>
</div>
<div class="clear">&nbsp;</div>