#!/bin/sh

##
##	Yehoodi 3 Comment Number ReSynch Script
##	July 20 2009
##
##	Run this script only from the dbscripts dir
##	This script will resynch the comment numbers
##	in the comment database should they be
##	somehow out of order.
##
##	Takes about 10 minutes to complete.
##

echo
echo "Deleting comment rows with user_id = 0 and setting all rows to comment_num = 0..."
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < utility_delete_zero_user_ids.sql

echo
echo "Running utility_resynchCommentNum.php"
php -f utility_resynchCommentNum.php

echo
echo "Dumping utility_resynchCommentNum.sql"
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < utility_resynchCommentNum.sql

echo
echo "Deleting remaining comment_num = 0 rows..."
mysql -u webadmin --password='yehood1c0m' datyehoodi3_dev < utility_delete_zero_comment_nums.sql

echo "Cleaning up after myself..."
rm -rf utility_resynchCommentNum.sql

echo
echo "Done."