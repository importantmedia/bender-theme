<?php

remove_action('theme_page_top', 'theme_page_top');
remove_action('theme_single_top', 'theme_visitor_announcement');
remove_action('theme_index_after_post_content', 'theme_index_after_post_content');
remove_action('theme_single_after_post_content', 'theme_related_posts', 10);
remove_action('theme_single_after_post_content', 'theme_content_add_comment', 14);
remove_action('theme_single_after_post_content', 'theme_content_recommend', 15);
remove_action('theme_single_after_post_content', 'theme_content_post_actions', 20);
remove_filter('the_posts', 'cross_post_the_posts');

add_action('theme_navmenu', 'style_navmenu');

function style_navmenu() {
?>
<div id="navmenu">

<ul class="menu horizontal">
<li><a href="/about">About Important<br />Media</a></li>
<li><a href="/">Important<br />Announcements</a></li>
<li><a href="/writewithus">Write with<br />Important Media</a></li>
<li><a href="/advertise">Advertise with<br />Important Media</a></li>
</ul>
<div class="clear"></div>
</div>
<?php
}

?>
