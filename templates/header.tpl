<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
     <title>{$title|escape}</title>
     <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
     
     {* General Loaders: All pages *}
     <link rel="icon" type="image/png" href="/images/favicon.ico" />
     <link href="/css/{$controller}.css" media="all" rel="Stylesheet" type="text/css" />
     <link href="/css/yehoodi.css" media="all" rel="Stylesheet" type="text/css" />
     <link href="/css/print.css" media="print" rel="Stylesheet" type="text/css" />

    <meta name="google-site-verification" content="4kH5j_qXmuOYS8ql6KA9Y3XEzvxZ5EUqpMmUiTA9uss" />
     
     {if $controller == 'calendar' }
     {* We are loading jQuery here for the Calendar *}
          <script type="text/javascript" src="/js/jquery-qtip/jquery-1.3.2.min.js"></script>
          <script type="text/javascript" src="/js/jquery-qtip/jquery.qtip-1.0.0-rc3.min.js"></script>
          <script type='text/javascript' src='/js/fullcalendar/fullcalendar.{$jsExt}{$version}'></script>
          <script type='text/javascript' src='/js/fullcalendar/gcal.{$jsExt}{$version}'></script>
     {/if}

     {if $env != 'production'}     
          <script type="text/javascript" src="/js/frameworks/prototype1.5.1.js"></script>
          <script type="text/javascript" src="/js/frameworks/scriptaculous.js"></script>
     {else}
     	<script type="text/javascript" src="/js/frameworks/protoculous-1.0.2.js"></script>
     {/if}

     {* Specific Loaders *}
     {if $controller == 'index' }
          <script type="text/javascript" src="/js/lib/swfobject.js"></script>
          <script type="text/javascript">
          	swfobject.registerObject("flashCarousel", "9", "/flash/expressInstall.swf");
          </script>            
          <meta http-equiv="Pragma" content="no-cache" />
          <meta http-equiv="Expires" content="-1" />
     {/if}

     {if $controller == "show"}
     {* This is for outside scrapers (Facebook) to pull the show description *}
          <meta content="{$meta}" name="description" />
          <link rel="alternate" type="application/rss+xml" title="{$showTitle}" href="{$feed}" />
     {/if}

     {if $controller == 'comment' }
        <link href="/css/wmd.css" media="all" rel="Stylesheet" type="text/css" />
        <link href="/css/lightbox.css" media="all" rel="Stylesheet" type="text/css" />
     {/if}

     {if $controller == 'mail' }
        <link href="/css/wmd.css" media="all" rel="Stylesheet" type="text/css" />
     {/if}
     
     {if $controller == 'submit' }
        <link href="/css/wmd.css" media="all" rel="Stylesheet" type="text/css" />
     {/if}
     
     {if $maps}
     	<script type="text/javascript" src="http://www.google.com/jsapi?key={$mapConfig->googleAPIKey|escape}"> </script>
     {/if}
     <!--[if lt IE 7.]>
     	<script defer type="text/javascript" src="/js/pngfix.js"></script>
     <![endif]-->

     {* For OpenAds *}
     <!-- Generated by OpenX 2.8.3 -->
     <script type='text/javascript' src='http://yehoodi.com/openads/www/delivery/spcjs.php?id=1&amp;target=_blank'></script>
</head>

<body>
     <div id="div_header">
          <div id="div_headerContent">
               <h1><a href="/"><img src="{insert name='logo'}" width="278" height="115" alt="Yehoodi.com" /></a></h1>
               <div id="div_userLinks" class="{if $authenticated}loggedIn{else}notLoggedIn{/if}">
               {if $authenticated}
                    <span id="span_welcome"><a href="{geturl controller='account' action='avatar'}"><img class="avatar-tiny" alt="{$identity->user_name|escape}" src="{avatarfilename id=$myAvatar->getId()}" /></a> Welcome back, <strong>{$identity->user_name|escape}</strong>!</span>
                    <ul id="ul_userLinks">
                         <li><a class="iconText iconGearLarge" title="Manage Your Account" id="userLink_yourAccount" href="{geturl controller='account' action='summary'}">Account</a></li>
                         {if $unreadMailCount > 0}
                            <li class="unread"><a class="iconText iconMailExclamation" title="Mail ({$unreadMailCount} new)" id="userLink_mail" href="{geturl controller='mail'}">Mail ({$unreadMailCount})</a></li>
                         {else}
                             <li><a class="iconText iconMailOpenLarge" title="Mail" id="userLink_mail" href="{geturl controller='mail'}">Mail</a></li>
                         {/if}
                         {if $identity->mod == "true" }<li><a class="iconText iconWrenchLarge" title="Administration" id="userLink_admin" href="{geturl controller='admin'}">Admin</a></li>{/if}
                         <li><a class="iconText iconDoorOpen" title="Log out" id="userLink_logout" href="{geturl action='logout' controller='account'}">Log Out</a></li>
                    </ul>
               {else}
                    <span id="span_welcome">You are not logged in.</span>
                    <ul id="ul_userLinks">
                         <li><a class="iconText iconUserNew" id="userLink_register" href="{geturl action='register' controller='account'}">Register</a></li>
                         <li><a class="iconText iconDoorClosed" id="userLink_login" href="{geturl controller='account' action='login'}">Log In</a></li>
                    </ul>                           
               {/if}
               </div>
               <div id="div_navigation">
                    <ul>
                         <li id="nav_home"><a href="/">&nbsp;<span class="hidden">Home</span></a></li>
                         <li id="nav_discussions"><a {if $controller == 'discussion'} class="current-page"{/if} href="{geturl controller='discussion'}">Discussions</a></li>
                         <li id="nav_calendar"><a {if $controller == 'calendar'} class="current-page"{/if} href="{geturl controller='calendar'}">Calendar</a></li>
                         <li id="nav_show"><a {if $controller == 'show'} class="current-page"{/if} href="{geturl controller='show'}">Shows &amp; Podcasts</a></li>
                         <li id="nav_submit"><a {if $controller == 'submit'} class="current-page"{/if} href="{geturl controller='submit'}">Add a Topic</a></li>
                    </ul>
                    <form method="get" action="{geturl controller='search'}">
                    	<input class="input_text" value="Search Yehoodi..." id="input_searchBox" name="q" {if $q}value="{$q|escape}"{/if} type="text" />
                    </form>
               </div> <!-- #div_navigation -->
          </div> <!-- #div_headerContent -->
     </div> <!-- #div_header -->
     
     {if $controller != 'index' && $controller != 'ilhc'}
         {include file='modules/module.ad-banner.tpl'}
     {/if}
     
     <div id="div_content" class="container_16 content_{$section}">
          {include file='modules/module.message-box.tpl'}
          {* breadcrumbs trail=$breadcrumbs->getTrail() *}