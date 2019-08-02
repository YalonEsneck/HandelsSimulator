<?php

namespace App\HandelsSimulator\GraphicsEngine\Tiles;

/**
 *
 * @author Jan Merkelbag
 *
 */
class GenericTile extends BaseTile {
  /**
   *
   * @param string $imagePath
   * @param array $imageDisposition
   * @see BaseTile::getImageDisposition
   */
  public function __construct( string $imagePath, array $imageDisposition ) {
    parent::__construct ( $imagePath );
    if ( count ( $imageDisposition ) !== 2 || ! is_int ( $imageDisposition [0] ) ) {
      throw new \InvalidArgumentException ( "Image disposition must contain an x (int) and y (int) at index 0 and 1 respectively!" );
    }
    list ( $this->imageAnchorX, $this->imageAnchorX ) = $imageDisposition;
  }

  /**
   *
   * {@inheritdoc}
   * @see \App\HandelsSimulator\GraphicsEngine\Tiles\BaseTile::setImageDisposition()
   */
  protected function setImageDisposition() {
  }
}