<?php

remove_action('theme_page_top', 'theme_page_top');
add_action('theme_masthead', 'style_masthead');
add_filter('theme_masthead_filename', 'style_masthead_filename');
add_action('theme_sidebar_column_top', 'style_content_sidebar_top');

function style_masthead() {
  theme_adspot('728x90', '1');
}

function style_masthead_filename($content='') {
  return 'masthead.jpg';
}

function style_content_sidebar_top() {
?>
<div><a href="http://www.guardian.co.uk/environment/network" class="external"><img src="<?php bloginfo('stylesheet_directory'); ?>/styles/ecoworldly.com/GEN_200x159.png" width="200" height="159" alt="Part of the Guardian Environment Network" /></a></div>
<?php
}

?>