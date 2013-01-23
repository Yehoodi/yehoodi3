{* This is the user's meta information including avatar, post count, etc... *}

<div class="div_userAvatar">
    <img class="avatar-large" src="{avatarfilename id=$userMeta->avatar->getId()}" alt="{$mailMeta->postedBy}" />
    <strong {if $userMeta->online}class="iconTextAlt iconStatusGreen"{/if}>
    {if $controller == 'mail'}
        <a href="{geturl controller = 'profile'}{$author}">{$author}</a>
    {else}
        <a href="{geturl controller = 'profile'}{$mailMeta->postedBy}">{$mailMeta->postedBy}</a>
    {/if}
    </strong>
</div>

<div class="div_userMeta">
    <ul class="meta">
        {if $authenticated && $controller != 'mail'}            
            {if $userMeta->role}<li>{$userMeta->role}er</li>{/if}
            {if $userMeta->hometown}<li>{$userMeta->hometown|truncate:32:"&hellip;"}</li>{/if}
            <li class="iconText iconBalloonSmall">{$userMeta->postCount}<span class="hidden"> comment{if $userMeta->postCount !=1}s{/if}</span></li>
            <li class="final iconText iconMailSmall"><a href="/mail/message?recipient={$mailMeta->postedBy}" title="Send mail to {$mailMeta->postedBy}">Mail</a></li>
        {elseif $authenticated && $controller == 'mail'}
            <li>Joined {$userMeta->joinedDate}</li>
            <li class="iconText iconBalloonSmall final">{$userMeta->postCount} comment{if $userMeta->postCount !=1}s{/if}</li>
        {else}
            <li>Joined {$userMeta->joinedDate}</li>
            <li class="final iconText iconBalloonSmall">{$userMeta->postCount}<span class="hidden"> comment{if $userMeta->postCount !=1}s{/if}</span></li>
        {/if}    
    </ul>
</div>
