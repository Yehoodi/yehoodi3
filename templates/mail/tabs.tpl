<ul id="ul_mainTabs">    
   <li class="{if $currentTab == 'inbox'}current-page{/if}"><a href="{geturl controller='mail'}inbox/" class="">Inbox (<span id="a_inboxCounter">{$mailUnreadCount}</span> unread)</a></li>
   <li class="{if $currentTab == 'sent'}current-page{/if} final"><a href="{geturl controller='mail'}sent/" class="">Sent Messages</a></li>
</ul>
<p>{$mailTotalCount} total {if $mailTotalCount == 1}message{else}messages{/if}</p>
