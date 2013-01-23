<ul id="ul_mainTabs">    
   <li class="{if $currentPage == 'home'}current-page{/if}"><a href="/admin/">Admin Home{if $currentPage == 'home'} &raquo;{/if}</a></li>
   <li class="{if $currentPage == 'user'}current-page{/if}"><a href="/admin/users">Users{if $currentPage == 'user'} &raquo;{/if}</a></li>
   <li class="{if $currentPage == 'site'}current-page{/if}"><a href="/admin/site">Site{if $currentPage == 'site'} &raquo;{/if}</a></li>
   <li class="final {if $currentPage == 'topics'}current-page{/if}"><a href="/admin/topics">Topics{if $currentPage == 'topics'} &raquo;{/if}</a></li>
</ul>
