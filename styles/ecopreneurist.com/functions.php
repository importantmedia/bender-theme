<?php

remove_action('theme_page_top', 'theme_page_top');
add_action('theme_masthead', 'style_masthead');
//add_action('theme_ad_column_bottom', 'style_ad_column_bottom');

function style_masthead() {
  theme_adspot('728x90', '1');
}

function style_ad_column_bottom() {
?>
<div class="site-block">
<style type="text/css" media="all">
#tp_widget {width:125px;font-family:verdana,sans-serif;font-size:10px;}
#tp_widget .rss-box {padding: 32px 0 0 0;background:url(http://www.triplepundit.com/3p-wid-ban.gif) no-repeat 0 0;border:1px solid #767676;border-bottom:0;text-align:left;background-color:#fff;}
#tp_widget .rss-box .rss-items {list-style:none;margin:0;padding:8px 5px 3px 5px;border-top:1px solid #d9d9d9;}
#tp_widget .rss-box .rss-item {margin:0 0 8px 0;}
#tp_widget .rss-box a:link {color:#295FAD;text-decoration:underline;}
#tp_widget .rss-box a:visited {color:#295FAD;text-decoration:underline !important;}
#tp_widget .rss-box a:hover {text-decoration: none !important;}
#tp_footer {height:2em;padding:6px;background-color:#767676;}
#tp_footer a:link,#tp_footer a:visited {color:#FFF;text-decoration:underline;}
#tp_footer img {margin:0 0 0 4px;vertical-align:middle;}
</style>
<div id="tp_widget">
<script type="text/javascript" src="http://feed2js.org/feed2js.php?src=http%3A%2F%2Fwww.triplepundit.com%2Fprivate.xml&amp;num=5&amp;targ=y&amp;utf=y"></script>
<div id="tp_footer">
  <a class="visit" href="http://www.triplepundit.com">Visit 3P Today!</a>
  <a class="img" href="http://feeds.feedburner.com/TriplePundit"><img src="/wp-content/themes/pro/images/feed-icon-14x14.png" border="0" alt="" /></a>
</div>
</div>
</div>
<?php
}

?>
