{include file='header.tpl' section='submit' maps=true}

<h1 class="hidden">Submit a new topic to Yehoodi.com</h1>

<div class="grid_12">	
	{include file='submit/submit.tpl'}
</div>
<div class="grid_4 text">
	{include file='help/help.howto-submit.tpl'}
</div>
<div class="clear">&nbsp;</div>

<!-- preview section -->
{* Disable preview window for iPhone/iPad *}
{if !$smart_device}
    <div id="div_preview">
    	<h2 id="previewSectionTitle">Preview your topic</h2>
    	{include file='submit/preview.tpl'}
        <div class="clear">&nbsp;</div>
    </div>
{/if}
<!-- end preview section-->

{include file='footer.tpl'}
<script type="text/javascript" src="/js/google/LocationManager.{$jsExt}{$version}"> </script>

<!--<script type="text/javascript" src="/js/lib/BoxOver.{$jsExt}{$version}"></script>-->
<script type="text/javascript" src="/js/lib/utils.{$jsExt}{$version}"></script>
<script type="text/javascript" src="/js/submit/ResourceSubmitForm.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new ResourceSubmitForm('form_submit');
</script>

<script type="text/javascript">
	new LocationManager('div_containerGoogleMap','button_addLocation');
</script>

<script type="text/javascript" src="/js/lib/wmd.{$jsExt}{$version}"></script>

{if !$smart_device}
    <script type="text/javascript" src="/js/lib/showdown.{$jsExt}{$version}"></script>
{/if}