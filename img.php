<?php
// once again, start the session at the very top!
session_start();

/* if there is a key */
if ($_SESSION['key']) {
/* define that the browser should treat
  the output of this file as an image */
  header("Content-type: image/png");

  // set the string to the key
  $string = $_SESSION['key'];

  // create an temporary image from a PNG file
  $im    = imagecreatefrompng("style/bg.png");

  // get a color from the image (in this case, yellow)
  $orange = imagecolorallocate($im, 220, 210, 60);

  /* Now we need to get widh and height of the image, so that we can center the
  key on the image, so that it does not go outside of the borders or look strange */
  $px    = (imagesx($im) - 7.5 * strlen($string)) / 2;
  $h = (imagesy($im) - 7.5) / 2;

  // here we write the key (the string)  on the image
  imagestring($im, 3, $px, $h, $string, $orange);

  // now create the final image
  imagepng($im);

  // to free up results, we need to destroy the temporary image.
  imagedestroy($im);
  }
?> 