{include file='header.tpl' title='Yehoodi.com: Users Admin' section='admin' maps=false}

<h1>Yehoodi.com: Admin</h1>

<div class="grid_2 alpha">{include file='admin/main.tabs.tpl'}</div>
<div class="grid_14 omega">
    {include file='admin/users.tabs.tpl'}
    {if $section == 'ban'}
    	{include file='admin/user.ban.tpl'}
    {elseif $section == 'disallow' }
    	{include file='admin/user.disallow.tpl'}
    {elseif $section == 'find_user' }
    	{include file='admin/user.find.tpl'}
    {elseif $section == 'manage_edit' }
    	{include file='admin/user.manage-edit.tpl'}
    {/if}
</div>
<div class="clear">&nbsp;</div>
{include file='footer.tpl'}