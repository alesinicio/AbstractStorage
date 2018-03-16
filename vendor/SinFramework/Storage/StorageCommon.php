<?php

namespace SinFramework\Storage;

/**
 * Common methods for StorageInterface objects.
 * 
 * @author alexandre.sinicio
 * @version 1.000.000
 */
class StorageCommon {
	/**
	 * Generates a unique random filename for archiving a file.
	 *
	 * @param string $filename
	 * 		Name of the file being stored.
	 * @param string $storageLocation
	 * 		Fully qualified pathname of storage location.
	 * @return string
	 * 		Fully qualified pathname where file should be archived.
	 */
	public static function getStorageFilename($filename, $storageLocation) {
		do {
			$rand			= microtime().mt_rand();
			$uniqueFilename	= md5($rand).'_'.md5($filename.$rand);
		} while (file_exists($storageLocation.$uniqueFilename));
		
		return $storageLocation.$uniqueFilename;
	}
	/**
	 * Guarantees a path/directory exists, creating it if needed.
	 *
	 * @param string $path
	 * 		Fully qualified path to directory.
	 */
	public static function guaranteePathExist($path) {
		if (!file_exists($path)) mkdir($path);
	}
	/**
	 * Guarantees all paths will end with a single slash.
	 *
	 * @param string $path
	 * 		Pathname.
	 * @return string
	 * 		Normalized pathname with single slash on the end.
	 */
	public static function normalizeSlashFolderPath($path) {
		return rtrim($path, '/\\').'/';
	}
}