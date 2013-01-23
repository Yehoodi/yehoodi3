<h2>
Topics sending you Email Notifications
{if $userWatches > 0}
    <span class="small">[<a href="{geturl controller = 'account' action = 'summary'}/watched" />Refresh List</a>]</span>
{/if}
</h2>
{if $userWatches > 0}
    {include file='modules/module.results-resource-small.tpl' viewType = 'collapsed'}
{ else }
    You are not watching any topics.
{/if}


<script type="text/javascript" src="/js/lib/ResourceActions.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new ResourceActions();
</script>
