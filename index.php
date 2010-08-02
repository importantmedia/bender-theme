<?php do_action('theme_alternate_home'); ?>

<?php get_header(); ?>

		<div id="content"><div class="sleeve">

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Left content') ) : ?>

<?php
if (have_posts()) :
  do_action('theme_index_top');
  $firstpost = true;
  while (have_posts()) :
    the_post();
?>
		<div class="post<?php if (theme_is_cross_post()) echo ' cross-post'; ?>" id="post-<?php the_ID(); ?>">
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<?php if (!theme_is_cross_post()): ?>
			<div class="postinfo">
				<div class="byline">
					<span class="avatar"><?php echo get_avatar($GLOBALS['post']->post_author, 32); ?></span>
					<span class="author">Written by <?php theme_post_author(); ?></span>
				</div>
				<div class="date"><span class="verb">Published</span> on <?php the_time('F jS, Y'); ?></div>
				<div class="comments"><a href="<?php the_permalink() ?>#comments"><?php comments_number(''); ?></a></div>
				<?php the_category(','); ?>
			</div>
			<?php endif; ?>
			<div class="entry">
				<?php theme_the_content(); ?>
			</div>

			<div class="postmetadata">
				<?php if (!theme_is_cross_post()) the_tags( '<div>Tags: ', ', ', '</div>'); ?>
			</div>
			<?php do_action('theme_index_after_post_content'); ?>

		</div>
<?php
    if ($firstpost) {
      theme_adspot('468x60', 'L1');
      $firstpost = false;
    }
  endwhile;
?>

	<div class="navigation">
		<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
		<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
	</div>

<?php else : ?>

	<h2 class="center">Whoa, slow down</h2>
	<p class="center">You're too fast for us. We haven't even posted yet. Try back later.</p>
<?php endif; ?>

<?php endif; ?>

		</div> <!-- #content > .sleeve -->
		</div> <!-- #content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
