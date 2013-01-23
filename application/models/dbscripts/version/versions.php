<?php
/**
 * This File is run during the push to production
 * directly AFTER the new pages get rsynched to the
 * production dir.
 * 
 * This goes through the list of files in the $files
 * array and adds a "v?={timestamp}" to all .css and .js
 * files to force browser cache clearing and reloading
 * of those files.
 */
require 'version.class.php';
$fullPath = '/var/www/sites/yehoodi3.com/prod/';    // Linux
//$fullPath = '/wamp/www/prod/';            // Windows

$files = array(
                'public/css/yehoodi.css',
                'public/css/submit.css',
                'public/css/calendar.css',
                'public/css/mail.css',
                
                'templates/account/avatar.tpl',
                'templates/account/drafts-see-all.tpl',
                'templates/account/ignored-see-all.tpl',
                'templates/account/login.tpl',
                'templates/account/register.tpl',
                'templates/account/settings.tpl',
                'templates/account/watched-see-all.tpl',
                'templates/calendar/index.tpl',
                'templates/comment/index.tpl',
                'templates/discussion/index.tpl',
                'templates/mail/index.tpl',
                'templates/mail/message.tpl',
                'templates/profile/index.tpl',
                'templates/search/index.tpl',
                'templates/show/show.tpl',
                'templates/submit/index.tpl',

                'templates/header.tpl',
                'templates/footer.tpl',
              );
              
foreach ($files as $file) {
    $version = new version();
    
    $version->loadFile($fullPath . $file);
    $version->versionCSS();
    $version->versionJS();
    $version->saveFile();
}

echo 'Done';