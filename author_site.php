<?php get_header(); ?>

		<div id="content" class="narrowcolumn" role="main"><div class="sleeve">

		<h2>Author Archive</h2>

		<div class="bio clear">
			<div class="picture"><?php theme_author_avatar(60); ?></div>
			<h3 class="name"><?php the_author(); ?></h3>
			<div class="description"><?php the_author_meta('user_description', $GLOBALS['authordata']->ID); ?></div>
		</div>

		<?php the_author_recent_posts(); ?>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>

		</div> <!-- .sleeve -->
		</div> <!-- #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
