<div id="sidecolumn">
	<?php theme_adspot('300x250', '1'); ?>
	<?php //theme_adspot('329x101', '1'); ?>

	<div id="sidebar">
		<div class="sleeve">
			<?php do_action('theme_sidebar_column_top'); ?>
			<ul>
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Right sidebar') ) : ?>

				<li>
					<h2>Welcome to ...</h2>
					<p><strong><?php bloginfo('blogname'); ?></strong></p>
					<p><?php theme_about(); ?></p>
				</li>

				<li id="subscribe">
					<h2>Subscribe</h2>
					<p class="rss">
						<span><a href="<?php bloginfo('rss2_url'); ?>">Subscribe via <abbr title="Really Simple Syndication">RSS</abbr></a></span>
					</p>
					<p class="twitter external">
						<span><a href="<?php theme_blog_twitter_url(); ?>">Follow us on Twitter</a></span>
					</p>
					<p class="email">
						<span>Subscribe via email</span>
						<?php $fb_email_domain = str_replace('.', '/', strtolower($_SERVER['SERVER_NAME'])); ?>
						<span>
						<form style="padding:0" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $fb_email_domain; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
							<input type="hidden" value="<?php echo $fb_email_domain; ?>" name="uri"/>
							<input type="hidden" name="loc" value="en_US"/>
							<input type="text" style="color:#777;width:90px;padding:2px 1px;font-size:90%;" name="email" value="Enter your email" onfocus="this.value='';this.style.color='#000';" />
							<input type="submit" value="Go" style="font-size:90%;padding:0;" />
						</form>
						</span>
					</p>
					<p class="fb-count"><a href="http://feeds.feedburner.com/<?php echo $fb_email_domain; ?>"><img style="padding-top:1em" src="http://feeds.feedburner.com/~fc/<?php echo $fb_email_domain; ?>?bg=FF9900&amp;fg=444444&amp;anim=1" height="26" width="88" style="border:0" alt="" /></a></p>
				</li>

				<li class="pagenav">
					<h2>Resources</h2>
					<ul>
						<li class="page_item"><a href="<?php echo get_blog_option(1, 'home'); ?>/writewithus/">Write for <?php bloginfo('name'); ?></a></li>
					<?php wp_list_pages('title_li='); ?>
					</ul>
				</li>

				<li><?php include (TEMPLATEPATH . '/searchform.php'); ?></li>
<?php if (false): // OBSOLETE ?>
				<li>
					<h2>Find us on Digg:</h2>
					<div id="tab-container-1" class="tab-container"><?php theme_digg_tabs(); ?></div>
				</li>
<?php endif; ?>
				
				<li>
					<?php recent_comments(); ?>
				</li>

				<li>
					<h2>Archives</h2>
					<form action="">
						<select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
							<option value=""><?php echo attribute_escape(__('Select Month')); ?></option>
							<?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?>
						</select>
					</form>
				</li>

				<?php wp_list_categories('show_count=1&title_li=<h2>Categories</h2>'); ?>

				<?php if (is_home() || is_page()): ?>
					<?php wp_list_bookmarks(); ?>

				<?php endif; //is_home ?>
<?php if (FALSE): ?>
				<li>
					<h2>Green Options Tags</h2>
					<?php if (function_exists('site_wide_tagcloud')) site_wide_tagcloud(25, ' ', true); ?>
				</li>
<?php endif; ?>
<!--[if IE]>
				<li>
					<p><a href="http://browsehappy.com/" title="Browse Happy: Switch to a safer browser today"><img src="http://browsehappy.com/buttons/bh_150x40_anim.gif" alt="Browse Happy logo" width="150" height="40"></a></p>
<![endif]-->
<!--[if IE 7]>
					<p><a href="http://en.wikipedia.org/wiki/Internet_Explorer_7">Your internet program</a> is <strong><?php echo date('Y') - 2006; ?></strong> years old. Consider upgrading to a <a href="http://browsehappy.com/">modern browser</a> for better compatibility and safety.</p>
<![endif]-->
<!--[if IE 6]>
					<p><a href="http://en.wikipedia.org/wiki/Internet_Explorer_6">Your internet program</a> is <strong><?php echo date('Y') - 2001; ?></strong> years old. Consider upgrading to a <a href="http://browsehappy.com/">modern browser</a> for better compatibility and safety.</p>
<![endif]-->
<!--[if (gte IE 5)&(lt IE 6)]>
					<p><a href="http://en.wikipedia.org/wiki/Internet_Explorer_5">Your internet program</a> is <strong><?php echo date('Y') - 1999; ?></strong> years old. Consider upgrading to a <a href="http://browsehappy.com/">modern browser</a> for better compatibility and safety.</p>
<![endif]-->
<!--[if IE]>
				</li>
<![endif]-->

			<?php endif; ?>
			</ul>

		</div> <!-- #sidebar > .sleeve -->
	</div> <!-- #sidebar -->
	<div id="side_ad">
		<div class="sleeve">
			<?php theme_adspot('160x600', '1'); ?>
			<?php theme_adspot('125x125', '1'); ?>
			<?php theme_adspot('blogads', '1'); ?>
			<?php theme_adspot('160x600', '2'); ?>
			<?php theme_adspot('160x600', '3'); ?>
			<?php theme_adspot('textad', '2'); ?>
			<?php do_action('theme_ad_column_bottom'); ?>
		</div> <!-- #side_ad > .sleeve -->
	</div> <!-- #side_ad -->
</div> <!-- #sidecolumn -->
