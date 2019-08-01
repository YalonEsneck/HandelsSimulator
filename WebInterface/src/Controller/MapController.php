<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController {
  /**
   *
   * @Route("/map/test")
   */
  public function test() {
    return new Response('<html><body>Map</body></html>');
  }
}