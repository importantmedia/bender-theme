<?php get_header(); ?>

		<div id="content"><div class="sleeve">

<?php is_tag(); ?>
		<?php if (have_posts()) : $firstpost=true; ?>

		 <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>

 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>

 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>

	 <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>

		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>

	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle">Author Archive</h2>

		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle">Blog Archives</h2>

		<?php } ?>


		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="postinfo">
					<div class="byline">
						<span class="avatar"><?php theme_author_avatar(); ?></span>
						<span class="author">Written by <?php theme_post_author(); ?></span>
					</div>
					<div class="date"><span class="verb">Published</span> on <?php the_time('F jS, Y'); ?></div>
					<div class="comments"><a href="<?php the_permalink() ?>#comments"><?php comments_number(''); ?></a></div>
					<?php $cats = get_the_category(); if ($cats[0]->name != 'Uncategorized'): ?>
					<div class="cats"><span class="verb">Posted</span> in <?php the_category(', '); ?></div>
					<?php endif; ?>
				</div>

				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

				<div class="postmetadata">
					<?php the_tags( '<div>Tags: ', ', ', '</div>'); ?>
				</div>
				<?php do_action('theme_index_after_post_content'); ?>


			</div>
			<?php if ($firstpost) { theme_adspot('468x60', '1'); $firstpost = false; } ?>
		<?php endwhile; ?>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

		</div> <!-- #content > .sleeve -->
		</div> <!-- #content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
