		<div class="clear"></div>
	</div> <!-- #content_container -->
	<div id="footer">
		<div id="ad_footer"><?php theme_adspot('728x90', '2'); ?></div>
		<p><?php bloginfo('name'); ?> is a <a href="<?php echo get_blog_option(1, 'siteurl'); ?>"><?php echo get_blog_option(1, 'blogname'); ?></a> Production.
		<a href="http://creativecommons.org/licenses/by-nc-sa/2.5/">Some Rights Reserved</a>
		<a href="<?php bloginfo('rss2_url'); ?>" class="feed"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/rss_small_button.png" alt="Subscribe via RSS" /></a>
		</p>
	</div>

</div> <!-- #page -->
</div> <!-- #page_container -->

<?php wp_footer(); ?>
<?php  
	if (function_exists('ubiquity_print')) {
		ubiquity_print("tracker_bodybottom");
	}
?>
</body>
</html>
