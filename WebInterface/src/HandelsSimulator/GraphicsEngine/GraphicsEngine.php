<?php

namespace App\HandelsSimulator\GraphicsEngine;

/**
 *
 * @author Jan Merkelbag
 */
class GraphicsEngine {

  /**
   * Debug the rendering process.
   *
   * @var boolean
   */
  private $DebugRendering = FALSE;

  /**
   * Count of tiles from left to right.
   *
   * @var integer
   */
  private $Cols = 0;

  /**
   * Count of tiles from top to bottom.
   *
   * @var integer
   */
  private $Rows = 0;

  /**
   * Total width of a image in pixels.
   *
   * @var integer
   */
  private $Width = 0;

  /**
   * Total height of image in pixels.
   *
   * @var integer
   */
  private $Height = 0;

  /**
   * Width of a cell in pixels.
   *
   * @var integer
   */
  private $CellWidth = 0;

  /**
   * Height of a cell in pixels.
   *
   * @var integer
   */
  private $CellHeight = 0;

  /**
   * Height of an image in pixels.
   *
   * @var integer
   */
  private $ImageHeight = 0;

  /**
   * An image file containing the rendered map.
   *
   * @var resource
   */
  private $Image = null;

  /**
   * Contains the loaded images.
   * The key is the file path and the value is the loaded resource.
   *
   * @var array
   */
  private $LoadedImages = array ();

  /**
   * Deny instantiation.
   *
   * @param int $Cols
   *          Count of tiles from left to right.
   * @param int $Rows
   *          Count of tiles from top to bottom.
   * @param int $CellWidth
   *          Width of a cell in pixels.
   * @param int $CellHeight
   *          Height of a cell in pixels.
   * @param int $CellHeight
   *          Height of an image in pixels.
   */
  public function __construct() {

    // set parameters
    $this->Cols = COLS;
    $this->Rows = ROWS;
    $this->CellWidth = CELLWIDTH;
    $this->CellHeight = CELLHEIGHT;
    $this->ImageHeight = IMAGEHEIGHT;
    $this->Width = $this->Cols * $this->CellWidth;
    $this->Height = $this->Rows * $this->CellHeight;

    // allocate final image to copy stuff to
    $this->Image = imagecreatetruecolor ( $this->Width, $this->Height );

    // enable alpha channel (opaque)
    imagesavealpha ( $this->Image, true );

    // fill image with transparent background
    imagefill ( $this->Image, 0, 0, imagecolorallocatealpha ( $this->Image, 0, 0, 0, 127 ) );
  }

  /**
   */
  public function __destruct() {

    // destroy image on exit
    if ( $this->Image !== null )
      imagedestroy ( $this->Image );
  }

  /**
   * Deny instantiation.
   */
  private function __clone() {
    throw new \Exception ( 'Direct instantiation of singleton "' . __CLASS__ . '" not permitted!' );
  }

  /**
   * Places a tile image at a given position.
   * Respects different heights for images: The bottom of the image will always
   * be placed at the bottom of the tile.
   */
  private function drawTile( \stdClass $tileObject ) {

    // TODO Get the image depending on the cell data. The image should already
    // have been created in the beginning of the rendering process - see below.
    if ( ! array_key_exists ( $tileObject->value, $this->LoadedImages ) ) {
      $this->LoadedImages [$tileObject->value] = imagecreatefrompng ( $tileObject->value );
    }

    $img = $this->LoadedImages [$tileObject->value];
    $dimensions = getimagesize ( $tileObject->value );

    // place the image at the given position
    imagecopyresampled ( $this->Image, $img, $tileObject->xCoord, $tileObject->yCoord - $dimensions [1] + $tileObject->height, 0, 0, $dimensions [0], $dimensions [1], $dimensions [0], $dimensions [1] );
    // This is the calculation which should be used above:
    // (img, tileObject.xCoord, tileObject.yCoord - img.height + tileObject.height)

    // TODO Maybe not destroy the image here... Let's just create all in the
    // beginning of the render process and destroy them at the end - otherwise
    // the hard drive is going to be used to extensively (thus slowing down the
    // whole rendering process).
    // Or we're just going to lazy load the images actually being used just in
    // time while destroying them altogether at the end anyway (I don't like the
    // idea of having image files loaded into RAM all the time...).
    //
    // It's done.
    // imagedestroy ( $img );
  }

