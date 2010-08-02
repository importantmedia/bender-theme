<?php

remove_action('theme_page_top', 'theme_page_top');
add_action('theme_masthead', 'style_masthead');
add_filter('theme_masthead_filename', 'style_masthead_filename');

function style_masthead() {
  theme_adspot('728x90', '1');
}

function style_masthead_filename($content='') {
  return 'masthead.jpg';
}

?>
