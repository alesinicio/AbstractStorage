<?php
namespace SinFramework\Storage;

interface StorageInterface {
	/**
	 * Storages a file.
	 * 
	 * @param string $filepath
	 * 		Fully qualified pathname to origin file.
	 * @param string $bucket
	 * 		Bucket name for storaging purposes.
	 * @throws \Exception
	 * 		All errors throw Exception with human-readable information.
	 * @return string
	 * 		Unique identifier of archived file.
	 */
	public static function putFile($filepath, $bucket);
	/**
	 * Gets file from storage.
	 * 
	 * @param string $identifier
	 * 		Unique identifier generated when file was first archived.
	 * @param string $outputFilename
	 * 		Output filename for user-downloading purposes.
	 * @param string $bucket
	 * 		Bucket where the file is archived.
	 * @throws \Exception
	 * 		All errors throw Exception with human-readable information.
	 * @return int
	 * 		File size in bytes.
	 */
	public static function getFile($identifier, $outputFilename, $bucket);
	/**
	 * Deletes file from storage.
	 * 
	 * @param string $identifier
	 * 		Unique identifier generated when file was first archived.
	 * @param string $bucket
	 * 		Bucket where the file is archived.
	 * @throws \Exception
	 * 		All errors throw Exception with human-readable information.
	 * @return boolean
	 * 		TRUE if file was succesfully deleted.
	 */
	public static function deleteFile($identifier, $bucket);
	/**
	 * Calculates total space usage on bucket.
	 * 
	 * @param string $bucket
	 * 		Bucket name.
	 * @return int
	 * 		Total file size in bytes.
	 */
	public static function calculateQuota($bucket);
}