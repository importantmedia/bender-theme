<?php

remove_action('theme_page_top', 'theme_page_top');
remove_filter('the_content', 'theme_category_links_filter', 1);
remove_filter('the_posts', 'cross_post_the_posts');

add_action('theme_navmenu', 'style_navmenu');
add_action('theme_masthead', 'style_masthead');
add_filter('theme_excerpt_defaults', 'style_excerpt_defaults');
add_action('theme_single_after_post', 'style_single_after_post', 1);
add_filter('theme_adspot_attributes', 'style_adspot_attributes');

function style_navmenu() {

  // Get sub-categories of "Topics". Items are ordered by name.
  $args = array(
    'child_of' => get_cat_ID('Topics'),
    'orderby' => 'name',
    'hide_empty' => 0,
    'depth' => -1,
    'hierarchical' => false,
    'title_li' => __(''),
  );

?>
<div id="navmenu">

<ul class="menu horizontal">
<li><a href="/">Front Page</a></li>
<?php wp_list_categories($args); ?>
</ul>

<?php theme_widget_search( array('before_widget'=>'<div id="search">','after_widget'=>'</div>') ); ?>
<div class="clear"></div>
</div>
<?php
}

function style_excerpt_defaults($defaults) {
  $defaults['more_link_text'] = 'Read more &raquo;';
  $defaults['allowed_tags'] = '<p>';
  $defaults['more_tag'] = 'span';
  $defaults['excerpt_length'] = 120;
  return $defaults;
}

function style_masthead() {
  theme_adspot('468x60', '1');
}

function style_adspots($location_name_map) {
  foreach ($location_name_map as $k => $map) {
    if ($map['location'] == 'T') {
      $location_name_map[$k]['dimensions'] = '468x60';
    }
  }

  return $location_name_map;
}

function style_adspot_attributes($attrs) {
  if (!is_home())
    $attrs['RGB'] = array('RGB_R4');

  return $attrs;
}

function style_single_after_post() {
  global $post, $comments, $id;

  if (!(is_single() && $post->post_type == 'post'))
    return;

  $cats = array();
  if (in_category('Liberal')) {
    $cats[] = get_cat_ID('Center');
    $cats[] = get_cat_ID('Conservative');
  } elseif (in_category('Conservative')) {
    $cats[] = get_cat_ID('Liberal');
    $cats[] = get_cat_ID('Center');
  } elseif (in_category('Center')) {
    $cats[] = get_cat_ID('Liberal');
    $cats[] = get_cat_ID('Conservative');
  }

  if (!empty($cats)) {
    $current_comments = $comments;
    $current_post = $post;
    $current_id = $id;

    echo '<div class="category-posts"><h2>Other Views from '.get_option('blogname').'</h2>';
    foreach ($cats as $cat) {
      $query['showposts'] = 1;
      $query['category__and'] = array($cat, get_cat_ID('Leader'));
      $query['category__not_in'] = get_cat_ID('Editor\'s Choice');

      $q = new WP_Query($query);
      if ($q->have_posts()) {
        while ($q->have_posts()) {
          $q->the_post();
?>
		<div class="post half" id="post-<?php the_ID(); ?>">
			<?php theme_post_thumbnail(array('before'=>'<div class="post-thumbnail">','after'=>'</div>')); ?>
			<h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			<div class="entry">
				<?php the_excerpt(); ?>
			</div>
		</div>
<?php
        }
      }
    }
    echo '<div class="clear"></div></div>';
    $id = $current_id;
    $post = $current_post;
    $comments = $current_comments;
    $posts = array($current_post);
    update_post_caches($posts);
  }
}

?>