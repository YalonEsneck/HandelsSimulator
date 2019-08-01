<?php

namespace App\HandelsSimulator\GraphicsEngine\Tiles;

/**
 *
 * @author Jan Merkelbag
 *
 */
class Tile {

  /**
   * File path of the image to display.
   *
   * @var string
   */
  protected $imagePath = '';

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

    // TODO move this to config file or so
  }

  /**
   * Get the file path of the image to display.
   *
   * @return string
   */
  public function getImagePath(): string {
    return $this->imagePath;
  }
  public function getHeight(): int {
  }
  public function getWidth(): int {
  }
}