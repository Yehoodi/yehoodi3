{literal}
<script type="text/javascript">
	updatePreviewCat = function(){
			$('liCategory').innerHTML = $('select_categoryTypeId').options[$('select_categoryTypeId').selectedIndex].text;
		};

		$('liCategory').innerHTML = $('select_categoryTypeId').options[0].text;
</script>
{/literal}
			<label>Category:</label>
			<select name="cat_id" id="select_categoryTypeId" onchange="updatePreviewCat()">
			
			{* This if sets the selected value of the list box to either the previous value or to the FIRST value in the list
			   I'm doing this because I need to explicitly set the selected value of the list for later *}
			   
			{if $fp->cat_id}
				{assign var="default" value = $fp->cat_id}
			{else}
				{assign var ="default" value = $categoryTypes.0.cat_id}
			{/if}

			{foreach from=$categoryTypes item=category}
			
				<option value="{$category.cat_id}" 
					{if $default == $category.cat_id}
						selected
					{/if}
				>{$category.cat_type|capitalize}</option>
			{/foreach}
			</select>