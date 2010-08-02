<?php

add_action('admin_menu', 'theme_add_page');
add_action('init', 'theme_widget_update');

function theme_add_page() {
  if (isset($_GET['page']) && $_GET['page'] == 'theme-options' && isset($_POST['Submit'])) {
    check_admin_referer('bender-theme-options');
    if (isset($_POST['substyle'])) {
      if ($_POST['substyle'])
        update_option('bender_substyle', $_POST['substyle']);
      else
        delete_option('bender_substyle');
    }
    if (isset($_POST['gac'])) {
      if ($_POST['gac'])
        update_option('google_analytics_code', $_POST['gac']);
      else
        delete_option('google_analytics_code');
    }
    if (isset($_POST['twitter'])) {
      if ($_POST['twitter'])
        update_option('twitter_url', $_POST['twitter']);
      else
        delete_option('twitter_url');
    }
    if (isset($_POST['description'])) {
      if ($_POST['description'])
        update_option('blog_extended_description', $_POST['description']);
      else
        delete_option('blog_extended_description');
    }
    if (isset($_POST['about'])) {
      if ($_POST['about'])
        update_option('blog_about', $_POST['about']);
      else
        delete_option('blog_about');
    }
    $GLOBALS['bender-options-saved'] = true;
  }
  add_theme_page(__('Customize Header'), __('Theme Options'), 'edit_themes', 'theme-options', 'theme_options_page');
}

