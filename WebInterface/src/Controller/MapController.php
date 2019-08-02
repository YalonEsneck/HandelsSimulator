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
    $terrainTile = new GraphicsEngine\Tiles\Tile ( "{$imageDirPath}/new.terrain.png" );
    $wallSeTile = new GraphicsEngine\Tiles\Tile ( "{$imageDirPath}/wall_se.png" );
    $wallNsTile = new GraphicsEngine\Tiles\Tile ( "{$imageDirPath}/wall_ns.png" );
    $wallNeTile = new GraphicsEngine\Tiles\Tile ( "{$imageDirPath}/wall_ne.png" );
    $wallWeTile = new GraphicsEngine\Tiles\Tile ( "{$imageDirPath}/wall_we.png" );
    $wallSwTile = new GraphicsEngine\Tiles\Tile ( "{$imageDirPath}/wall_sw.png" );
    $wallNwTile = new GraphicsEngine\Tiles\Tile ( "{$imageDirPath}/wall_nw.png" );

    // TODO this should be assembled from a database or such
    $map = new GraphicsEngine\Map ( [
        [
            $terrainTile,
            $terrainTile,
            $terrainTile,
            $terrainTile,
            $terrainTile
        ],
        [
            $terrainTile,
            $wallSeTile,
            $wallNsTile,
            $wallNeTile,
            $terrainTile
        ],
        [
            $terrainTile,
            $wallWeTile,
            $terrainTile,
            $wallWeTile,
            $terrainTile
        ],
        [
            $terrainTile,
            $wallSwTile,
            $wallNsTile,
            $wallNwTile,
            $terrainTile
        ],
        [
            $terrainTile,
            $terrainTile,
            $terrainTile,
            $terrainTile,
            $terrainTile
        ]
    ] );

    $engine->render ( $map );
    $response->setContent ( $engine->getResultImage () );

    return $response;
  }
}