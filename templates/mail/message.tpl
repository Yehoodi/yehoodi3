{include file='header.tpl' section='mail' maps=false}

<form action="{geturl controller='mail' action='message'}/{$threadId}/{$pageNumber}?a=222" method="post" id="form_message" name="formMessage">
    {include file='mail/message-thread.tpl'}
</form>

{include file='footer.tpl'}

<script type="text/javascript" src="/js/mail/MailMessage.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new MailMessage('form_message');
</script>

<script type="text/javascript" src="/js/lib/wmd.{$jsExt}{$version}"></script>

{if !$smart_device}
    <script type="text/javascript" src="/js/lib/showdown.{$jsExt}{$version}"></script>
{/if}