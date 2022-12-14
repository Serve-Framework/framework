<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\pixl;

use RuntimeException;
use serve\pixl\processor\ProcessorInterface;

use function file_exists;
use function is_writable;
use function pathinfo;
use function vsprintf;

/**
 * Image manager.
 *
 * @author Joe J. Howard
 */
class Image
{
    /**
     * Path to image file.
     *
     * @var string
     */
    private $image;

    /**
     * Processor instance.
     *
     * @var \serve\pixl\processor\ProcessorInterface
     */
    private $processor;

    /**
     * Constructor.
     *
     * @param \serve\pixl\processor\ProcessorInterface $processor Image processor implementation
     * @param string                                   $image     Absolute path to file (optional) (default '')
     */
    public function __construct(ProcessorInterface $processor, string $image = '')
    {
        $this->processor = $processor;

        if (!empty($image))
        {
            $this->loadImage($image);
        }
    }

    /**
     * Load an image file into the processor.
     *
     * @param  string           $image Absolute path to file (optional) (default '')
     * @throws RuntimeException If image file doesn't exist
     */
    public function loadImage(string $image): void
    {
        if (!file_exists($image))
        {
            throw new RuntimeException(vsprintf('The image [ %s ] does not exist.', [$image]));
        }

        $this->image = $image;

        $this->processor->load($this->image);
    }

    /**
     * Get the image width in px.
     *
     * @return int
     */
    public function width(): int
    {
        return $this->processor->width();
    }

    /**
     * Get the image height in px.
     *
     * @return int
     */
    public function height(): int
    {
        return $this->processor->height();
    }

    /**
     * Save the new file to disk.
     *
     * @param  string|null $image       Absolute path to file (optional) (default NULL)
     * @param  int|null    $image_type  PHP image type constant (optional) (default NULL)
     * @param  int|null    $quality     Quality of image to save (optional)
     * @param  int|null    $permissions File permissions to save with (optional)
     * @return mixed
     */
    public function save(?string $image = null, ?int $image_type = null, ?int $quality = null, ?int $permissions = null)
    {
        $image = $image ?? $this->image;

        if (file_exists($image))
        {
            if(!is_writable($image))
            {
                throw new RuntimeException(vsprintf('The file [ %s ] isn\'t writable.', [$image]));
            }
        }
        else
        {
            $pathInfo = pathinfo($image);

            if(!is_writable($pathInfo['dirname']))
            {
                throw new RuntimeException(vsprintf('The directory [ %s ] isn\'t writable.', [$pathInfo['dirname']]));
            }
        }

        return $this->processor->save($image, $image_type, $quality, $permissions);
    }

    /**
     * Resize to height.
     *
     * @param  int               $height        Height in px
     * @param  bool              $allow_enlarge Allow image to be enlarged ? (optional) (default FALSE)
     * @return \serve\pixl\Image
     */
    public function resizeToHeight(int $height, bool $allow_enlarge = false): Image
    {
        $this->processor->resizeToHeight($height, $allow_enlarge);

        return $this;
    }

    /**
     * Resize to width.
     *
     * @param  int               $width         Width in px
     * @param  bool              $allow_enlarge Allow image to be enlarged ? (optional) (default FALSE)
     * @return \serve\pixl\Image
     */
    public function resizeToWidth(int $width, bool $allow_enlarge = false): Image
    {
        $this->processor->resizeToWidth($width, $allow_enlarge);

        return $this;
    }

    /**
     * Scale image by a percentage.
     *
     * @param  int               $scale Scale percentage
     * @return \serve\pixl\Image
     */
    public function scale(int $scale): Image
    {
        $this->processor->scale($scale);

        return $this;
    }

    /**
     * Resize image to height and width.
     *
     * @param  int               $width         Width in px
     * @param  int               $height        Height in px
     * @param  bool              $allow_enlarge Allow image to be enlarged ? (optional) (default FALSE)
     * @return \serve\pixl\Image
     */
    public function resize(int $width, int $height, bool $allow_enlarge = false): Image
    {
        $this->processor->resize($width, $height, $allow_enlarge);

        return $this;
    }

    /**
     * Crop to width and height.
     *
     * @param  int               $width         Width in px
     * @param  int               $height        Height in px
     * @param  bool              $allow_enlarge Allow image to be enlarged ? (optional) (default FALSE)
     * @return \serve\pixl\Image
     */
    public function crop(int $width, int $height, bool $allow_enlarge = false): Image
    {
        $this->processor->crop($width, $height, $allow_enlarge);

        return $this;
    }

    /**
     * Add a background to the image.
     *
     * @param  int               $red   Red color value
     * @param  int               $green Green color value
     * @param  int               $blue  Blue color value
     * @return \serve\pixl\Image
     */
    public function addBackground(int $red, int $green, int $blue): Image
    {
        $this->processor->addBackground($red, $green, $blue);

        return $this;
    }
}
