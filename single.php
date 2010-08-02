<?php
get_header();
?>

		<div id="content"><div class="sleeve">
<?php
if (have_posts()) :
  do_action('theme_single_top');
  while (have_posts()) :
    the_post();
?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<div class="postinfo">
				<div class="byline">
					<span class="avatar"><?php theme_author_avatar(); ?></span>
					<span class="author">Written by <?php theme_post_author(); ?></span>
				</div>
				<div class="date"><span class="verb">Published</span> on <?php the_time('F jS, Y'); ?></div>
				<div class="comments"><a href="#comments"><?php comments_number(''); ?></a></div>
				<?php the_category(','); ?>
			</div>
			<div class="entry">
				<?php theme_digg_related_widget(); ?>
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p id="post-nav">Pages: ', 'pagelink' => '<span class="post-page-num">%</span>')); ?>

				<div class="postmetadata">
					<?php the_tags( '<div>Tags: ', ', ', '</div>'); ?>
				</div>
				
				<?php theme_wt_adsense(); ?>
				
				<?php edit_post_link('Edit this entry','<p>','.</p>'); ?>
				<?php theme_adspot('530x114', '1'); ?>
				<?php do_action('theme_single_after_post_content'); ?>

			</div>
		</div>

<?php
    do_action('theme_single_after_post');
    comments_template();
  endwhile;

else:
?><p>Sorry, no posts matched your criteria.</p><?php
endif;

?>
		</div> <!-- #content > .sleeve -->
		</div> <!-- #content -->
<?php
get_sidebar();
get_footer();
?>
