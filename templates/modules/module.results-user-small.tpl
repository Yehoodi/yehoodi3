<div class="results userSmall">
    {foreach from=$users item=user name=users}
    {cycle values=',alt' assign='class'}
    <div class="result {$class} {if $user->ignore}div_ignoredUser{/if}">    
        <div class="div_avatar">
            <a href="{geturl controller='profile'}{$rsrc->meta->postedBy}{$user->user_name}"><img src="{avatarfilename id=$user->userMeta->avatar->getId()}" alt="{$user->user_name}" class="avatar-tiny" /></a>
        </div>
        <div class="div_user">         
            <h3><a href="{geturl controller='profile'}{$rsrc->meta->postedBy}{$user->user_name}">{$user->user_name}</a></h3>
            <ul class="meta">
            {if $user->profile->location}
                <li>{$user->profile->location}</li>
            {/if}
            {if $user->profile->utilize_email}
                <li>{$user->email_address}</li>
            {/if}
                <li class="final iconText iconBalloonSmall">{$user->userMeta->postCount}<span class="hidden"> comment{if $user->userMeta->postCount !=1}s{/if}</span></li>
            </ul>            
        </div>
        {if $user->ignore}                      
        <div class="div_ignore">
            <p class="iconText iconUserIgnored">You are <strong>ignoring</strong> this user.<br />
            To remove the ignore flag, <a href="{geturl controller='profile'}{$rsrc->meta->postedBy}{$user->user_name}">visit their profile</a>.</p>
        </div>
        {/if}    
    </div>
    {/foreach}
</div>