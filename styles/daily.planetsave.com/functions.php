<?php

add_action('theme_alternate_home', 'style_alternate_home');
add_action('theme_masthead', 'style_masthead');

function style_alternate_home() {
  include 'home.php';
  exit;
}

function style_masthead() {
?>
<ul id="menu">
	<li><a href="http://webmail.planetsave.com/">Webmail</a></li>
</ul>
<?php
}

?>