<div class="controlBoxTabs">
    <ul>
        <li class="{if $section == 'find_user' }current-page{/if}"><a href="{geturl controller='admin' action='users'}?section=find_user">Management</a></li>
        <li class="{if $section == 'ban' }current-page{/if}"><a href="{geturl controller='admin' action='users'}?section=ban">Ban Control</a></li>
        <li class="{if $section == 'disallow' }current-page{/if}"><a href="{geturl controller='admin' action='users'}?section=disallow">Disallow Names</a></li>
    </ul>
</div>