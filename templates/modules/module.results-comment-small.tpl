<div class="results commentSmall">
    {foreach from=$comments item=com name=comments}
	{if $com->meta->resourceName == 'admin' && !$identity->mod}
	   {* Skip display if it's in the admin section and the user isn't a moderator *}
	{else}
    {cycle values=',alt' assign='class'}
    <div class="result {$class}">
        <div class="div_avatar">
            <img src="{avatarfilename id=$com->userMeta->avatar->getId()}" alt="{$com->meta->postedBy}" class="avatar-tiny" />
        </div>
        <div class="div_comment">
            <strong class="iconText iconUserSmall"><a href="{geturl controller = 'profile'}{$com->meta->postedBy}">{$com->meta->postedBy}</a></strong> said
            on <a href="{geturl controller='comment'}{$com->meta->resourceUrl}{$com->meta->commentPageNum}#comment_{$com->comment_num}">page {$com->meta->commentPageNum}</a>
            of <strong><a href="{geturl controller='comment'}{$com->meta->resourceUrl}">{$com->meta->resourceTitle|truncate:50:"&hellip;"}</a></strong>
            {if $q == ''}
                {$com->comment|strip|truncate:255:"&hellip;":false}
            {else}
                <p>{highlighttext|truncate:255:"&hellip;" text=$com->comment words=$words}</p>
            {/if}
        </div>
        <div class="meta">
            {$com->meta->relativeDate}
        </div>
    </div>
    {/if}
    {/foreach}
</div>
