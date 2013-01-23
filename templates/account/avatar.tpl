{include file='header.tpl' section='account' maps=false}

<h1>Manage your Yehoodi.com account</h1>

<div class="grid_3 alpha">{include file='account/tabs.tpl'}</div>	
<div id="div_accountDetails" class="grid_13 omega text">  
    <h2>Update Your Avatar</h2> 
    <form method="POST" action="{geturl action='avatar'}" enctype="multipart/form-data" id="form_avatarDelete">
        
        {if $fp->hasError()}
        <div class="error">
        An error has occurred in the form below. Please check
        the highlighted fields and re-submit the form.
        </div>
        {/if}
        
        <p>
            Make your identity unique! Upload an avatar image for all
            on Yehoodi to see. File formats can be .jpg, .gif or .png.
        </p>
        <p>                    
            Maximum size of your avatar is an <strong>80 pixel</strong> width and an <strong>80 pixel</strong>
            height. 
        </p>
        
        <div class="fieldset">
            <div class="row">
             <label class="grid_2">Avatar:</label>
             <input type="hidden" name="id" value="{$fp->user->getId()}" />
             {if $fp->avatar->getId()}
             	<img src="{avatarfilename id=$fp->avatar->getId()}" alt="" id="" />
            </div>
            <div class="row">
             	<label class="grid_2">&nbsp;</label>
            <input type="submit" class="button_cancel" id="button_avatarDelete" value="Delete Avatar" name="formAction">
             	<input type="hidden" name="avatar" value="{$fp->avatar->getId()}" />
             { else }
             	<input type="file" name="avatar" id="input-avatar" />
             	<label class="grid_2">&nbsp;</label>
            <input type="submit" class="button_submit" id="button-avatarUpload" value="Upload Avatar" name="formAction" class="" />
             {/if}				
             {include file='lib/error.tpl' error=$fp->getError('avatar')}
            </div>
        </div>
    </form>
</div>	
<div class="clear">&nbsp;</div>

     <script type="text/javascript" src="/js/account/AccountAvatarForm.class.{$jsExt}{$version}"></script>
	 <script type="text/javascript">
		new AccountAvatarForm('form_avatarDelete');
	 </script>

{include file='footer.tpl'}