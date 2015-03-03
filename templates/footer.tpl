	</div> <!-- div_content -->
    <div id="div_footer">
        <div class="container_16">
            <div class="grid_3 alpha">
            	<h4>Navigate</h4>	
                <ul>                	
                    <li><a {if $controller == 'index'}class="currentSelection"{/if} href="/">Home</a></li>
                    <li><a {if $controller == 'discussion'}class="currentSelection"{/if} href="{geturl controller='discussion'}">Discussions</a></li>
                    <li><a {if $controller == 'calendar'}class="currentSelection"{/if} href="{geturl controller='calendar'}">Event Calendar</a></li>
                    <li><a {if $controller == 'submit'}class="currentSelection"{/if} href="{geturl controller='submit'}">Add a Topic</a></li>
                    <li><a {if $controller == 'show'}class="currentSelection"{/if} href="{geturl controller='show'}">Shows &amp; Podcasts</a></li>
                    <li><a {if $controller == 'search'}class="currentSelection"{/if} href="{geturl controller='search'}">Search</a></li>
                </ul>				
            </div>
            <div class="grid_3">
            	<h4>User Links</h4>
                <ul>
                    { if $authenticated }
                        <li><a {if $controller == 'account'}class="currentSelection"{/if} href="{geturl controller='account' action='summary'}">Account Settings</a></li>
                        <li><a {if $controller == 'mail'}class="currentSelection"{/if} href="{geturl controller='mail'}">Mail</a> {if $unreadMailCount > 0}<span id="unreadMailFooter">({$unreadMailCount})</span>{/if}</li>
                        { if $identity->mod == "true" }
                            <li><a {if $controller == 'admin'}class="currentSelection"{/if} href="{geturl controller='admin'}">Administration</a></li>
                        {/if }
                        <li><a href="{geturl action='logout' controller='account'}">Logout</a></li>
                    { else }
                        <li><a {if $controller == 'account' && $section == 'register'}class="currentSelection"{/if} href="{geturl controller='account' action='register'}">Register</a></li>
                        <li><a {if $controller == 'account' && $section == 'login'}class="currentSelection"{/if} href="{geturl controller='account' action='login'}">Log In</a></li>
                    {/if}                    
                </ul>				
            </div> 
            <div class="grid_3">
            	<h4>About Yehoodi</h4>
                <ul>
                    <li><a {if $controller == 'help' && $section== 'faq'}class="currentSelection"{/if} href="{geturl controller='help/faq'}">FAQ</a></li>                                       
                    <li><a {if $controller == 'help' && $section== 'colophon'}class="currentSelection"{/if} href="{geturl controller='help/colophon'}">Colophon</a></li>                    
                    <li><a {if $controller == 'help' && $section== 'privacy-policy'}class="currentSelection"{/if} href="{geturl controller='help/privacy-policy'}">Privacy Policy</a></li>
                    <li><a {if $controller == 'help' && $section== 'terms-of-agreement'}class="currentSelection"{/if} href="{geturl controller='help/terms-of-agreement'}">Terms of use</a></li> 
                   	<li><a {if $controller == 'help' && $section== 'contact'}class="currentSelection"{/if} href="{geturl controller='help/contact'}">Contact Us</a></li>
                </ul>				
            </div>                       
            <div id="copyright" class="grid_7 omega">
            	<p>Copyright &copy; {$smarty.now|date_format:"%Y"} Yehoodi.com<br />
                    Yehoodi version 3.1.<span id="yehoodi_revision">2533</span><br />
                    Built <span id="yehoodi_release_date">02.03.2011</span><br />               
                    <a href="#div_header">Top of page</a>
            	</p>
            </div>
            <div class="clear">&nbsp;</div>
        </div>
    </div> <!-- div_footer -->
    
<script type="text/javascript" src="/js/search/SearchBox.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new SearchBox();
</script>

<script type="text/javascript" src="/js/lib/Messages.class.{$jsExt}{$version}"></script>
<script type="text/javascript">
	new MessagesClass();
</script>

<script type="text/javascript" src="/js/lib/konami-js/konami.js"></script>
<script type="text/javascript" src="/js/lib/konami-js/konami_example.js"></script>
<script type="text/javascript">
    konami.load();
</script>

{if $env == 'production'}
    {literal}
    <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
        try {
            var pageTracker = _gat._getTracker("UA-12345844-1");
            pageTracker._trackPageview();
        } catch(err) {}
    </script>
    {/literal}
{/if}
</body>
</html>
