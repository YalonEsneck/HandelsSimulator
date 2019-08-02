<?php

namespace App\HandelsSimulator\GraphicsEngine\Tiles;

/**
 *
 * @author Jan Merkelbag
 *
 */
abstract class BaseTile {

  /**
   * Width of all tiles in pixels.
   * TODO Static for now
   *
   * @var integer
   */
  protected static $width = 60;

  /**
   * Height of all tiles in pixels.
   * TODO Static for now
   *
   * @var integer
   */
  protected static $height = 40;

  /**
   * File path of the image to display.
   *
   * @var string
   */
  protected $imagePath = '';

  /**
   *
   * @var integer
   */
  protected $imageWidth = 0;

  /**
   *
   * @var integer
   */
  protected $imageHeight = 0;

  /**
   *
   * @var integer
   */
  protected $imageAnchorX = 0;

  /**
   *
   * @var integer
   */
  protected $imageAnchorY = 0;

  /**
   * File path of the image to display.
   *
   * @param string $imagePath
   */
  public function __construct( string $imagePath ) {
    if ( ! is_file ( $imagePath ) ) {
      throw new \InvalidArgumentException ( "Image path '{$imagePath}' points to no valid file!" );
    }
    $this->imagePath = $imagePath;

    list ( $this->imageWidth, $this->imageHeight ) = getimagesize ( $this->imagePath );
    $this->setImageDisposition ();
  }

  /**
   * Set the image disposition.
   *
   * @see BaseTile::getImageDisposition
   */
  abstract protected function setImageDisposition();

  /**
   * Returns the image disposition.
   *
   * The image disposition is the amount of X and Y to add to the image position when placed on the full map image.
   * This is to allow for asymmetric and/or overlapping images to be displayed correctly.
   *
   * @return int[]
   */
  public function getImageDisposition(): array {
    return [
        $this->imageAnchorX,
        $this->imageAnchorY
    ];
  }

  /**
   * Get the file path of the image to display.
   *
   * @return string
   */
  public function getImagePath(): string {
    return $this->imagePath;
  }

  /**
   * Returns actual image width in pixels.
   *
   * @return int
   */
  public function getImageWidth(): int {
    return $this->imageWidth;
  }

  /**
   * Returns actual image height in pixels.
   *
   * @return int
   */
  public function getImageHeight(): int {
    return $this->imageHeight;
  }

  /**
   * Returns height of all tiles in pixels.
   *
   * @return int
   */
  public static function getWidth(): int {
    return self::$width;
  }

  /**
   * Returns height of all tiles in pixels.
   *
   * @return int
   */
  public static function getHeight(): int {
    return self::$height;
  }
}