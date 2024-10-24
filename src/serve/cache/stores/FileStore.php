<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\cache\stores;

use serve\file\Filesystem;

use function serialize;
use function time;
use function unserialize;

/**
 * Cache file storage.
 *
 * @author Joe J. Howard
 */
class FileStore implements StoreInterface
{
    /**
     * {@inheritDoc}
     */
    private $path;

    /**
     * Filesystem instance.
     *
     * @var \serve\file\Filesystem
     */
    private $filesystem;

    /**
     * Constructo.
     *
     * @param \serve\file\Filesystem $filesystem Filesystem instance
     * @param string                 $path       Directory to store cache files
     */
    public function __construct(Filesystem $filesystem, string $path)
    {
        $this->path = $path;

        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        if ($this->has($key))
        {
            return unserialize($this->filesystem->getContents($this->keyToFile($key)));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function put(string $key, $data): void
    {
        $this->filesystem->putContents($this->keyToFile($key), serialize($data));
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $key): bool
    {
        return $this->filesystem->exists($this->keyToFile($key));
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): void
    {
        if ($this->has($key))
        {
            $this->filesystem->delete($this->keyToFile($key));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function expired(string $key, int $maxAge): bool
    {
        if ($this->has($key))
        {
            if ((($maxAge - time()) + $this->filesystem->modified($this->keyToFile($key))) < time())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $files = $this->filesystem->list($this->path);

        foreach ($files as $file)
        {
            $path = $this->path . DIRECTORY_SEPARATOR . $file;

            if ($this->filesystem->exists($path))
            {
                $this->filesystem->delete($path);
            }
        }
    }

    /**
     * Converts a key to the file path.
     *
     * @param string $key Key to convert
     */
    private function keyToFile(string $key): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $key . '.cache';
    }
}
