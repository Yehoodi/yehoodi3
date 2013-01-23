<?php
ini_set("memory_limit", "64M");
date_default_timezone_set('America/Los_Angeles');
require_once('Yehoodi3.class.php');

// Windows
//set_include_path('C:\wamp\www\yehoodi3\trunk\library');
//require_once('C:\wamp\www\yehoodi3\trunk\library\Zend\Search\Lucene.php');
//$searchDir = 
'c:'.DIRECTORY_SEPARATOR.'wamp'.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'yehoodi3'.DIRECTORY_SEPARATOR.'trunk'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'search-index';

// Linux
set_include_path('/var/www/sites/yehoodi3.com/dev/trunk/library');
require_once('/var/www/sites/yehoodi3.com/dev/trunk/library/Zend/Search/Lucene.php');
$searchDir = '/var/www/sites/yehoodi3.com/dev/trunk/'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'search-index';

/**
 * Zend Lucene Search Stuff
 *
 */
function getIndexableDocument($id, $title, $descrip, $userName )
{
	$doc = new Zend_Search_Lucene_Document();
	$doc->addField(Zend_Search_Lucene_Field::Keyword('rsrc_id', $id));
	
	$fields = array(
		'title'		=> strip_tags($title),
		'descrip'	=> strip_tags($descrip),
		'user_name'	=> $userName
		);
		
	foreach ($fields as $name => $field)
	{
		//Zend_Debug::dump($fields);
		$doc->addField(Zend_Search_Lucene_Field::UnStored($name, $field));
	}
	
	return $doc;
}


$statusLIVE = 1;
$statusDRAFT = 0;
$resourceObj = new Yehoodi3();
$resourceCount = $resourceObj->getIndexableResourcesCount();
$limit = 1000;

echo "Rebuilding the Search Index on Yehoodi3\n";
echo "RESOURCES: {$resourceCount}\n\n";

$index = Zend_Search_Lucene::create($searchDir);

for ($offset = 0; $offset <= $resourceCount; $offset += 1000) {
	echo "\nLIMIT:".$limit." OFFSET:".$offset."\n";
	$allResources = $resourceObj->getIndexableResources($limit,$offset);

	//var_dump($allResources);die;

	try
		{
			
			foreach ($allResources as $resource)
			{
				echo ".";
				$index->addDocument( getIndexableDocument( $resource['rsrc_id'], $resource['title'], $resource['descrip'], $resource['user_name']));
			}
			$index->commit();
		}
	catch (Exception $ex)
		{
			//$logger = Zend_Registry::get('logger');
			echo 'Error rebuilding search index: ' . $ex->getMessage();
		}
}

echo "\nDone\n";
