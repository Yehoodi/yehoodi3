{include file='header.tpl' title='Yehoodi.com: Site Admin' section='admin' maps=false}

<h1>Yehoodi.com: Admin</h1>

<div class="grid_2 alpha">{include file='admin/main.tabs.tpl'}</div>
<div class="grid_14 omega">
    {include file='admin/site.tabs.tpl'}
    {if $section == 'svn'}
    	{include file='admin/site.svn.tpl'}
    {/if}
</div>
<div class="clear">&nbsp;</div>
{include file='footer.tpl'}