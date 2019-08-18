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

    $imageDirPath = $this->fileLocator->locate ( '@GraphicsEngineBundle' ) . 'Resources/images';
    $zeroImageDisposition = [
        0,
        0
    ];

    $terrainGrassTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/simple/terrain/grass.png", $zeroImageDisposition );
    $farmTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/simple/buildings/farm.png", $zeroImageDisposition );
    $farmlandTile = new GraphicsEngine\Tiles\GenericTile ( "{$imageDirPath}/simple/terrain/farmland.png", $zeroImageDisposition );

    // TODO this should be assembled from a database or such
    $dummy_map = [ ];
    for ( $x = 0; $x < 10; $x ++ ) {
      for ( $y = 0; $y < 10; $y ++ ) {
        $dummy_map [$x] [$y] = $terrainGrassTile;
      }
    }

    // Farm test
    for ( $x = 2; $x < 7; $x ++ ) {
      for ( $y = 2; $y < 7; $y ++ ) {
        $dummy_map [$x] [$y] = $farmlandTile;
      }
    }
    $dummy_map [3] [2] = $farmTile;

    $map = new GraphicsEngine\Map ( $dummy_map );

    $engine->render ( $map );
    $response->setContent ( $engine->getResultImage () );

    return $response;
  }
}
