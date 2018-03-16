<?php

namespace SinFramework\Storage;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Implementation of StorageInterface for using local filesystem as storage for files.
 * 
 * @author alexandre.sinicio
 * @version 1.000.000
 */
class LocalStorage implements StorageInterface {
	private static $baseStorageLocation = './';
	
	/**
	 * Sets base directory of storage.
	 * 
	 * @param string $strFilepath 
	 * 		Fully qualified base storage directory.
	 */
	public static function setBaseStorageLocation($strFilepath) {
		self::$baseStorageLocation = $strFilepath;
	}
	/**
	 * Gets base storage directory.
	 *
	 * @return string
	 * 		Fully qualified base storage directory.
	 */
	private static function getBaseStorageLocation() {
		return self::$baseStorageLocation;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\Storage\StorageInterface::putFile()
	 */
	public static function putFile($filepath, $bucket='') {
		if (!file_exists($filepath)) throw new \Exception('File not found');
		
		$storageLocation	= self::getFinalStorageLocation($bucket);
		$filename			= basename($filepath);
		$finalPathname		= StorageCommon::getStorageFilename($filename, $storageLocation);
		
		if (!rename($filepath, $finalPathname)) return false;
		
		return basename($finalPathname);
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\Storage\StorageInterface::deleteFile()
	 */
	public static function deleteFile($identifier, $bucket='') {
		$storageLocation	= self::getFinalStorageLocation($bucket);
		$filepath			= $storageLocation.$identifier;

		if (!file_exists($filepath))	throw new \Exception('File not found');
		if (!@unlink($filepath))		throw new \Exception('Could not delete file');
		
		return true;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\Storage\StorageInterface::calculateQuota()
	 */
	public static function calculateQuota($bucket='') {
		$directory	= self::getFinalStorageLocation($bucket);
		$size		= 0;
		
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
			$size += $file->getSize();
		}
		
		return $size;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\Storage\StorageInterface::getFile()
	 */
	public static function getFile($identifier, $outputFilename, $bucket) {
		$storageLocation	= self::getFinalStorageLocation($bucket);
		$filepath			= $storageLocation.$identifier;
		
		if (!file_exists($filepath)) throw new \Exception('File not found');
		
		set_time_limit(0);
		
		$fileLen = filesize($filepath);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $outputFilename . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . $fileLen);
		header('Expires: 0');
		header('Pragma: public');
		header('X-Pad: avoid browser bug');
		header('Cache-Control: no-cache');
		return @readfile($filepath);
	}
	/**
	 * Gets fully qualified pathname of storage location. Creates directory if needed.
	 * 
	 * @param string $bucket
	 * 		Bucket name.
	 * @return string
	 * 		Fully qualified pathname of storage location.
	 */
	private static function getFinalStorageLocation($bucket) {
		$storageLocation = self::getBaseStorageLocation();
		StorageCommon::guaranteePathExist($storageLocation);
		
		if ($bucket !== '') {
			$directory			= StorageCommon::normalizeSlashFolderPath($storageLocation);
			$filename			= StorageCommon::normalizeSlashFolderPath($bucket);
			$storageLocation	= $directory.$filename;
		}
		StorageCommon::guaranteePathExist($storageLocation);

		return $storageLocation;
	}
}