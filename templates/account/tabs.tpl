<ul id="ul_mainTabs">    
   <li class="{if $currentPage == 'summary'}current-page{/if}"><a href="/account/summary">Account Summary</a></li>
   <li class="{if $currentPage == 'details'}current-page{/if}"><a href="/account/details">Update Your Details</a></li>
   <li class="{if $currentPage == 'avatar'}current-page{/if}"><a href="/account/avatar">Avatar</a></li>				
   <li class="{if $currentPage == 'settings'}current-page{/if} final"><a href="/account/settings">Site Settings</a></li>
</ul>
<p class="iconText iconQuestionLarge"><a href="{geturl controller='profile'}{$user->user_name|escape}">See Public Profile</a></p>
