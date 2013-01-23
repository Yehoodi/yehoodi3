{ if $mail }
    <div class="div_pageControl">
        {include file='modules/module.pagination.tpl'}    	
        {include file='modules/module.add-link.tpl'} 
    </div> <!-- div_pageControl -->

    {include file='mail/mail-box.tpl'}
    
    <div class="div_pageControl">
        {include file='modules/module.pagination.tpl'}    	
        {include file='modules/module.add-link.tpl'} 
    </div> <!-- div_pageControl -->
{else}
    <p id="p_noMail">No Messages in your Mailbox. What a bringer downer...</p>
    {include file='modules/module.add-link.tpl'} 
{/if}