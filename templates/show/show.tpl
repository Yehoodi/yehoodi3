{include file='header.tpl' section='show' maps=false}

{if $showCode == 'YRS'}
	{include file='show/show-content-radio.tpl'}
{else}     
     {if $showCode == 'YTV'}
         {include file='show/show-content-top-video.tpl'}
	 {elseif $showCode == 'SWN'}
		 {include file='show/show-content-top-swingnation.tpl'}
	 {elseif $showCode == 'ILHA'}
		 {include file='show/show-content-top-ilhc2012.tpl'}
	 {elseif $showCode == 'ILHB'}
		 {include file='show/show-content-top-ilhc2013.tpl'}
	 {elseif $showCode == 'ILHC'}
		 {include file='show/show-content-top-ilhc2014.tpl'}
     {else}
         {include file='show/show-content-top.tpl'}
     {/if}
	{include file='show/show-content-bottom.tpl'}
{/if}

{include file='footer.tpl'}

<script type="text/javascript" src="/js/show/ShowPage.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new ShowPage();
</script>
