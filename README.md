# Abstract Storage
Storing and retrieving files with PHP is easy... right?

Yes, it is, until you find out that you were storing files in your local server
and now needs to change that to Amazon S3, Google or whatever.

You hard-coded a lot of stuff in your code, and when you start to "rewire" stuff,
everything starts to break. It hurts.

The objective of this repo is to create an interface for file storage. This allow
us to separate the implementation for each storage provider and the actual code
which just want to store/retrieve/delete a file.

The basic implementation for local storage and Amazon S3 are included.

Feel free to contribute, criticize, extend and use.

## Terminology
Some storage providers use different terminology for stuff. It can lead to confusion,
and there's no way around it except choosing what seems the "best fit" for everything.

We use the term `bucket` on the interface as a way to represent a logic place where
files are stored. In Amazon S3, that is a native term. In local storage, `bucket`
just means `directory/folder`.

## Dependencies
The interface itself does not have any dependencies. However the Amazon S3 implementation
does. I suggest to use Composer and get the "aws/aws-sdk-php" package. Everything should be
there.

## Examples
### Basic general examples
	//OBJECT CREATION/INITIALIZATION -- SHOULD BE IN YOUR DEPENDENCY INJECTOR CONTAINER
	$storageObj = new LocalStorage();
	$storageObj::setBaseStorageLocation('../storage');

	//ARCHIVE A FILE
	try {
		$fileUID = $storage->putFile('myfile.txt', 'mybucket');
	} catch (Exception $e) {
		die($e->getMessage());
	}

	//DOWNLOAD A FILE
	try {
		$storage->getFile($fileUID, 'output_name.txt', 'mybucket');
	} catch (Exception $e) {
			die($e->getMessage());
		}