function theme_options_page() {
  if (isset($GLOBALS['bender-options-saved']))
    echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.').'</strong></p></div>';
?>
<div class="wrap">
	<h2><?php _e('Options'); ?></h2>
	<form method="post" action="">
		<?php wp_nonce_field('bender-theme-options'); ?>
		<table class="form-table"><tbody>
			<tr>
				<th scope="row"><?php _e('Sub-style'); ?></th>
				<td>
					<select name="substyle">
						<option> </option>
						<?php
						$styles = theme_get_substyles();
						foreach ($styles as $style) {
						  if ($style == get_option('bender_substyle'))
						    echo '<option selected="selected">'. $style .'</option>';
						  else
						    echo '<option>'. $style .'</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Blog Description'); ?></th>
				<td>
					<input type="text" name="description" value="<?php echo get_option('blog_extended_description'); ?>" size="75" />
					<div><?php _e('This description will appear in the &lt;meta name="description"&gt; tag of the front page.'); ?></div>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('About this Blog'); ?></th>
				<td>
					<input type="text" name="about" value="<?php echo get_option('blog_about'); ?>" size="75" />
					<div><?php _e('This text will appear in the "Welcome" area of the blog sidebar.'); ?></div>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Google Analytics ID'); ?></th>
				<td>
					<input type="text" name="gac" value="<?php echo get_option('google_analytics_code'); ?>" />
					<div><?php _e('Example:'); ?> <code>_gat._getTracker("<strong style="color:#f00">UA-5555555-1</strong>");</code></div>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Twitter Page Address'); ?></th>
				<td>
					<input type="text" name="twitter" value="<?php echo get_option('twitter_url'); ?>" size="75" />
					<div><?php _e('Example:'); ?> <code>http://twitter.com/gas2</code></div>
				</td>
			</tr>
		</tbody></table>
		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Save Changes'); ?>" />
		</p>
	</form>
<?php
}

function theme_widget_category_posts_control($widget_args = 1) {
  global $wp_registered_widgets;
  static $updated = false; // Whether or not we have already updated the data after a POST submit

  $id_base = 'category-posts';

  if (is_numeric($widget_args)) {
    $widget_args = array('number' => $widget_args);
  }
  $widget_args = wp_parse_args($widget_args, array('number' => -1));
  extract($widget_args, EXTR_SKIP);

  // Data should be stored as array:  array( number => data for that instance of the widget, ... )
  $options = get_option('widget_bender_category_posts');
  if (!is_array($options))
    $options = array();

  // Update the widget options
  if (!$updated && !empty($_POST['sidebar'])) {
    $sidebar = (string) $_POST['sidebar'];

    $sidebars_widgets = wp_get_sidebars_widgets();
    if (isset($sidebars_widgets[$sidebar]))
      $this_sidebar =& $sidebars_widgets[$sidebar];
    else
      $this_sidebar = array();

    foreach ($this_sidebar as $_widget_id) {
      // Remove all widgets of this type from the sidebar.  We'll add the new data in a second.  This makes sure we don't get any duplicate data
      // since widget ids aren't necessarily persistent across multiple updates
      if ('theme_category_posts' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
        $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
        if (!in_array($id_base.'-'.$widget_number, $_POST['widget-id'])) // the widget has been removed.
          unset($options[$widget_number]);
      }
    }

    foreach ((array) $_POST['category-posts'] as $widget_number => $widget_many_instance) {
      if (!isset($widget_many_instance['cat']) && isset($options[$widget_number]) ) // user clicked cancel
        continue;
      $cat = $widget_many_instance['cat'];
      $category__not_in = $widget_many_instance['category__not_in'];
      $showposts = intval($widget_many_instance['showposts']);
      $show_post_meta = intval($widget_many_instance['show_post_meta']);
      $sticky_cat = intval($widget_many_instance['sticky_cat']);
      $sticky_posts = intval($widget_many_instance['sticky_posts']);
      $more_label = $widget_many_instance['more_label'];
      $title = trim(strip_tags($widget_many_instance['title']));
      if ($widget_many_instance['post_width'] == 'half')
        $post_width = 'half';
      else
        $post_width = 'full';

      $options[$widget_number] = array(
      	'cat' => $cat,
        'category__not_in' => $category__not_in,
      	'showposts' => $showposts,
        'show_post_meta' => $show_post_meta,
        'post_width' => $post_width,
        'more_label' => $more_label,
        'sticky_cat' => $sticky_cat,
        'sticky_posts' => $sticky_posts,
        'title' => $title,
      );
    }

    update_option('widget_bender_category_posts', $options);

    $updated = true; // So that we don't go through this more than once
  }

  if (-1 == $number) {
    $cat = array();
    $category__not_in = array();
    $showposts = 5;
    $show_post_meta = 0;
    $more_label = '';
    $post_width = 'full';
    $sticky_cat = -1;
    $sticky_posts = 0;
    $title = '';
    $number = '%i%';
  } else {
    $cat = $options[$number]['cat'];
    $category__not_in = $options[$number]['category__not_in'];
    $showposts = intval($options[$number]['showposts']);
    $more_label = $options[$number]['more_label'];
    $show_post_meta = intval($options[$number]['show_post_meta']);
    $post_width = $options[$number]['post_width'];
    $sticky_cat = $options[$number]['sticky_cat'];
    $title = $options[$number]['title'];
    $sticky_posts = $options[$number]['sticky_posts'];
  }

  // The form has inputs with names like widget-many[$number][something] so that all data for that instance of
  // the widget are stored in one $_POST variable: $_POST['widget-many'][$number]
?>
        <p>Posts with <em>all</em> of the selected categories, and none of the excluded categories, will be displayed.</p>
		<p><label><?php
        _e('Categories: ');
        $args = array(
          'hide_empty' => 0,
          'name' => 'category-posts['.$number.'][cat][]',
          'orderby' => 'name',
          'hierarchical' => 1,
          'show_count' => 1,
          'class' => 'widefat',
          'show_option_none' => 'None',
          'selected' => $cat,
        );
        theme_select_categories($args);
        ?></label></p>
		<p><label><?php
		_e('Categories to exclude: ');
		$args['name'] = 'category-posts['.$number.'][category__not_in][]';
		$args['selected'] = $category__not_in;
		theme_select_categories($args);
		?></label></p>
		<p><label><?php _e('Number of posts to show:'); ?><input name="category-posts[<?php echo $number; ?>][showposts]" type="text" value="<?php echo $showposts; ?>" style="width:25px;text-align:center;" /><br /><small>(at most 15)</small></label></p>
		<p><label><?php _e('Sticky category:'); wp_dropdown_categories(array('show_option_none'=>'None', 'hierarchical'=>1, 'name'=>'category-posts['.$number.'][sticky_cat]','selected'=>$sticky_cat)); ?></label></p>
		<p><label><?php _e('Number of sticky posts:'); ?><input name="category-posts[<?php echo $number; ?>][sticky_posts]" type="text" value="<?php echo $sticky_posts; ?>" style="width:25px;text-align:center;" /><br /><small>(at most 5)</small></label></p>
		<p><label><?php _e('More link text:'); ?> <input class="widefat" type="text" name="category-posts[<?php echo $number; ?>][more_label]" value="<?php echo $more_label; ?>" /></label></p>
		<p><label><?php _e('Title:'); ?> <input classs="widefat" type="text" name="category-posts[<?php echo $number; ?>][title]" value="<?php echo $title; ?>" /></label></p>
		<p><?php _e('Post column width:'); ?> <label><?php _e('Full'); ?> <input class="widefat" name="category-posts[<?php echo $number; ?>][post_width]" type="radio" value="full" <?php if ($post_width != 'half') echo 'checked="checked"'; ?> /></label> <label><?php _e('Half'); ?> <input class="widefat" name="category-posts[<?php echo $number; ?>][post_width]" type="radio" value="half" <?php if ($post_width == 'half') echo 'checked="checked"'; ?> /></label></p>
		<p><?php _e('Show byline:'); ?> <label><?php _e('No'); ?> <input class="widefat" name="category-posts[<?php echo $number; ?>][show_post_meta]" type="radio" value="0" <?php if (!$show_post_meta) echo 'checked="checked"'; ?> /></label> <label><?php _e('Yes'); ?> <input class="widefat" name="category-posts[<?php echo $number; ?>][show_post_meta]" type="radio" value="1" <?php if ($show_post_meta) echo 'checked="checked"'; ?> /></label></p>
<?php
}

/**
 * Add allowed HTML tags to profile text input fields like the user description
 */
function theme_widget_update() {
  global $allowedtags;
  $allowedtags['img'] = array(
    'src' => array(),
    'alt' => array(),
    'title' => array(),
    'width' => array(),
    'height' => array(),
  );
}

function theme_select_categories($args = '') {
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => '',
		'orderby' => 'ID', 'order' => 'ASC',
		'show_last_update' => 0, 'show_count' => 0,
		'hide_empty' => 1, 'child_of' => 0,
		'exclude' => '', 'echo' => 1,
		'selected' => array(), 'hierarchical' => 0,
		'name' => 'cat', 'class' => 'postform',
		'depth' => 0, 'tab_index' => 0
	);

	$r = wp_parse_args( $args, $defaults );
	$r['include_last_update_time'] = $r['show_last_update'];
	extract( $r );

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = ' tabindex="'.$tab_index.'"';

	$categories = get_categories($r);

	$output = '';
	if ( ! empty($categories) ) {
		$output = '<select multiple="multiple" style="height:8em" name="'.$name.'" id="'.$name.'" class="'.$class.'" '.$tab_index_attribute.'>'."\n";

		if ( $show_option_all ) {
			$show_option_all = apply_filters('list_cats', $show_option_all);
			$output .= "\t".'<option value="0">'.$show_option_all.'</option>'."\n";
		}

		if ( $show_option_none) {
			$show_option_none = apply_filters('list_cats', $show_option_none);
			$output .= "\t".'<option value="-1">'.$show_option_none.'</option>'."\n";
		}

		if ( $hierarchical )
			$depth = $r['depth'];  // Walk the full depth.
		else
			$depth = -1; // Flat.

		$output .= walk_category_select_tree($categories, $depth, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_cats', $output);

	if ( $echo )
		echo $output;

	return $output;
}

function walk_category_select_tree() {
	$walker = new Walker_CategorySelect;
	$args = func_get_args();
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

class Walker_CategorySelect extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	function start_el(&$output, $category, $depth, $args) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$cat_name = apply_filters('list_cats', $category->name, $category);
		$output .= "\t<option value=\"".$category->term_id."\"";
		if
		(
		  ( !is_array($args['selected']) && $category->term_id == $args['selected'] ) ||
		  ( is_array($args['selected']) && in_array($category->term_id, $args['selected']))
		)
//		if ( $category->term_id == $args['selected'] )
		{
			$output .= ' selected="selected"';
		}
		$output .= '>';
		$output .= $pad.$cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '&nbsp;&nbsp;' . gmdate($format, $category->last_update_timestamp);
		}
		$output .= "</option>\n";
	}
}

