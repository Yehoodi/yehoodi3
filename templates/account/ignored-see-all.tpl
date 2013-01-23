<h2>
    Your Ignored Users
    <span class="small">[<a href="{geturl controller = 'account' action = 'summary'}/ignored" />Refresh List</a>]</span>
</h2>
{include file='modules/module.results-user-small.tpl' viewType = 'collapsed'}

<script type="text/javascript" src="/js/lib/ResourceActions.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new ResourceActions();
</script>
