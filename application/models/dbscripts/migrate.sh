#!/bin/sh

##
##	Yehoodi 2 to Yehoodi 3 Data Migration Script
##	July 9 2009 (Happy Birthday Micah!)
##
##	Coverts all Yehoodi 2 data to the new format
##	Run this script only from the dbscripts dir
##
##	Takes about an hour to complete.
##

##
## Make sure to copy fresh versions of
## datYehoodi_2008.sql.gz
## datphpBB_2008.sql.gz
## news_photos.tgz
## and avatars.tgz
## into the dbscripts dir
## and unzip the avatars file and the news_photos file
## in the dir.
##

# Unzip database files to the current dir
echo "Unzipping the databases..."
gunzip *.gz

echo
echo "Extracting avatars..."
tar xvf avatars.tgz

#Dump the db's to the temp containers
echo
echo "Dumping Yehoodi 2 data..."
mysql -u webadmin --password='yehood1c0m' datyehoodi2_import < datYehoodi_2008.sql

echo "Dumping phpBB data..."
mysql -u webadmin --password='yehood1c0m' datphpbb_import < datphpBB_2008.sql

echo "Creating datyehoodi3_dev db container..."
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < datYehoodi3_schema.sql

echo "Adding resources, categories and user types..."
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < datYehoodi3_resource_schema.sql
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < datYehoodi3_category_schema.sql
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < datYehoodi3_user_type_schema.sql

# PHP scripts follow...
echo
echo
echo "Running 0_user_convert.php"
php -f 0_user_convert.php

echo
echo "Dumping usersImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < usersImport.sql
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < usersProfileImport.sql

echo
echo "Dumping datYehoodi3_temp_ubb_dates_schema.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < datYehoodi3_temp_ubb_dates_schema.sql

echo
echo "Running 0b_ubbUpdateDates.php"
php -f 0b_ubbUpdateDates.php

echo
echo "Dumping ubbDateUpdate.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < ubbDateUpdate.sql

echo
echo "Running 1_phpBB_convert.php"
php -f 1_phpBB_convert.php

echo
echo "Dumping phpBBImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < phpBBImport.sql

echo
echo "Running 2_phpBB_comment_convert.php"
php -f 2_phpBB_comment_convert.php

echo
echo "Dumping phpBBCommentImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < phpBBCommentImport.sql

echo
echo "Running 2a_ubbForumsImport.php"
php -f 2a_ubbForumsImport.php

echo
echo "Dumping ubbResourceImport.sql and ubbCommentImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < ubbResourceImport.sql
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < ubbCommentImport.sql

echo
echo "Running 3_YehoodiNews_convert.php"
php -f 3_YehoodiNews_convert.php

echo
echo "Dumping y2NewsImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < y2NewsImport.sql

echo
echo "Running 4_YehoodiEvent_convert.php"
php -f 4_YehoodiEvent_convert.php

echo
echo "Dumping y2EventImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < y2EventImport.sql

echo
echo "Running 4b_resourceLastCommentId.php"
php -f 4b_resourceLastCommentId.php

echo
echo "Dumping resourceLastCommentIdUpdate.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < resourceLastCommentIdUpdate.sql

echo
echo "Running 5_Event_insert.php"
php -f 5_Event_insert.php

echo
echo "Dumping y2EventImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < EventInsert.sql

echo
echo "Running 6_Yehoogle_convert.php"
php -f 6_Yehoogle_convert.php

echo
echo "Dumping YehoogleInsert.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < YehoogleInsert.sql

echo
echo "Running 7_Resource_Urls.php"
php -f 7_Resource_Urls.php

echo
echo "Dumping resourceUrlImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < resourceUrlImport.sql

echo
echo "Running 7a_resourceStats.php"
php -f 7a_resourceStats.php

echo
echo "Dumping phpTopicStats.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < phpTopicStats.sql

