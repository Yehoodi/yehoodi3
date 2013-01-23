<div id="div_resultsFeatured" class="grid_16 alpha">
    {assign var=resources value=$latestFeatures}
    { if $resources|@count > 0 }
    <h2>Featured Topics <span class="seeAll">[ <a href="{geturl controller='discussion' action='featured'}/all/30days/activity/">See more</a> ]</span></h2>
    {include file='index/module-featured-topics-loop.tpl'}
    {/if}
</div>
<div class="div_resultsOther grid_8 alpha">
    {assign var=resources value=$latestLindy}
    { if $resources|@count > 0 }
    <h3>Lindy <span class="seeAll">[ <a href="{geturl controller='discussion' action='lindy'}/all/30days/activity/">See more</a> ]</span></h3>
    {include file='index/module-topics-loop.tpl'}
    {/if}    
    {assign var=resources value=$latestEvent}
    { if $resources|@count > 0 }
    <h3>Events <span class="seeAll">[ <a href="{geturl controller='discussion' action='event'}/all/30days/activity/">See more</a> ]</span></h3>
    {include file='index/module-topics-loop.tpl'}
    {/if}
</div>
<div class="div_resultsOther grid_8 omega">
    {assign var=resources value=$latestLounge}
    { if $resources|@count > 0 }
    <h3>The Lounge <span class="seeAll">[ <a href="{geturl controller='discussion' action='lounge'}/all/30days/activity/">See more</a> ]</span></h3>
    {include file='index/module-topics-loop.tpl'}
    {/if}
</div>
<div class="clear">&nbsp;</div>