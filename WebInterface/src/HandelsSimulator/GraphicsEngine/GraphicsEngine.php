<?php
namespace App\HandelsSimulator\GraphicsEngine;

/**
 * This class is a singleton.
 *
 * @author Jan Merkelbag
 *
 */
class GraphicsEngine {

  /**
   * The instance if one exists.
   * Else NULL.
   *
   * @var GraphicsEngine
   */
  private static $instance = null;

  /**
   * Debug the rendering process.
   *
   * @var boolean
   */
  private static $DebugRendering = FALSE;

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
  private static $LoadedImages = array ();

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
  private function __construct($Cols, $Rows, $CellWidth, $CellHeight, $ImageHeight) {

    // set parameters
    $this->Cols = $Cols;
    $this->Rows = $Rows;
    $this->CellWidth = $CellWidth;
    $this->CellHeight = $CellHeight;
    $this->ImageHeight = $ImageHeight;
    $this->Width = $this->Cols * $this->CellWidth;
    $this->Height = $this->Rows * $this->CellHeight;

    // allocate final image to copy stuff to
    $this->Image = imagecreatetruecolor ( $this->Width, $this->Height );
    imagefill ( $this->Image, 0, 0, imagecolorallocate ( $this->Image, 255, 255, 255 ) );
  }

  /**
   */
  public function __destruct() {

    // destroy image on exit
    if ($this->Image !== null)
      imagedestroy ( $this->Image );
  }

  /**
   * Deny instantiation.
   */
  private function __clone() {
    throw new Exception ( 'Direct instantiation of singleton "' . __CLASS__ . '" not permitted!' );
  }

  /**
   * Initialise a new engine or get the existing one.
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
   * @return GraphicsEngine|NULL Returns either the current GraphicsEngine on success or NULL on failure.
   */
  public static function getInstance($Cols = 0, $Rows = 0, $CellWidth = 0, $CellHeight = 0, $ImageHeight = 0) {

    // check parameters for validity
    if (is_int ( $Cols ) && is_int ( $Rows ) && $Cols > 0 && $Rows > 0) {

      // create a new singleton if none exists
      if (self::$instance === null) {

        // get new instance
        self::$instance = new GraphicsEngine ( $Cols, $Rows, $CellWidth, $CellHeight, $ImageHeight );
      }

      // return singleton
      return self::$instance;
    }

    // failure
    return null;
  }

  /**
   * Places a tile image at a given position.
   * Respects different heights for images: The bottom of the image will always
   * be placed at the bottom of the tile.
   */
  private static function drawTile(stdClass $tileObject) {

    // TODO Get the image depending on the cell data. The image should already
    // have been created in the beginning of the rendering process - see below.
    if (! array_key_exists ( 'imgs/' . $tileObject->value . '.png', self::$LoadedImages )) {
      self::$LoadedImages [$tileObject->value] = imagecreatefrompng ( 'imgs/' . $tileObject->value . '.png' );
    }

    $img = self::$LoadedImages [$tileObject->value];
    $dimensions = getimagesize ( 'imgs/' . $tileObject->value . '.png' );

    // place the image at the given position
    imagecopyresampled ( self::$instance->Image, $img, $tileObject->xCoord, $tileObject->yCoord - $dimensions [1] + $tileObject->height, 0, 0, $dimensions [0], $dimensions [1], $dimensions [0], $dimensions [1] );
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
  private static function determineCoordinates(stdClass $tileObject, $maxCols, $maxRows) {

    // tilt the tiles first
    $tileObject = self::tilt ( $tileObject, $maxCols, $maxRows );

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
  private static function tilt(stdClass $tileObject, $maxCols, $maxRows) {

    // shift the tile to right or left depending on its x and y
    $tileObject->xMod = 0.5 * ($maxCols - $tileObject->x - 1 - $tileObject->y) + ($maxRows - $maxCols) * 0.5;

    // shift the tile up or down depending on its x and y
    $tileObject->yMod = ($tileObject->x - $tileObject->y) * 0.5;

    // debug string if necessary...
    if (self::$DebugRendering) {
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
   * @param array $Map
   *          An array containing the content of the map to be rendered.
   */
  public static function render(array $Map, $debuggingEnabled = FALSE) {

    // enable debugger if necessary
    self::$DebugRendering = $debuggingEnabled;

    // check whether image is usable
    if (self::$instance->Image === null) {
      throw new Exception ( 'Image was not initialised yet!' );
    }

    // iterate through each column in each row
    for($y = 0; $y < self::$instance->Rows; $y ++) {
      for($x = 0; $x < self::$instance->Cols; $x ++) {

        // construct the tile object for simplified data exchange
        $tileObject = new stdClass ();
        $tileObject->width = self::$instance->CellWidth;
        $tileObject->height = self::$instance->CellHeight;
        $tileObject->x = $x;
        $tileObject->y = $y;
        $tileObject->value = $Map [$x] [$y];

        // now we have to determine the coordinates
        $tileObject = self::determineCoordinates ( $tileObject, self::$instance->Cols, self::$instance->Rows );
        self::drawTile ( $tileObject, self::$instance->Cols, self::$instance->Rows );
      }
    }

    // clean up loaded images
    foreach ( self::$LoadedImages as $FilePath => $Resource ) {
      imagedestroy ( $Resource );
    }
  }

  /**
   * Print the rendered map as a PNG to stdout.
   */
  public static function output() {

    // output image
    if (! self::$DebugRendering) {
      imagepng ( self::$instance->Image );
    }
  }
}

header ( "Content-type: image/png" );
// header ( "Content-type: text/plain" );

define ( 'ROWS', 5 );
define ( 'COLS', 5 );

define ( 'CELLWIDTH', 60 );
define ( 'CELLHEIGHT', 40 );
define ( 'IMAGEHEIGHT', 60 );

$map = array ();
for($x = 0; $x < COLS; $x ++) {
  $map [$x] = array ();
  for($y = 0; $y < ROWS; $y ++)
    $map [$x] [$y] = rand ( 0, 3 );
}

$map = array (
    array (
        'terrain',
        'terrain',
        'terrain',
        'terrain',
        'terrain'
    ),
    array (
        'terrain',
        'wall_se',
        'wall_ns',
        'wall_ne',
        'terrain'
    ),
    array (
        'terrain',
        'wall_we',
        'terrain',
        'wall_we',
        'terrain'
    ),
    array (
        'terrain',
        'wall_sw',
        'wall_ns',
        'wall_nw',
        'terrain'
    ),
    array (
        'terrain',
        'terrain',
        'terrain',
        'terrain',
        'terrain'
    )
);