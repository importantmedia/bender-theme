<?php
$queries = array(
	array('q' => 'showposts=1', 'class' => 'first_article'),
	array('q' => 'showposts=5&offset=1', 'class' => 'column left'),
	array('q' => 'showposts=5&offset=6', 'class' => 'column middle'),
	array('q' => 'showposts=5&offset=11', 'class' => 'column right'),
);

$GLOBALS['body_class'][] = 'multi-column';
?>
<?php get_header(); ?>

<div id="content"><div class="sleeve">

<h2><img src="<?php echo get_bloginfo('stylesheet_directory') .'/styles/'. get_option('bender_substyle'); ?>/sub-banner.png" alt="<?php echo get_bloginfo('name'); ?>" /></h2>

<div class="columns">

<?php foreach ($queries as $query): $q = new WP_Query($query['q']); ?>
	<div class="<?php echo $query['class'] ?>">
	<?php while ($q->have_posts()) : $q->the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
			<div class="byline"><?php the_time('F jS, Y') ?> by <span class="author"><?php the_author_archive_link(); ?></span></div>
			<div class="entry">
			<?php the_content('Read the rest of this entry &raquo;'); ?>
			</div>
			<div class="clear"></div>
		</div>
	<?php endwhile; ?>
	</div>
<?php endforeach; ?>

</div> <!-- #content .columns -->
</div> <!-- #content > sleeve -->
</div> <!-- #content -->

<?php get_footer(); ?>