{literal}
<script type="text/javascript">
	updateSortOrder = function(){
		// get the resource and category values
		var resource = $('select_resource_types').value;
		var category = $('select_category_types').value;
		
		// if the resource is an event - ajax in the extra sort options
		switch (category) {
			case "competitions":
			case "camps-workshops":
			case "exchange":
			case "recurring-dance-event":
			case "performance-special-event":
				new Ajax.Updater('span_listSortBy', '/browseajax/ajaxselectsortby/',
				{
					method: 'get',
					parameters: {select_resourceTypeId: 3}
				});
				break;
			
/*			case "all":
				new Ajax.Updater('span_listCategories', '/browseajax/ajaxselectcategory/',
				{
					method: 'get',
					parameters: {select_resourceTypeId: 0},
					evalScripts: true
				});
				break;
*/			
			default:
				new Ajax.Updater('span_listSortBy', '/browseajax/ajaxselectsortby/',
				{
					method: 'get',
					parameters: {select_resourceTypeId: 1}
				});
				break;
		}
	}
</script>
{/literal}
            <select name="category" id="select_category_types" onchange="updateSortOrder()">
	          	<option value="all">All categories</option>
				{foreach from=$categoryTypes item=category}
		          	<option value="{$category.cat_site_url}" {if $categoryUrl == $category.cat_site_url}selected{/if}>{$category.rsrc_type}: {$category.cat_type}</option>
				{/foreach}
			</select>