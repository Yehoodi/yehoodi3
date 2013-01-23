{literal}
	<script type="text/javascript">
		sfHover = function() {
			var sfEls = document.getElementById("ul_discussionTabs").getElementsByTagName("LI");
			for (var i=0; i<sfEls.length; i++) {
				sfEls[i].onmouseover=function() {
					this.className+=" sfhover";
				}
				sfEls[i].onmouseout=function() {
					this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
				}
			}
		}
		if (window.attachEvent) window.attachEvent("onload", sfHover);
	</script>
{/literal}

<div class="controlBoxTabs">
    <ul>
        <li class="{if $selectedRsrc == ''}current-page{/if}">
          	<a href="/discussion/all/all/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">All</a>
          </li>
          <li class="{if $selectedRsrc == '1'}current-page{/if}">
               <a class="iconTextAlt iconBrowseChevron"href="/discussion/featured/all/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">Featured</a>
               <ul>
               {foreach from=$categoryTypes item=cat}
                {if $cat.rsrc_type == "featured"}
                 <li><a href="{geturl controller='discussion'}{$cat.rsrc_type}/{$cat.cat_site_url}/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">{$cat.cat_type|capitalize|escape}</a></li>
                {/if}
               {/foreach}
               </ul>
          </li>
		<li class="{if $selectedRsrc == '2'}current-page{/if}">
               <a class="iconTextAlt iconBrowseChevron"href="/discussion/lindy/all/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">Lindy</a>
               <ul>
               {foreach from=$categoryTypes item=cat}
                {if $cat.rsrc_type == "lindy"}
                 <li><a href="{geturl controller='discussion'}{$cat.rsrc_type}/{$cat.cat_site_url}/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">{$cat.cat_type|capitalize|escape}</a></li>
                {/if}
               {/foreach}
               </ul>
          </li>
		<li class="{if $selectedRsrc == '5'}current-page{/if}">
               <a class="iconTextAlt iconBrowseChevron"href="/discussion/biz/all/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">Biz</a>
               <ul>
               {foreach from=$categoryTypes item=cat}
                {if $cat.rsrc_type == "biz"}
                 <li><a href="{geturl controller='discussion'}{$cat.rsrc_type}/{$cat.cat_site_url}/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">{$cat.cat_type|capitalize|escape}</a></li>
                {/if}
               {/foreach}
               </ul>
          </li>
		<li class="{if $selectedRsrc == '4'}current-page{/if}">
               <a class="iconTextAlt iconBrowseChevron"href="/discussion/lounge/all/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">The Lounge</a>
               <ul>
               {foreach from=$categoryTypes item=cat}
                {if $cat.rsrc_type == "lounge"}
                 <li><a href="{geturl controller='discussion'}{$cat.rsrc_type}/{$cat.cat_site_url}/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">{$cat.cat_type|capitalize|escape}</a></li>
                {/if}
               {/foreach}
               </ul>
          </li>
		<li class="{if $selectedRsrc == '3'}current-page{/if}">
               <a class="iconTextAlt iconBrowseChevron"href="/discussion/event/all/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">Events</a>
               <ul>
               {foreach from=$categoryTypes item=cat}
                {if $cat.rsrc_type == "event"}
                 <li><a href="{geturl controller='discussion'}{$cat.rsrc_type}/{$cat.cat_site_url}/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">{$cat.cat_type|capitalize|escape}</a></li>
                {/if}
               {/foreach}
               </ul>
          </li>
          {if $identity->mod}
          <li class="{if $identity->mod && $selectedRsrc == '99'}current-page{/if}">
               <a class="iconTextAlt iconBrowseChevron" href="/discussion/admin/all/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">Admin</a>
               <ul>
               {foreach from=$categoryTypes item=cat}
                {if $cat.rsrc_type == "admin"}
                 <li><a href="{geturl controller='discussion'}{$cat.rsrc_type}/{$cat.cat_site_url}/{$range}/{$order}/?view={$viewType}&amp;eFilter={$eFilter}">{$cat.cat_type|capitalize|escape}</a></li>
                {/if}
               {/foreach}
               </ul>
          </li>
          {/if}
          {if $authenticated}
            <li class="{if $selectedRsrc == 'bookmarks'}current-page {/if}iconText iconStarLarge"><a href="/discussion/bookmarks">Bookmarks</a></li>
          {/if}
        <li class="specialTab iconText iconQuestionLarge"><a href="{geturl controller='help' action='faq'}#Discussions">FAQ</a></li>
    </ul>
</div>	