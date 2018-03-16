<?php
use SinFramework\Storage\LocalStorage;

require '../bootstrap.php';
require '../vendor/autoload.php';

//OUR GLOBAL VARS FOR THIS EXAMPLE
$fileID	= '1234567890_1234567890';
$bucket = 'mybucket';

//GET STORAGE OBJECT
$storage = getStorageObj();

//DELETE FILE
try {
	$storage->deleteFile($fileID, $bucket);
} catch (Exception $e) {
	die($e->getMessage());
}

//THIS FUNCTION IS RESPONSIBLE FOR CREATING AND SETTING UP THE STORAGE OBJECT
//IN A REAL WORLD APPLICATION, IT SHOULD BE CONTAINED IN SOME KIND OF DEPENDENCY INJECTOR CONTAINER
/**
 * @return \SinFramework\Storage\StorageInterface
 */
function getStorageObj() {
	$storageObj = new LocalStorage();
	$storageObj::setBaseStorageLocation('../storage');
	
	return $storageObj;
}