{include file='header.tpl' section='mail' maps=false}
<h1>{$title|escape}</h1>

<div class="grid_3 alpha">{include file='mail/tabs.tpl'}</div>	
<div id="div_accountDetails" class="grid_13 omega">
    <form id="form_mail" method="" action="">
    {include file='mail/mail-control-box.tpl'}
    {include file='mail/mail.tpl'}
    </form>
</div>
<div class="clear">&nbsp;</div>

{include file='footer.tpl'}
<script type="text/javascript" src="/js/mail/Mail.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new Mail('form_mail');
</script>
