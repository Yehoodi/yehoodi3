<div id="div_archiveTable">
    <table>
    {if $showArchive}
        {foreach from=$showArchive item=rsrc}
        {cycle values=',alt' assign='class'}
            <tr class="{$class}">
                <td class="thumb"><img src="{imagefilename id=$rsrc->image->getId() w=31 h=31}" alt="placeholder" /></td>
                <th><strong><a href="{geturl controller='show'}{$showURL}{$rsrc->extended->show_episode}">{$rsrc->extended->show_name}</a></strong>
                    <ul class="meta">
                        <li>{$rsrc->meta->neatPostedDate}</li>
                    </ul>
                </th>
                {if $rsrc->extended->media_url}
                    <td class="downloadLink"><a class="iconText iconDownload" href="{$rsrc->extended->media_url}">Download</a></td>
                {/if}
            </tr>
        {/foreach}
    {else}
    <tr>
        <td rowspan="2">There are no shows in the archive</td>
    </tr>
    {/if}
    </table>
</div>