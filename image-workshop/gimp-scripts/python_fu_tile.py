from gimpfu import *

def hstile():
  pdb.gimp_perspective(gimp.image_list()[0].layers[0], FALSE, 30, 20, 60, 40, 0, 40, 30, 60)

register(
  "python-fu-tile",
  "Transform to tile",
  "Transforms the image perspective-wise to fit the needs of the HandelSimulator's GraphicsEngine.",
  "Jan Merkelbag",
  "Jan Merkelbag",
  "2019",
  "<Image>/Image/Transform/_Tile image",
  "*",
  [],
  [],
  hstile)

main()