  /**
   * Calculate the coordinates in pixels of a tile's position.
   */
  private function determineCoordinates( \stdClass $tileObject, $maxCols, $maxRows ) {

    // tilt the tiles first
    $tileObject = $this->tilt ( $tileObject, $maxCols, $maxRows );

    // map the x and y values to the absolute coordinate in pixels
    $tileObject->xCoord = $tileObject->width * ($tileObject->x + $tileObject->xMod);
    $tileObject->yCoord = $tileObject->height * ($tileObject->y + $tileObject->yMod);

    return $tileObject;
  }

  /**
   * Tilt the tiles by +45°.
   *
   * Basically this method converts the following...
   * +-----------+
   * | 0 | 1 | 2 |
   * |---+---+---|
   * | 3 | 4 | 5 |
   * |---+---+---|
   * | 6 | 7 | 8 |
   * +-----------+
   *
   * into...
   * _________---
   * ______--- 0 ---
   * ___--- 3 --- 1 ---
   * --- 6 --- 4 --- 2 ---
   * ___--- 7 --- 5 ---
   * ______--- 8 ---
   * _________---
   */
  private function tilt( \stdClass $tileObject, $maxCols, $maxRows ) {

    // shift the tile to right or left depending on its x and y
    $tileObject->xMod = 0.5 * ($maxCols - $tileObject->x - 1 - $tileObject->y) + ($maxRows - $maxCols) * 0.5;

    // shift the tile up or down depending on its x and y
    $tileObject->yMod = ($tileObject->x - $tileObject->y) * 0.5;

    // debug string if necessary...
    if ( $this->DebugRendering ) {
      echo implode ( PHP_EOL, array (
          '---',
          $tileObject->x . '|' . $tileObject->y,
          'xMod: (' . $maxCols . ' - ' . $tileObject->x . ' - 1 - ' . $tileObject->y . ') / 2 = ' . $tileObject->xMod,
          'yMod: (' . $tileObject->x . ' - ' . $tileObject->y . ') / 2 = ' . $tileObject->yMod
      ) ) . PHP_EOL;
    }

    return $tileObject;
  }

  /**
   * Renders, saves and returns the image.
   *
   * @param Map $Map
   *          The map to be rendered.
   */
  public function render( Map $map, $debuggingEnabled = FALSE) {

    // enable debugger if necessary
    $this->DebugRendering = $debuggingEnabled;

    // check whether image is usable
    if ( $this->Image === null ) {
      throw new \Exception ( 'Image was not initialised yet!' );
    }

    // iterate through each column in each row
    for ( $y = 0; $y < $map->getRowCount (); $y ++ ) {
      for ( $x = 0; $x < $map->getColCount (); $x ++ ) {

        // construct the tile object for simplified data exchange
        $tileObject = new \stdClass ();
        $tileObject->width = $this->CellWidth;
        $tileObject->height = $this->CellHeight;
        $tileObject->x = $x;
        $tileObject->y = $y;
        $tileObject->value = $map->getCellAt ( $x, $y )->getImagePath ();

        // now we have to determine the coordinates
        $tileObject = $this->determineCoordinates ( $tileObject, $this->Cols, $this->Rows );
        $this->drawTile ( $tileObject, $this->Cols, $this->Rows );
      }
    }

    // clean up loaded images
    foreach ( $this->LoadedImages as /*$FilePath =>*/ $Resource ) {
      imagedestroy ( $Resource );
    }
  }

  /**
   * Print the rendered map as a PNG to stdout.
   */
  public function output() {

    // output image
    if ( ! $this->DebugRendering ) {
      imagepng ( $this->Image );
    }
  }

  /**
   * Return the rendered map.
   */
  public function getResultImage( string $type = 'png') {

    // output image
    if ( ! $this->DebugRendering ) {
      if ( $type === 'png' ) {
        ob_start ();
        imagepng ( $this->Image );
        $imageData = ob_get_contents ();
        ob_end_clean ();
        return $imageData;
      } else {
        throw new \InvalidArgumentException ( "Unknown renderer type '{$type}'!" );
      }
    }
  }
}

define ( 'ROWS', 5 );
define ( 'COLS', 5 );

define ( 'CELLWIDTH', 60 );
define ( 'CELLHEIGHT', 40 );
define ( 'IMAGEHEIGHT', 60 );
