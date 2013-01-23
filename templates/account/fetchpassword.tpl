{include file='header.tpl' section='account' maps=false}

<h1 class="grid_16">Retrieve your Yehoodi password</h1>

<div class="grid_8">
{if $action == 'confirm'}
    {if $errors|@count == 0}
        <p>
            Your new password has now been activated.
        </p>

        <ul>
            <li><a href="{geturl action='login'}">Log in to your account</a></li>
        </ul>
    {else}
        <p>
            Your new password was not confirmed. Please double-check the link
            sent to you by e-mail, or try using the
            <a href="{geturl action='fetchpassword'}">Fetch Password</a> tool again.
        </p>
    {/if}
{elseif $action == 'complete'}
    <p>
        A password has been sent to your account e-mail address containing
        your new password. You must click the link in this e-mail to activate
        the new password.
    </p>
{else}
    <form method="post" action="{geturl action='fetchpassword'}" id="form_fetchPassword" class="grid_7">
        <fieldset>
            <div class="row" id="form_user_name_container">
                <label for="form_user_name">Username:</label>
                <input type="text" id="form_user_name" name="user_name" />
                {include file='lib/error.tpl' error=$errors.user_name}
            </div>
            <div class="row" id="">
	   		<label>&nbsp;</label>
                <input type="submit" value="Fetch Password" id="button_fetchPassword" class="button_submit" />
            </div>
        </fieldset>
    </form>
{/if}
</div>
<div class="grid_8 text">
	{include file='help/help.forgot-password.tpl'}
</div>
<div class="clear">&nbsp;</div>

{include file='footer.tpl'}