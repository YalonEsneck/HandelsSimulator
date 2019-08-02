<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\HandelsSimulator\GraphicsEngine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Config\FileLocator;

class MapController extends AbstractController {
  private $fileLocator;
  public function __construct( FileLocator $fileLocator ) {
    $this->fileLocator = $fileLocator;
  }

  /**
   *
   * @Route("/map/test")
   */
  public function test() {
    return $this->render ( 'map/with_navigation.html.twig' );
  }

  /**
   *
   * @Route("/map/render/{coordX}/{coordY}/{colCount}/{rowCount}", name="render_map", requirements={"coordX"="\d+","coordY"="\d+","colCount"="\d+","rowCount"="\d+"})
   */
  public function renderMap( GraphicsEngine\GraphicsEngine $engine, int $coordX, int $coordY, int $colCount, int $rowCount ) {
    $response = new Response ();
    $response->headers->set ( 'Cache-Control', 'private' );
    $response->headers->set ( 'Content-Type', 'image/png' );

    $imageDirPath = $this->fileLocator->locate ( '@GraphicsEngineBundle' ) . '/Resources/images';
    $zeroImageDisposition = [
        0,
        0
    ];

    $terrainTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/simple/terrain.png", $zeroImageDisposition );
    $wallSeTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/wall_se.png", $zeroImageDisposition );
    $wallNsTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/wall_ns.png", $zeroImageDisposition );
    $wallNeTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/wall_ne.png", $zeroImageDisposition );
    $wallWeTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/wall_we.png", $zeroImageDisposition );
    $wallSwTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/wall_sw.png", $zeroImageDisposition );
    $wallNwTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/wall_nw.png", $zeroImageDisposition );

    // TODO this should be assembled from a database or such
    $dummy_map = [ ];
    for ( $x = 0; $x < 10; $x ++ ) {
      for ( $y = 0; $y < 10; $y ++ ) {
        if ( $x === 1 && $y === 1 ) {
          $dummy_map [$x] [$y] = $wallSeTile;
        } else if ( $x === 1 && $y === 8 ) {
          $dummy_map [$x] [$y] = $wallNeTile;
        } else if ( $x === 8 && $y === 1 ) {
          $dummy_map [$x] [$y] = $wallSwTile;
        } else if ( $x === 8 && $y === 8 ) {
          $dummy_map [$x] [$y] = $wallNwTile;
        } else if ( ($x === 1 || $x === 8) && $y > 1 && $y < 8 ) {
          $dummy_map [$x] [$y] = $wallNsTile;
        } else if ( ($y === 1 || $y === 8) && $x > 1 && $x < 8 ) {
          $dummy_map [$x] [$y] = $wallWeTile;
        } else {
          $dummy_map [$x] [$y] = $terrainTile;
        }
      }
    }

    $map = new GraphicsEngine\Map ( $dummy_map );

    $engine->render ( $map );
    $response->setContent ( $engine->getResultImage () );

    return $response;
  }
}