echo
echo "Running 8_Resource_Urls_cleanup.php"
php -f 8_Resource_Urls_cleanup.php

echo
echo "Deleting old avatars dir..."
rm -rf avatars/
echo "Copying source avatars folder..."
cp -r var/www/sites/yehoodi2.com/avatars/ .
chmod 777 avatars/

echo "Clearing the data/avatar dir for new avatars..."
rm -rf /var/www/sites/yehoodi3.com/dev/trunk/data/avatars/*
rm -rf /var/www/sites/yehoodi3.com/stage/trunk/data/avatars/*

echo
echo "Running 9_avatar_import.php"
php -f 9_avatar_import.php

echo
echo "Copying avatars to the stage directory..."
cp /var/www/sites/yehoodi3.com/dev/trunk/data/avatars/* /var/www/sites/yehoodi3.com/stage/trunk/data/avatars/

echo
echo "Dumping phpBBAvatarImport.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < phpBBAvatarImport.sql

echo
echo "Running 10_private_mesage_Import_v2.php"
#php -f 10_private_mesage_Import_v2.php

echo
echo "Dumping phpBBPrivMessageImportV2.sql"
#mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < phpBBPrivMessageImportV2.sql

echo
echo "Running 10a_private_mesage_Import_v2.php"
#php -f 10a_private_mesage_Import_v2.php

echo
echo "Dumping phpBBPrivMessageImportV2.sql"
#mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < mailMessageImport.sql

echo
echo "Running 11_private_message_body_Import.php"
#php -f 11_private_message_body_Import.php

echo
echo "Dumping phpBBPrivMessageBodyImport.sql"
#mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < phpBBPrivMessageBodyImport.sql

echo
echo "Running utility_resourceCountUpdater.php"
php -f utility_resourceCountUpdater.php

echo
echo "Dumping utility_resourceCountUpdater.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < utility_resourceCountUpdater.sql

echo
echo "Running utility_commentReplyToZero.sql to zero out all reply_to_ids"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < utility_commentReplyToZero.sql

echo
echo "Running the comment_num Re-synch script..."
./utility_resynch_comment_num.sh

echo "Cleaning up after myself: removing .sql files..."
rm -rf EventInsert.sql
rm -rf mailMessageImport.sql
rm -rf phpBBAvatarImport.sql
rm -rf phpBBCommentImport.sql
rm -rf resourceLastCommentIdUpdate.sql
rm -rf phpBBImport.sql
rm -rf phpBBPrivMessageBodyImport.sql
rm -rf phpBBPrivMessageImportV2.sql
rm -rf resourceUrlImport.sql
rm -rf ubbCommentImport.sql
rm -rf ubbDateUpdate.sql
rm -rf ubbResourceImport.sql
rm -rf usersImport.sql
rm -rf usersProfileImport.sql
rm -rf utility_resourceCountUpdater.sql
rm -rf y2EventImport.sql
rm -rf y2NewsImport.sql
rm -rf YehoogleInsert.sql
rm -rf phpTopicStats.sql
rm -rf var

# Sorce files
# rm -rf datphpBB_2008.sql
# rm -rf datYehoodi_2008.sql
# rm -rf news_photos.tgz
# rm -rf avatars.tgz

echo
echo "Clearing Smarty caches..."
rm -rf /var/www/sites/yehoodi3.com/dev/trunk/data/templates_c/*
rm -rf /var/www/sites/yehoodi3.com/stage/trunk/data/templates_c/*

echo "Flushing /tmp dirs..."
rm -rf /tmp/uploaded-files/*
rm -rf /tmp/thumbnails/*

echo
echo "Dumping final database for use in sandboxes..."
mysqldump -u webadmin --password='yehood1c0m' --opt datyehoodi3_dev > datyehoodi3_dev.sql
gzip datyehoodi3_dev.sql

echo
echo
echo "Done"
echo "You may want to run utility_ResourceImageConvert.sh to update the images in news stories"
echo
