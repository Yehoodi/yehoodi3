{include file='header.tpl' section='account' maps=false}

<h1 class="grid_16">Manage your Yehoodi.com account</h1>

{include file='account/tabs.tpl'}
	
     <div id="div_accountDetails" class="grid_16">
		<form>
          	<p>Thank you {$user->profile->first_name|escape}, your details have been updated.</p>
		</form>
	</div>
	
     <!--div class="grid_4">Sidebar</div-->
     <div class="clear">&nbsp;</div>
{include file='footer.tpl'}