<?php

namespace App\HandelsSimulator\GraphicsEngine;

use App\HandelsSimulator\GraphicsEngine\Tiles\BaseTile;

/**
 * Contains the visual representation of the logical map.
 * It's basically a two-dimensional array.
 *
 * @author Jan Merkelbag
 */
class Map {

  /**
   *
   * @var BaseTile[][]
   */
  private $fields = [ ];

  /**
   * Fast access to row count.
   *
   * @var integer
   */
  private $rowCount = 0;

  /**
   * Fast access to col count.
   * Also used for validation (ensure that all rows have equal amounts of cells).
   *
   * @var integer
   */
  private $colCount = 0;

  /**
   *
   * @param BaseTile[][] $fields
   */
  public function __construct( array $fields = []) {

    // Get row and column count.
    $this->rowCount = count ( $fields );
    $this->colCount = count ( reset ( $fields ) );

    // Iterate over each row...
    foreach ( $fields as $rowNo => $row ) {

      // Ensure that all rows have equal amounts of cells.
      if ( count ( $row ) !== $this->colCount ) {
        throw new \InvalidArgumentException ( "Row no. '{$rowNo}' has unexpected amount of cells! Actual: '" . count ( $row ) . "'; expected: '{$this->colCount}'" );
      }

      // Iterate over each cell...
      foreach ( $row as $colNo => $cell ) {

        // Ensure that cell is an object.
        if ( ! ($cell instanceof BaseTile) ) {

          // Complain about cell's class (if $cell is an object) or type (if it is not).
          throw new \InvalidArgumentException ( "All cells must be of type 'BaseTile'! Cell at {$colNo}|{$rowNo} is of type " . (is_object ( $cell ) ? get_class ( $cell ) : gettype ( $cell )) );
        }
      }
    }

    $this->fields = $fields;
  }
  public function getRowCount(): int {
    return $this->rowCount;
  }
  public function getColCount(): int {
    return $this->colCount;
  }
  public function getCellAt( int $x, int $y ): BaseTile {
    if ( $x > $this->colCount ) {
      throw new \OutOfRangeException ( "Cannot get cell at x:{$x} / y:{$y} because x exceeds column count!" );
    }
    if ( $y > $this->rowCount ) {
      throw new \OutOfRangeException ( "Cannot get cell at x:{$x} / y:{$y} because y exceeds row count!" );
    }

    return $this->fields [$x] [$y];
  }
}