function theme_widget_rss_control($widget_args) {
	global $wp_registered_widgets;
	static $updated = false;

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);

	$options = get_option('widget_bender_rss');
	if ( !is_array($options) )
		$options = array();

	$urls = array();
	foreach ( $options as $option )
		if ( isset($option['url']) )
			$urls[$option['url']] = true;

	if ( !$updated && 'POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			if ( 'theme_widget_rss' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( 'rss-'.$widget_number, $_POST['widget-id'] ) ) // the widget has been removed.
					unset($options[$widget_number]);
			}
		}

		foreach( (array) $_POST['rss'] as $widget_number => $widget_rss ) {
			if ( !isset($widget_rss['url']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
			$widget_rss = stripslashes_deep( $widget_rss );
			$url = sanitize_url(strip_tags($widget_rss['url']));
			$options[$widget_number] = theme_widget_rss_process( $widget_rss, !isset($urls[$url]) );
		}

		update_option('widget_bender_rss', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		$title = '';
		$url = '';
		$items = 10;
		$error = false;
		$number = '%i%';
		$show_summary = 0;
		$show_author = 0;
		$show_date = 0;
		$show_icon = 1;
		$before_content = '';
		$after_content = '';
		$widget_width = 'full';
	} else {
		extract( (array) $options[$number] );
	}

	theme_widget_rss_form( compact( 'number', 'title', 'url', 'items', 'error', 'show_summary', 'show_author', 'show_date', 'show_icon', 'before_content', 'after_content', 'widget_width' ) );
}

function theme_widget_rss_form( $args, $inputs = null ) {
	$default_inputs = array( 'url' => '', 'title' => '', 'items' => 10, 'show_summary' => false, 'show_author' => false, 'show_date' => false, 'show_icon' => true, 'before_content' => '', 'after_content' => '', 'widget_width' => 'full' );
	$inputs = wp_parse_args( $inputs, $default_inputs );
	extract( $args );
	$number = attribute_escape( $number );
	$title  = attribute_escape( $title );
	$url    = attribute_escape( $url );
	$items  = (int) $items;
	if ( $items < 1 || 20 < $items )
		$items  = 10;
	$show_summary   = (int) $show_summary;
	$show_author    = (int) $show_author;
	$show_date      = (int) $show_date;
	$show_icon      = (int) $show_icon;
	$before_content = attribute_escape( $before_content );
	$after_content  = attribute_escape( $after_content );
	$widget_width   = attribute_escape( $widget_width );

?>
	<p>
		<label><?php _e('Enter the RSS feed URL here:'); ?>
			<input class="widefat" name="rss[<?php echo $number; ?>][url]" type="text" value="<?php echo $url; ?>" />
		</label>
	</p>
	<p>
		<label><?php _e('Give the feed a title (optional):'); ?>
			<input class="widefat" name="rss[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
		</label>
	</p>
	<p>
		<label><?php _e('How many items would you like to display?'); ?>
			<select name="rss[<?php echo $number; ?>][items]">
				<?php
					for ( $i = 1; $i <= 20; ++$i )
						echo '<option value="'.$i.'"'. ( $items == $i ? ' selected="selected"' : '' ) . '>'.$i.'</option>';
				?>
			</select>
		</label>
	</p>
	<p>
		<label>
			<input name="rss[<?php echo $number; ?>][show_summary]" type="checkbox" value="1" <?php if ( $show_summary ) echo 'checked="checked" '; ?>/>
			<?php _e('Display item content?'); ?>
		</label>
	</p>
	<p>
		<label>
			<input name="rss[<?php echo $number; ?>][show_author]" type="checkbox" value="1" <?php if ( $show_author ) echo 'checked="checked" '; ?>/>
			<?php _e('Display item author if available?'); ?>
		</label>
	</p>
	<p>
		<label>
			<input name="rss[<?php echo $number; ?>][show_date]" type="checkbox" value="1" <?php if ( $show_date ) echo 'checked="checked" '; ?>/>
			<?php _e('Display item date?'); ?>
		</label>
	</p>
	<p>
		<label>
			<input name="rss[<?php echo $number; ?>][show_icon]" type="checkbox" value="1" <?php if ( $show_icon ) echo 'checked="checked" '; ?>/>
			<?php _e('Display feed icon/link?'); ?>
		</label>
	</p>
	<p>
		<label><?php _e('Introductory text (optional):'); ?>
			<input class="widefat" name="rss[<?php echo $number; ?>][before_content]" type="text" value="<?php echo $before_content; ?>" />
		</label>
	</p>
	<p>
		<label><?php _e('Closing text (optional):'); ?>
			<input class="widefat" name="rss[<?php echo $number; ?>][after_content]" type="text" value="<?php echo $after_content; ?>" />
		</label>
	</p>
	<p><?php _e('Widget width:'); ?>
		<label><?php _e('Full'); ?>
			<input name="rss[<?php echo $number; ?>][widget_width]" type="radio" value="full" <?php if ( $widget_width == 'full' ) echo 'checked="checked" '; ?>/>
		</label>
		<label><?php _e('Half'); ?>
			<input name="rss[<?php echo $number; ?>][widget_width]" type="radio" value="half" <?php if ( $widget_width == 'half' ) echo 'checked="checked" '; ?>/>
		</label>
	</p>

	<input type="hidden" name="rss[<?php echo $number; ?>][submit]" value="1" />
<?php
	foreach ( array_keys($default_inputs) as $input ) :
		if ( 'hidden' === $inputs[$input] ) :
			$id = str_replace( '_', '-', $input );
?>
	<input type="hidden" name="rss[<?php echo $number; ?>][<?php echo $input; ?>]" value="<?php echo $input; ?>" />
<?php
		endif;
	endforeach;
}

// Expects unescaped data
function theme_widget_rss_process( $widget_rss, $check_feed = true ) {
	$items = (int) $widget_rss['items'];
	if ( $items < 1 || 20 < $items )
		$items = 10;
	$url           = sanitize_url(strip_tags( $widget_rss['url'] ));
	$title         = trim(strip_tags( $widget_rss['title'] ));
	$show_summary  = (int) $widget_rss['show_summary'];
	$show_author   = (int) $widget_rss['show_author'];
	$show_date     = (int) $widget_rss['show_date'];
	$show_icon     = (int) $widget_rss['show_icon'];
	$before_content = $widget_rss['before_content'];
	$after_content = $widget_rss['after_content'];
	$widget_width = ($widget_rss['widget_width'] == 'half') ? 'half' : 'full';

	if ( $check_feed ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		$rss = fetch_rss($url);
		$error = false;
		$link = '';
		if ( !is_object($rss) ) {
			$url = wp_specialchars(__('Error: could not find an RSS or ATOM feed at that URL.'), 1);
			$error = sprintf(__('Error in RSS %1$d'), $widget_number );
		} else {
			$link = clean_url(strip_tags($rss->channel['link']));
			while ( strstr($link, 'http') != $link )
				$link = substr($link, 1);
		}
	}

	return compact( 'title', 'url', 'link', 'items', 'error', 'show_summary', 'show_author', 'show_date', 'show_icon', 'before_content', 'after_content', 'widget_width' );
}

function theme_widget_recent_comments_control() {
  $options = $newoptions = get_option('widget_recent_comments');
  if ( $_POST['recent-comments-submit'] ) {
    $newoptions['title'] = strip_tags(stripslashes($_POST['recent-comments-title']));
    $newoptions['number'] = (int) $_POST['recent-comments-number'];
    $newoptions['before_content'] = stripslashes($_POST['recent-comments-before-content']);
    $newoptions['after_content'] = stripslashes($_POST['recent-comments-after-content']);
    if (!current_user_can('unfiltered_html')) {
      $newoptions['before_content'] = stripslashes(wp_filter_post_kses($newoptions['recent-comments-before-content']));
      $newoptions['after_content'] = stripslashes(wp_filter_post_kses($newoptions['recent-comments-after-content']));
    }
  }

  if ( $options != $newoptions ) {
    $options = $newoptions;
    update_option('widget_recent_comments', $options);
    wp_delete_recent_comments_cache();
  }
  $title = attribute_escape($options['title']);
  $before_content = attribute_escape($options['before_content']);
  $after_content = attribute_escape($options['after_content']);
  if ( !$number = (int) $options['number'] ) {
    $number = 5;
  }
?>
			<p><label><?php _e('Title:'); ?> <input class="widefat" name="recent-comments-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><label><?php _e('Number of comments to show:'); ?> <input style="width: 25px; text-align: center;" name="recent-comments-number" type="text" value="<?php echo $number; ?>" /></label>
				<br />
				<small><?php _e('(at most 15)'); ?></small>
			</p>
			<p><label><?php _e('Introductory text (optional):'); ?> <input class="widefat" name="recent-comments-before-content" type="text" value="<?php echo $before_content; ?>" /></label></p>
			<p><label><?php _e('Closing text (optional):'); ?> <input class="widefat" name="recent-comments-after-content" type="text" value="<?php echo $after_content; ?>" /></label></p>
			<input type="hidden" id="recent-comments-submit" name="recent-comments-submit" value="1" />
<?php
}

function theme_widget_site_digg_control() {
  $options = $newoptions = get_option('widget_site_digg');
  if ( $_POST['site-digg-submit'] ) {
    $newoptions['title'] = strip_tags(stripslashes($_POST['site-digg-title']));
  }

  if ( $options != $newoptions ) {
    $options = $newoptions;
    update_option('widget_site_digg', $options);
  }
  $title = attribute_escape($options['title']);
?>
			<p><label><?php _e('Title:'); ?> <input class="widefat" name="site-digg-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="site-digg-submit" name="site-digg-submit" value="1" />
<?php
}

?>