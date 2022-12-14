<?php

/**
 * @copyright Joe J. Howard
 * @license   https://github.com/Serve-Framework/Framework/blob/master/LICENSE
 */

namespace serve\pixl\processor;

use RuntimeException;

/**
 * Image processor interface.
 *
 * @author Joe J. Howard
 */
interface ProcessorInterface
{
    /**
     * Load image parameters for internal use.
     *
     * @param  string           $filename Absolute path to file
     * @throws RuntimeException If file is not an image
     */
    public function load(string $filename);

    /**
     * Get the image width in px.
     *
     * @return int
     */
    public function width(): int;

    /**
     * Get the image height in px.
     *
     * @return int
     */
    public function height(): int;

    /**
     * Save the new file to disk.
     *
     * @param  string   $filename    Absolute path to file
     * @param  int|null $image_type  PHP image type constant (optional) (default NULL)
     * @param  int|null $quality     Quality of image to save (optional)
     * @param  int|null $permissions File permissions to save with (optional)
     * @return mixed
     */
    public function save(string $filename, ?int $image_type = null, ?int $quality = null, ?int $permissions = null);

    /**
     * Resize to height.
     *
     * @param  int                                      $height        Height in px
     * @param  bool                                     $allow_enlarge Allow image to be enlarged ? (optional) (default FALSE)
     * @return \serve\pixl\processor\ProcessorInterface
     */
    public function resizeToHeight(int $height, bool $allow_enlarge = false);

    /**
     * Resize to width.
     *
     * @param  int                                      $width         Width in px
     * @param  bool                                     $allow_enlarge Allow image to be enlarged ? (optional) (default FALSE)
     * @return \serve\pixl\processor\ProcessorInterface
     */
    public function resizeToWidth(int $width, bool $allow_enlarge = false);

    /**
     * Scale image by a percentage.
     *
     * @param  int                                      $scale Scale percentage
     * @return \serve\pixl\processor\ProcessorInterface
     */
    public function scale(int $scale);

    /**
     * Resize image to height and width.
     *
     * @param  int                                      $width         Width in px
     * @param  int                                      $height        Height in px
     * @param  bool                                     $allow_enlarge Allow image to be enlarged ? (optional) (default FALSE)
     * @return \serve\pixl\processor\ProcessorInterface
     */
    public function resize(int $width, int $height, bool $allow_enlarge = false);

    /**
     * Crop to width and height.
     *
     * @param  int                                      $width         Width in px
     * @param  int                                      $height        Height in px
     * @param  bool                                     $allow_enlarge Allow image to be enlarged ? (optional) (default FALSE)
     * @return \serve\pixl\processor\ProcessorInterface
     */
    public function crop(int $width, int $height, bool $allow_enlarge = false);

    /**
     * Add a background to the image.
     *
     * @param  int                                      $red   Red color value
     * @param  int                                      $green Green color value
     * @param  int                                      $blue  Blue color value
     * @throws RuntimeException                         If file is not an image or not provided
     * @return \serve\pixl\processor\ProcessorInterface
     */
    public function addBackground(int $red, int $green, int $blue);
}
