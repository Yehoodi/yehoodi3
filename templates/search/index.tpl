{include file='header.tpl' section='search' maps=false}
<h1 class="hidden">{$title|escape}</h1>
{include file='search/search.tpl'}
{include file='footer.tpl'}

<script type="text/javascript" src="/js/lib/ResourceActions.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new ResourceActions();
</script>
