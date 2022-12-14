<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\http\request;

use serve\common\MagicObjectArray;

use function array_keys;
use function count;
use function is_array;

/**
 * Files manager.
 *
 * @author Joe J. Howard
 */
class Files extends MagicObjectArray
{
	/**
	 * Constructor.
	 *
	 * @param array $parameters $_FILES upload (optional) (default [])
	 */
	public function __construct(array $parameters = [])
	{
		$parameters = empty($parameters) ? $_FILES : $parameters;

		$this->data = $this->convertToUploadedFileObjects($parameters);
	}

	/**
	 * Creates a consistent uploaded file array.
	 *
	 * @param  array $file File info
	 * @return array
	 */
	protected function createUploadedFile(array $file): array
	{
		return
		[
			'tmp_name' => $file['tmp_name'],
			'name'     => $file['name'],
			'size'     => $file['size'],
			'type'     => $file['type'],
			'error'    => $file['error'],

		];
	}

	/**
	 * Normalizes a multi file upload array to a more manageable format.
	 *
	 * @param  array $files File upload array
	 * @return array
	 */
	protected function normalizeMultiUpload(array $files): array
	{
		$normalized = [];

		$keys = array_keys($files);

		$count = count($files['name']);

		for($i = 0; $i < $count; $i++)
		{
			foreach($keys as $key)
			{
				$normalized[$i][$key] = $files[$key][$i];
			}
		}

		return $normalized;
	}

	/**
	 * Converts the $_FILES array to an array of consistent arrays.
	 *
	 * @param  array $files File upload array
	 * @return array
	 */
	protected function convertToUploadedFileObjects(array $files): array
	{
		$uploadedFiles = [];

		foreach($files as $name => $file)
		{
			if(is_array($file['name']))
			{
				foreach($this->normalizeMultiUpload($file) as $file)
				{
					$uploadedFiles[$name][] = $this->createUploadedFile($file);
				}
			}
			else
			{
				$uploadedFiles[$name] = $this->createUploadedFile($file);
			}
		}

		return $uploadedFiles;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add(string $name, $value): void
	{
		if(is_array($value))
		{
			$value = $this->createUploadedFile($value);
		}

		$this->data[$name] = $value;
	}
}
