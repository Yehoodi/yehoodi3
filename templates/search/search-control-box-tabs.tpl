<div class="controlBoxTabs">
    <ul>
        <li class="{ if $type == 'resources' }current-page{ /if }">
            <a href="{geturl}?{if $q}q={$q|escape}&amp;{/if}type=resources{if $user}&amp;user=true{/if}">Topics {if $totalResources}({$totalResources}){/if}</a>
        </li>
        <li class="{ if $type == 'comments' }current-page{ /if }">
            <a href="{geturl}?{if $q}q={$q|escape}&amp;{/if}type=comments{if $user}&amp;user=true{/if}">Comments {if $totalComments}({$totalComments}){/if}</a>
        </li>
        <li class="{ if $type == 'events' }current-page{ /if }">
            <a href="{geturl}?{if $q}q={$q|escape}&amp;{/if}type=events{if $user}&amp;user=true{/if}">Events {if $totalEvents}({$totalEvents}){/if}</a>
        </li>
        <li class="{ if $type == 'users' }current-page{ /if }">
            <a href="{geturl}?{if $q}q={$q|escape}&amp;{/if}type=users{if $user}&amp;user=true{/if}">Users {if $totalUsers}({$totalUsers}){/if}</a>
        </li>
    </ul>
</div>