{include file='header.tpl' section='index' maps=true}

<h1 class="hidden">{$title|escape}</h1>

{include file='index/index-top.tpl'}

{assign var=randomNumber value=1|rand:2}

{if $randomNumber eq 2}
    {include file='modules/module.live-banner.tpl'}
{else}
    {include file='modules/module.static-banner.tpl'}
{/if}

{include file='index/index-middle.tpl'}
{include file='index/index-bottom.tpl'}

{include file='footer.tpl'}

