<?php

add_filter('theme_masthead_filename', 'style_masthead_filename');

function style_masthead_filename($content='') {
  return 'masthead.jpg';
}

?>
