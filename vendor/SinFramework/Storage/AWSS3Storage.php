<?php

namespace SinFramework\Storage;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * Implementation of StorageInterface for using AWS Amazon S3 as storage for files.
 * 
 * @author alexandre.sinicio
 * @version 1.000.000
 */
class AWSS3Storage implements StorageInterface {
	private static $instance		= null;
	private static $awsRegion		= null;
	private static $tokenDuration	= 5;

	/**
	 * Sets the time (in minutes) that download tokens should be valid for.
	 * 
	 * @param int $intMinutes
	 * 		Token validity in minutes.
	 */
	public static function setTokenDuration($intMinutes) {
		self::$tokenDuration = $intMinutes;
	}
	/**
	 * Sets AWS region.
	 * 
	 * @param string $strAwsRegion
	 * 		AWS region as documented by AWS docs (`us-east-2`, `sa-east-1`, etc.)
	 */
	public static function setAwsRegion($strAwsRegion) {
		self::$awsRegion = $strAwsRegion;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\Storage\StorageInterface::putFile()
	 */
	 public static function putFile($filepath, $bucket) {
		if (!file_exists($filepath)) throw new \Exception('File not found');
		
		$s3			= self::getInstance();
		$identifier = StorageCommon::getStorageFilename($filepath, '');
		
		try {
			$response = $s3->putObject([
					'Bucket' => $bucket,
					'Key'    => $identifier,
					'Body'   => fopen($filepath, 'r'),
					'ACL'    => 'private',
			]);
			
			return $identifier;
		} catch (S3Exception $e) {
			return new \Exception('Error archiving file');
		}
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\Storage\StorageInterface::getFile()
	 */
	public static function getFile($identifier, $outputFilename, $bucket) {
		$s3	= self::getInstance();
		
		if (!$s3->doesObjectExist($bucket, $identifier)) throw new \Exception('File not found');
		
		$cmd = $s3->getCommand('GetObject', [
				'Bucket' 						=> $bucket,
				'Key'    						=> $identifier,
				'ResponseContentDisposition'	=> 'attachment;filename='.$outputFilename
		]);
		
		$request		= $s3->createPresignedRequest($cmd, '+'.self::$tokenDuration.' minutes');
		$presignedUrl	= (string) $request->getUri();
		
		header('Location: '.$presignedUrl);
		return true;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\Storage\StorageInterface::deleteFile()
	 */
	public static function deleteFile($identifier, $bucket) {
		$s3	= self::getInstance();
		
		try {
			$result = $s3->deleteObject([
					'Bucket'	=> $bucket,
					'Key'		=> $identifier,
			]);
		} catch (S3Exception $e) {
			throw new \Exception('Error deleting file');
		}
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\Storage\StorageInterface::calculateQuota()
	 */
	public static function calculateQuota($bucket) {
		$items = self::getStoredItems($bucket);
		
		$quota = 0;
		foreach($items as $item) {
			$quota += $item['Size'];
		}
		return $quota;
	}
	/**
	 * Gets singleton instance of S3Client.
	 * 
	 * @throws \Exception
	 * 		Invalid configuration throws error with human-readable information.
	 * @return \Aws\S3\S3Client
	 */
	private static function getInstance() {
		if (self::$instance == null) {
			if (self::$awsRegion == null) throw new \Exception('AWS region not set');
			
			$s3 = S3Client::factory([
					'version' => 'latest',
					'region'  => self::$awsRegion
			]);
			
			self::$instance = $s3;
		}
		
		return self::$instance;
	}
	/**
	 * Gets list of stored items in bucket.
	 * 
	 * @param string $bucket
	 * 		Bucket name
	 * @param number $maxKeys
	 * 		Max keys retrieved per iteration (AWS specifies 1000 at most).
	 * @throws \Exception
	 * 		All errors throw Exception with human-readable information.
	 * @return array
	 * 		Array containing items stored in bucket and their metadata info.
	 */
	private static function getStoredItems($bucket, $maxKeys = 1000) {
		$s3 = self::getInstance();
		
		$config = [
			'Bucket'	=> $bucket,
			'MaxKeys'	=> $maxKeys,
		];
		
		try {
			$count	= 0;
			$items	= [];
			do {
				$result 		= $s3->listObjectsV2($config);
				if (!isset($result['Contents'])) throw new \Exception('Could not access bucket');
				$items			= array_merge($items, $result['Contents']);
				$resultCount	= count($result['Contents']);
				$count			+= $resultCount;
				if ($resultCount == $maxKeys && isset($result['NextContinuationToken'])) {
					$config['ContinuationToken'] = $result['NextContinuationToken'];
				} else {
					$resultCount = 0;
				}
			} while($resultCount == $maxKeys);
		} catch (S3Exception $e) {
			throw new \Exception('Could not access bucket');
		}
		
		return $items;
	}
}