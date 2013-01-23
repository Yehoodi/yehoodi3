#!/bin/sh

##
##	Yehoodi 3 Production Rsyncer
##
##	Pushes to production site
##

rsync -avz --exclude-from=/var/www/sites/yehoodi3.com/production_excludes /var/www/sites/yehoodi3.com/stage/trunk/ /var/www/sites/yehoodi3.com/prod/

# Compress JavaScript Files

# Prototype & Scriptaculous
#java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/frameworks/prototype1.5.1.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/frameworks/prototype1.5.1.js
#java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/frameworks/scriptaculous.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/frameworks/scriptaculous.js
#rm -rf /var/www/sites/yehoodi3.com/prod/public/js/frameworks/prototype1.5.1.src.js
#rm -rf /var/www/sites/yehoodi3.com/prod/public/js/frameworks/scriptaculous.src.js

# Account
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/account/AccountAvatarForm.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/account/AccountAvatarForm.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/account/Location.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/account/Location.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/account/LoginForm.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/account/LoginForm.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/account/UserRegistrationForm.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/account/UserRegistrationForm.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/account/AccountAvatarForm.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/account/Location.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/account/LoginForm.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/account/UserRegistrationForm.class.src.js

# Browse
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/browse/Browse.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/browse/Browse.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/browse/Browse.class.src.js

# Comment
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/comment/commentReply.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/comment/commentReply.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/comment/CommentSubmitForm.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/comment/CommentSubmitForm.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/comment/commentReply.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/comment/CommentSubmitForm.class.src.js

# Dashboard
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Bookmark.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Bookmark.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Dashboard.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Dashboard.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/dashboard/EventList.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/dashboard/EventList.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Watch.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Watch.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Bookmark.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Dashboard.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/dashboard/EventList.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/dashboard/Watch.class.src.js

# Google
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/google/LocationManager.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/google/LocationManager.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/google/LocationManager.src.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/google/MapDisplay.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/google/MapDisplay.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/google/MapDisplay.class.src.js

# lib
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/lib/BoxOver.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/lib/BoxOver.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/lib/ResourceActions.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/lib/ResourceActions.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/lib/ModeratorActions.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/lib/ModeratorActions.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/lib/utils.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/lib/utils.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/lib/YMDEditor.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/lib/YMDEditor.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/lib/Messages.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/lib/Messages.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/lib/BoxOver.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/lib/ResourceActions.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/lib/ModeratorActions.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/lib/utils.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/lib/YMDEditor.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/lib/Messages.class.src.js

# Mail
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/mail/Mail.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/mail/Mail.class.js
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/mail/MailMessage.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/mail/MailMessage.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/mail/Mail.class.src.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/mail/MailMessage.class.src.js

# Profile
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/profile/Profile.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/profile/Profile.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/profile/Profile.class.src.js

# Search
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/search/SearchBox.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/search/SearchBox.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/search/SearchBox.class.src.js

# Submit
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/submit/ResourceSubmitForm.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/submit/ResourceSubmitForm.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/submit/ResourceSubmitForm.class.src.js

# Show
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/show/ShowPage.class.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/show/ShowPage.class.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/show/ShowPage.class.src.js

# Misc
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/js/viewHelperFunctions.src.js -o /var/www/sites/yehoodi3.com/prod/public/js/viewHelperFunctions.js
rm -rf /var/www/sites/yehoodi3.com/prod/public/js/viewHelperFunctions.src.js

## Compress CSS Files

java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/account.css -o /var/www/sites/yehoodi3.com/prod/public/css/account.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/admin.css -o /var/www/sites/yehoodi3.com/prod/public/css/admin.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/browse.css -o /var/www/sites/yehoodi3.com/prod/public/css/browse.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/clear.css -o /var/www/sites/yehoodi3.com/prod/public/css/clear.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/comment.css -o /var/www/sites/yehoodi3.com/prod/public/css/comment.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/common.css -o /var/www/sites/yehoodi3.com/prod/public/css/common.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/dashboard.css -o /var/www/sites/yehoodi3.com/prod/public/css/dashboard.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/featured.css -o /var/www/sites/yehoodi3.com/prod/public/css/featured.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/form.css -o /var/www/sites/yehoodi3.com/prod/public/css/form.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/global.css -o /var/www/sites/yehoodi3.com/prod/public/css/global.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/grid_960_16_10_10.css -o /var/www/sites/yehoodi3.com/prod/public/css/grid_960_16_10_10.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/help.css -o /var/www/sites/yehoodi3.com/prod/public/css/help.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/home.css -o /var/www/sites/yehoodi3.com/prod/public/css/home.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/mail.css -o /var/www/sites/yehoodi3.com/prod/public/css/mail.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/message.css -o /var/www/sites/yehoodi3.com/prod/public/css/message.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/profile.css -o /var/www/sites/yehoodi3.com/prod/public/css/profile.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/register.css -o /var/www/sites/yehoodi3.com/prod/public/css/register.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/results.css -o /var/www/sites/yehoodi3.com/prod/public/css/results.css
# java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/search.css -o /var/www/sites/yehoodi3.com/prod/public/css/search.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/submit.css -o /var/www/sites/yehoodi3.com/prod/public/css/submit.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/tabs.css -o /var/www/sites/yehoodi3.com/prod/public/css/tabs.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/text.css -o /var/www/sites/yehoodi3.com/prod/public/css/text.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/user.css -o /var/www/sites/yehoodi3.com/prod/public/css/user.css
java -jar /var/www/utils/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar /var/www/sites/yehoodi3.com/prod/public/css/yehoodi.css -o /var/www/sites/yehoodi3.com/prod/public/css/yehoodi.css

cp /var/www/sites/yehoodi3.com/htaccess /var/www/sites/yehoodi3.com/prod/public/.htaccess
cp /var/www/sites/yehoodi3.com/htpasswd /var/www/sites/yehoodi3.com/prod/public/.htpasswd
cp /var/www/sites/yehoodi3.com/index.php /var/www/sites/yehoodi3.com/prod/public/index.php
cp /var/www/sites/yehoodi3.com/config.ini /var/www/sites/yehoodi3.com/prod/application/config/config.ini

#rm -rf /var/www/sites/yehoodi3.com/prod
#mv /var/www/sites/yehoodi3.com/prod/ /var/www/sites/yehoodi3.com/prod_temp

# Create symboic link to the openAds
#ln -s /var/www/sites/yehoodi3.com/openx-2.8.3/ /var/www/sites/yehoodi3.com/prod/public/openads

# Create symbolic link to show directory
#ln -s /var/www/sites/yehoodi3.com/shows/ /var/www/sites/yehoodi3.com/prod/public/shows
