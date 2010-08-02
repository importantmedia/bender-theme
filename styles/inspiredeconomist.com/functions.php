<?php

remove_action('theme_page_top', 'theme_page_top');

add_action('theme_masthead', 'style_masthead');

function style_masthead() {
  theme_adspot('728x90', '1');
}

?>