{if $error|@is_array || $error|strlen > 0}
   {assign var=hasError value=true}
{else}
    {assign var=hasError value=false}
{/if}

<div class="error"{if !$hasError} style="display:none;"{/if}>
    {if $error|@is_array}
        <ul>
            {foreach from=$error item=str}
                <li class="iconText iconX"> {$str|escape}</li>
            {/foreach}
        </ul>
    {else}
        <p class="iconText iconX">{$error|escape}</p>
    {/if}
</div>