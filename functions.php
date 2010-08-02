<?php

/**
 * @package WordPress
 * @subpackage Bender_theme
 */

if (!function_exists('get_current_theme')) die();

if ('Bender' == get_current_theme()) {

  add_action('init', 'theme_init');
  if (!is_admin()) {
    add_action('wp_head', 'theme_head', 1);
    add_action('wp_head', 'theme_head_end', 20);
    add_action('wp_footer', 'theme_footer');
    add_action('theme_page_top', 'theme_page_top');
    add_action('theme_single_top', 'theme_visitor_announcement');
    add_action('theme_single_top', 'theme_refresh_link');
    add_action('theme_index_after_post_content', 'theme_index_after_post_content');
    add_action('theme_single_after_post', 'theme_single_after_post');
    add_action('theme_single_after_post_content', 'theme_related_posts', 10);
    add_action('theme_single_after_post_content', 'theme_content_add_comment', 14);
    // add_action('theme_single_after_post_content', 'theme_content_recommend', 15);
    add_action('theme_single_after_post_content', 'theme_content_post_actions', 20);
    add_action('google_search_page_form_pre', 'theme_google_search_pre');
    add_action('google_search_page_form_post', 'theme_google_search_post');
    if (function_exists('google_analytics'))
      remove_action('wp_footer', 'google_analytics');
    add_filter('the_content', 'theme_category_links_filter', 1);
    add_filter('the_category', 'theme_the_category_filter', 20, 3);
  } else {
    add_action('edit_post', 'theme_delete_category_posts_cache'); // clear widget cache on post edit
    if (current_user_can('edit_themes')) {
      require_once('admin_functions.php');
    }
  }

  if (function_exists('register_sidebar')) {
    register_sidebar(array(
      'name' => 'Right sidebar',
      'before_widget' => '<li id="%1$s" class="widget %2$s">',
      'after_widget' => '</li>',
      'before_title' => '<h2><span class="title-sleeve">',
      'after_title' => '</span></h2>',
    ));
    register_sidebar(array(
      'name' => 'Left content',
  	  'before_widget' => '<div id="%1$s" class="widget %2$s">',
 	  'after_widget' => '<div class="clear"></div></div>',
  	  'before_title' => '<h2><span class="title-sleeve">',
  	  'after_title' => '</span></h2>',
    ));
    add_action('widgets_init', 'theme_widgets_init');
    add_action('widgets_init', 'theme_widget_category_posts_register');
    add_action('widgets_init', 'theme_widget_rss_register');
    add_action('widgets_init', 'theme_widget_recent_comments_register');
    add_action('widgets_init', 'theme_widget_site_digg_register');
  }

}

function theme_init() {
//  wp_enqueue_script('ui.tabs', '/wp-includes/js/jquery/ui.tabs.js', array('jquery'), '3');
  if (file_exists(TEMPLATEPATH .'/styles/'. get_option('bender_substyle') .'/functions.php'))
    include(TEMPLATEPATH .'/styles/'. get_option('bender_substyle') .'/functions.php');
}

/**
 * Initialize theme widgets
 */
function theme_widgets_init() {
  // Replace built-in search widget with contents of searchform.php
  unregister_sidebar_widget('search');
  $widget_ops = array('classname' => 'widget_search', 'description' => __('A search form for your blog'));
  wp_register_sidebar_widget('search', __('Search'), 'theme_widget_search', $widget_ops);
}

function theme_head() {
  theme_substyle_stylesheet();
  theme_meta_tags();
  echo '<link rel="stylesheet" type="text/css" media="all" href="http://digg.com/css/widget.css" />';
  if (is_single() && is_array(get_post_meta($GLOBALS['post']->ID, 'live-blogging')) && isset($_GET['refresh'])) {
    echo '<meta http-equiv="refresh" content="30" />'."\n";
  }
}

function theme_head_end() {
  echo '<link rel="stylesheet" href="'. get_bloginfo('stylesheet_directory') .'/jtabs.css" type="text/css" media="screen, tv, projection" />'."\n";
}

function theme_digg_network_posts($endpoint) {
  $file = dirname(__FILE__) .'/diggdata.txt';
  $fh = fopen($file, 'r');
  $s = fread($fh, filesize($file));
  fclose($fh);

  if ($s) {
    $data = unserialize($s);
    if (isset($data[$endpoint])) {
      $posts = array_slice($data[$endpoint], 0, 10, true);
      return $posts;
    }
  }
}

function theme_digg_tabs() {
  $popular_posts = theme_digg_network_posts('stories/popular');
  $upcoming_posts = theme_digg_network_posts('stories/upcoming');
  if ($popular_posts || $upcoming_posts) {
?>
	<ul>
		<li><a href="#upcoming-diggs">Upcoming</a></li>
		<li><a href="#popular-diggs">Popular</a></li>
	</ul>
	<div id="upcoming-diggs">
		<div class="digg-widget digg-widget-theme1">
			<ul class="external">
<?php foreach ($upcoming_posts as $post): ?>
				<li><a href="<?php echo $post['href']; ?>?OTC-go"class="digg-count"><?php echo $post['diggs']; ?> <span>diggs</span></a>
				<h3><a href="<?php echo $post['href']; ?>?OTC-go"><?php echo $post['title']; ?></a></h3></li>
<?php endforeach; ?>
			</ul>
			<div class="digg-widget-footer"><a href="http://digg.com/?OTC-go">Powered by Digg's Users</a></div>
		</div>
	</div>
	<div id="popular-diggs">
		<div class="digg-widget digg-widget-theme1">
			<ul class="external">
<?php foreach ($popular_posts as $post): ?>
				<li><a href="<?php echo $post['href']; ?>?OTC-go"class="digg-count"><?php echo $post['diggs']; ?> <span>diggs</span></a>
				<h3><a href="<?php echo $post['href']; ?>?OTC-go"><?php echo $post['title']; ?></a></h3></li>
<?php endforeach; ?>
			</ul>
			<div class="digg-widget-footer"><a href="http://digg.com/?OTC-go">Powered by Digg's Users</a></div>
		</div>
	</div>
	<script type="text/javascript">jQuery("#tab-container-1 > ul").tabs();</script>
<?php
  }
}

function theme_footer() {
  if (PRODUCTION) {
    theme_google_analytics();
?>

<script type="text/javascript" src="http://edge.quantserve.com/quant.js"></script>
<script type="text/javascript"><!--
_qacct="p-fdUTY5Z2fTrIw";try{quantserve();}catch(e){}
//--></script>
<noscript><img src="http://pixel.quantserve.com/pixel/p-fdUTY5Z2fTrIw.gif" style="display:none;" height="1" width="1" alt="Quantcast"/></noscript>

<div id="comscore-tag">
<!-- Begin comScore Tag -->
<script>
    document.write(unescape("%3Cscript src='" + (document.location.protocol == "https:" ? "https://sb" : "http://b") + ".scorecardresearch.com/beacon.js' %3E%3C/script%3E"));
</script>

<script>
  COMSCORE.beacon({
    c1:2,
    c2:7521787,
    c3:"",
    c4:"",
    c5:"",
    c6:"",
    c15:""
  });
</script>
<noscript>
  <img src="http://b.scorecardresearch.com/p?c1=2&c2=7521787&c3=&c4=&c5=&c6=&c15=&cj=1" />
</noscript>
<!-- End comScore Tag -->
</div>

<?php
  }
}

function theme_digg_related_widget() {
  $domain = $_SERVER['SERVER_NAME'];
?>
<script type="text/javascript" src="<?php echo get_bloginfo('stylesheet_directory'); ?>/digg.js?ver=4"></script>
<script type="text/javascript"><!--
if (document.referrer.indexOf("http://digg.com/") === 0) {
	jQuery('<div id="digg-related"></div>').insertBefore('.post > .entry');
	digg_related({domain:"<?php echo $domain; ?>",container:"#digg-related",width:"",height:"",endPoint:"stories/upcoming"});
}
//--></script>
<?php
}

/**
 * Return searchform.php as a widget
 *
 * @param array $args
 */
function theme_widget_search($args) {
  echo $args['before_widget'];
  include 'searchform.php';
  echo $args['after_widget'];
}

function theme_substyle_stylesheet() {
  $style = get_option('bender_substyle', 'plain');
  if (file_exists(TEMPLATEPATH .'/styles/'. $style .'/style.css')) {
    $url = get_bloginfo('stylesheet_directory') .'/styles/'. $style .'/style.css?4';
    echo '<link rel="stylesheet" href="'. $url .'" type="text/css" media="screen" charset="utf-8" />'."\n";
  }
}

function theme_meta_tags() {
  if (is_single()) {
    global $post;
    $values = get_post_custom_values('meta_description', $post->ID);
    if (is_array($values)) {
      echo '<meta name="description" content="'. $values[0] .'" />'."\n";
    }
  } elseif (is_home()) {
    $value = get_option('blog_extended_description');
    if ($value) {
      echo '<meta name="description" content="'. $value .'" />'."\n";
    }
  }
}

function theme_about() {
  $s = get_option('blog_about');
  echo $s;
}

function theme_masthead() {
  $masthead_file = 'masthead.png';
  $masthead_file = apply_filters('theme_masthead_filename', $masthead_file);
  if (file_exists(TEMPLATEPATH .'/styles/'. get_option('bender_substyle') .'/'. $masthead_file)) {
    echo '<img id="masthead" src="'. get_bloginfo('stylesheet_directory') .'/styles/'. get_option('bender_substyle') .'/'. $masthead_file .'" alt="'. get_bloginfo('name') .'" />';
  } else {
    bloginfo('name');
  }
}

function theme_page_top() {
  echo '<div id="top_ad">';
  theme_adspot('728x90', '1');
  echo '</div>';
}

function theme_pagetitle() {
  if (function_exists('go_title'))
    go_title();
  else {
    bloginfo('name');
    if (is_single()) {
      echo ' &raquo; Blog Archive ';
    }
    wp_title();
  }
}

function theme_body_attrs() {
  global $body_class;

  $attrs = '';
  if (is_home())
    $body_class[] = 'home';
  elseif (is_archive())
    $body_class[] = 'archive';
  elseif (is_single())
    $body_class[] = 'single';
  elseif (is_page())
    $body_class[] = 'page';

  if (is_array($body_class)) {
    $attrs = ' class="'. implode(' ', $body_class) . '"';
  }
  echo $attrs;
}

function theme_blogmenu() {
  if (file_exists(TEMPLATEPATH .'/styles/'. get_option('bender_substyle') .'/menu.php'))
    include TEMPLATEPATH .'/styles/'. get_option('bender_substyle') .'/menu.php';
}

function theme_content_add_comment() {
  if ('open' == $GLOBALS['post']->comment_status) {
  	echo '<div class="post-comment"><a href="#respond">Add a comment or question</a></div>';
  }
}

function theme_content_recommend() {
  if (function_exists(postvotes_ui)) {
    echo '<div class="recommendations">';
    postvotes_ui();
    echo '<div class="clear"></div></div>';
  }
}

function theme_content_post_actions() {
?>
					<div class="postactions">
					<div class="navigation">
						<div class="alignleft"><?php previous_post_link('&laquo; %link', 'Previous post') ?></div>
						<div class="alignright"><?php next_post_link('%link &raquo;', 'Next post') ?></div>
					</div>
				</div>
<?php
}

function theme_index_after_post_content() {
?>
			<div class="postactions">
				<div class="navigation">
					<div class="alignright comment"><a href="<?php the_permalink() ?>#respond">Add a comment or question</a></div>
				</div>
			</div>
<?php
}

function theme_single_after_post() {
  theme_adspot('300x250', '2');
}

function theme_post_author() {
  the_author();
}

function recent_comments() {
  $args = array(
    'before_title' => '<h2>',
    'after_title' => '</h2>',
  );
  //wp_widget_recent_comments($args);
}

function commentor_is_post_author() {
  global $post, $comment;
  if ($post->post_author == $comment->user_id) return true;
}

function has_child_blog() {
  $id = get_option('child_blog_id');
  if ($id === false) {
    add_option('child_blog_id', '');
    return false;
  } else {
    return ($id > 0);
  }
}

function child_blog_title() {
  $id = get_option('child_blog_id');
  if ($id) {
    echo '<a href="'.get_blog_option($id, 'siteurl').'">'. get_blog_option($id, 'blogname') .'</a>';
  }
}

function child_blog_posts() {
  $id = get_option('child_blog_id');
  if ($id) {
    blog_post_titles($id);
  }
}

function blog_post_titles($id, $limit = 10) {
  global $wpdb, $post;

  $current_post = $post->ID;
  switch_to_blog($id);
  $posts = $wpdb->get_results('SELECT * FROM '.$wpdb->posts.' WHERE post_type="post" AND post_status="publish" ORDER BY post_date_gmt DESC LIMIT '.$limit, OBJECT);
  if (!empty($posts)) {
    print '<ul>';
    foreach ($posts as $post) {
      ?><li><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title() ?></a></li><?php
    }
    print '</ul>';
  }
  restore_current_blog();
  $post = get_post($current_post);
}

function theme_get_substyles() {
  $styles = array();

  // Directories in ./styles
  $styles_dir_fs = get_template_directory() .'/styles';
  $fp = @ opendir($styles_dir_fs);
  if (!$fp)
    return $styles;

  while (($style_dir = readdir($fp)) !== false) {
    $style_dir_fs = $styles_dir_fs . '/' . $style_dir;
    if (is_dir($style_dir_fs) && is_readable($style_dir_fs)) {
      if ($style_dir{0} == '.' || $style_dir == '..')
        continue;
      $found_stylesheet = false;
      $fp_style_dir = @ opendir($style_dir_fs);
      while (($file = readdir($fp_style_dir)) !== false ) {
        if ($file == 'style.css') {
          $styles[] = $style_dir;
          $found_stylesheet = true;
          break;
        }
      }
    }
  }
  return $styles;
}

function theme_author_avatar($size=32) {
  echo get_avatar($GLOBALS['post']->post_author, $size);
}

function theme_blog_twitter_url() {
  $twitter_url = get_option('twitter_url');
  if ($twitter_url) {
    echo $twitter_url;
  } else {
    $domain = strtolower($_SERVER['SERVER_NAME']);
    preg_match('/^(.*)\.[^\.]+$/', $domain, $matches);
    if (isset($matches[1])) {
      $name = $matches[1];
    }
    echo 'http://twitter.com/'. $name;
  }
}

// Display Google Analytics Tracker script.
function theme_google_analytics() {
  if (is_preview()) return;

  $code = get_option('google_analytics_code');
  if ($code) {
    $subdomain = '';
    $virtual_path = '';
    if (defined('VHOST') && constant('VHOST') == 'yes') {
      // Check if blog is a subdomain
      global $current_site;
      $path = $_SERVER['REQUEST_URI'];
      $site_domain = $current_site->domain;
      $subdomain = '';
      preg_match('@https?://([^/]+)@i', get_option('siteurl'), $matches);
      if (is_array($matches)) {
        $current_domain = $matches[1];
        // Check if blog is subdomain of the site domain
        $pos = strpos($current_domain, $site_domain);
        if ($pos) {
          $subdomain = substr($current_domain, 0, $pos-1);
        } else {
          // Check if blog is a subdomain of another domain
          preg_match('/^(.*?)\.([^.]+\.[^.]+)$/', $current_domain, $matches);
          if (is_array($matches)) {
            $subdomain = $matches[1];
            $site_domain = $matches[2];
          }
        }
        if ($subdomain && $subdomain != 'www') {
          $virtual_path = '"/'.$subdomain.$path.'"';
        }
      }
    }

    // Display gat script
?>
<script type="text/javascript" src="http://www.google-analytics.com/ga.js"></script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?php echo $code; ?>");
pageTracker._initData();
pageTracker._trackPageview(<?php echo $virtual_path; ?>);
<?php if ($subdomain) echo 'pageTracker._setDomainName("'. $site_domain .'");'."\n"; ?>
} catch(err) {}
</script>
<?php
  }
}

function theme_is_cross_post() {
  if (function_exists('cross_post_is_external'))
    return cross_post_is_external($GLOBALS['post']);
  else
    return false;
}

function theme_adspot_attributes() {
  $attrs = array();

  // Page specific attributes from custom fields
  if (is_single()) {
    global $post;
    $custom_fields = get_post_custom($post->ID);
    if (!empty($custom_fields)) {
      foreach ($custom_fields as $name => $values) {
        if (strpos($name, 'gam_') === 0) {
          $key = substr($name, 4);
          foreach ($values as $value) {
            $attrs[$key][] = $value;
          }
        }
      }
    }
  }
  $attrs = apply_filters('theme_adspot_attributes', $attrs);
  return $attrs;
}

function theme_adspots() {
  static $ids = null;
  if (is_array($ids)) return $ids;

  $location_name_map = array(
    array('location' => 'T', 'dimensions' => '728x90'),
    array('location' => 'TR', 'dimensions' => '300x250'),
    array('location' => 'R', 'dimensions' => '160x600'),
    array('location' => 'BR', 'dimensions' => '160x600'),
    array('location' => 'R4', 'dimensions' => '160x600'),
    array('location' => 'B', 'dimensions' => '728x90'),
    array('location' => 'R', 'dimensions' => '125x125'),
  );
  if (is_single()) {
    $location_name_map[] = array('location' => 'C', 'dimensions' => '300x250');
  } elseif (is_front_page() || is_archive()) {
    $location_name_map[] = array('location' => 'C', 'dimensions' => '468x60');
  }

  // Allow substyles to override adspots
  if (function_exists('style_adspots')) {
    $location_name_map = style_adspots($location_name_map);
  }

  $domain = strtolower($_SERVER['SERVER_NAME']);
  preg_match('/^(.*)\.[^\.]+$/', $domain, $matches);
  if (isset($matches[1])) {
    $domain = str_replace('.', '_', $matches[1]);
  }

  foreach ($location_name_map as $map) {
    $ids[] = array(
      'domain' => $domain,
      'name' => $map['dimensions'],
      'location' => $map['location'],
    );
  }

  return $ids;
}

function theme_adspot($name, $location=null) {
  if (function_exists('wpads')) {
    $zone = $name . (($location) ? '-'. $location : '');
    $ad = get_wpads($zone);
    if ($ad) {
      echo '<div class="ad ad-'. $name .'">'. $ad ."</div>\n";
    }
  }
}

function theme_the_content() {
  global $post, $current_blog;

  if (isset($post->blog_id) && $post->blog_id != $current_blog->blog_id) {
    $blog_details = get_blog_details($post->blog_id);
    the_content('Read the rest of this entry on '. $blog_details->domain .' &raquo;');
  } else {
    the_content('Read the rest of this entry &raquo;');
  }
}

function theme_post_thumbnail($args='', $echo=true) {
  global $post;

  $output = '';
  if (is_object($post) && $post->ID) {
    $values = get_post_custom_values('post_thumbnail', $post->ID);
    if (is_array($values)) {
      $img = '<img class="thumbnail" src="'. $values[0] .'" alt="" />';
    } else {
      $attachments = get_children('post_parent='. $post->ID .'&post_type=attachment&post_mime_type=image&orderby=menu_order ASC, ID ASC');
      if ($attachments) {
        foreach ($attachments as $id => $attachment) {
          @list($src, $width, $height) = wp_get_attachment_image_src($id);
          $caption = trim($attachment->post_excerpt);
          break;
        }
        $img = '<img class="thumbnail" src="'. $src .'" width="'. $width .'" height="'. $height .'" alt="'. $caption .'" />';
      }
    }
  }

  if ($img) {
    if (isset($args['before']))
      $output = $args['before'];
    if (isset($args['link']) && $args['link'] == false)
      $output .= $img;
    else
      $output .= '<a href="'. get_permalink() .'">'. $img ."</a>\n";
    if (isset($args['after']))
      $output .= $args['after'];
  }

  if ($echo)
    echo $output;
  else
    return $output;
}

function theme_visitor_announcement() {
?>
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/resources/jquery.cookie.pack.js"></script>
<script type="text/javascript">//<![CDATA[
jQuery(document).ready(function(){
	view_thresh_hold = 3;
	views = parseInt(jQuery.cookie('views'));
	user = jQuery.cookie('wordpressuser');
	views = (isNaN(views)) ? 1 : views+1;
	jQuery.cookie('views', views, { expires: 365, path: '/' });
	if (views <= view_thresh_hold && !user)
		jQuery('.visitor-message').show();
});
//]]></script>
<p class="visitor-message" style="display:none">Like this post? <a href="/feed/">Subscribe to our RSS feed</a> and stay up to date.</p>
<?php
}

function theme_google_search_pre() {
  echo '<div id="content"><div class="sleeve">';
}

function theme_google_search_post() {
  echo '</div></div>';
}

/*
 * Excerpt functions borrowed and adapted from The-Excerpt-Reloaded
 */

function theme_excerpt($args='') {
    echo get_theme_excerpt($args);
}

function get_theme_excerpt($args='') {
  global $post;

  /*
   * Use same default behavior as the_excerpt()
   */
  $defaults = array(
    'excerpt_length' => 120, // length of excerpt in words. -1 to display all excerpt/content
    'allowed_tags' => 'all', // HTML tags allowed in excerpt, 'all' to allow all tags.
    'filter_type' => 'excerpt', // format filter used => 'the_content', 'the_excerpt', 'the_content_rss', 'the_excerpt_rss', 'none'
    'use_more_link' => true,
    'more_link_text' => 'Read the rest of this entry &raquo;',
    'force_anchor' => true,
    'force_link' => false,
    'use_content' => 'no_excerpt', // Use content instead of excerpt: 'always', 'never', 'no_excerpt'
    'fix_tags' => true,
    'no_more' => false,
    'more_tag' => 'div',
    'show_dots' => true,
  );
  $defaults = apply_filters('theme_excerpt_defaults', $defaults);
  $args = wp_parse_args($args, $defaults);
  extract($args, EXTR_SKIP);

  if (!empty($post->post_password)) { // if there's a password
    if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) { // and it doesn't match cookie
      if(is_feed()) { // if this runs in a feed
        $output = __('This is a protected post.');
      } else {
        $output = get_the_password_form();
      }
    }
    return $output;
  }

  if ($use_content == 'always') {
    $text = $post->post_content;
  } elseif($use_content == 'never') {
    $text = $post->post_excerpt;
  } else { // excerpt no matter what
    $text = (empty($post->post_excerpt)) ? $post->post_content : $post->post_excerpt;
  }

  if ($excerpt_length < 0) {
    $output = $text;
  } else {
    if (!$no_more && strpos($text, '<!--more-->')) {
      $text = explode('<!--more-->', $text, 2);
      $l = count($text[0]);
      $more_link = 1;
    } else {
      $text = explode(' ', $text);
      if(count($text) > $excerpt_length) {
        $l = $excerpt_length;
        $ellipsis = 1;
      } elseif (!$force_link) {
        $l = count($text);
        $more_link_text = '';
        $ellipsis = 0;
      }
    }
    for ($i=0; $i<$l; $i++)
      $output .= $text[$i] . ' ';
  }

  if('all' != $allowedtags) {
    $output = strip_tags($output, $allowedtags);
  }

//    $output = str_replace(array("\r\n", "\r", "\n", "  "), " ", $output);

  $output = rtrim($output, "\s\n\t\r\0\x0B");
  $output = ($fix_tags) ? $output : balanceTags($output, true);
  $output .= ($showdots && $ellipsis) ? '[...]' : '';

  if ($use_more_link && $more_link_text) {
    if($force_anchor) {
      $output .= ' <'. $more_tag .' class="more-link"><a href="'. get_permalink($post->ID) .'#more-'. $post->ID .'">'. $more_link_text .'</a></'. $more_tag .'>'."\n";
    } else {
      $output .= ' <'. $more_tag .' class="more-link"><a href="'. get_permalink($post->ID) .'">'. $more_link_text .'</a></'. $more_tag .'>'."\n";
    }
  }

  $output = apply_filters('the_'.$filter_type, $output);

  return $output;
}





/* *******************
 * WIDGETS
 ****************** */


function theme_widget_category_posts($args, $widget_args = 1) {
  extract($args, EXTR_SKIP);
  if (is_numeric($widget_args))
    $widget_args = array('number' => $widget_args);
  $widget_args = wp_parse_args($widget_args, array('number' => -1));
  extract($widget_args, EXTR_SKIP);

  // Data should be stored as array:  array( number => data for that instance of the widget, ... )
  $options = get_option('widget_bender_category_posts');
  if (!isset($options[$number]) || !is_array($options[$number]['cat']))
    return;
  $options = $options[$number];

  $cat = get_category($options['cat'][0]);
  $cat_link = get_category_link($cat->term_id);
  $widget_more_label = (!empty($options['more_label'])) ? $options['more_label'] : __('More').' &raquo;';
  $post_more_label = (!empty($options['post_more_label'])) ? $options['post_more_label'] : __('Read this post').' &raquo;';
  $title = (!empty($options['title'])) ? $options['title'] : wptexturize($cat->name);

  $vars = array('/%slug%/', '/%width%/');
  $vals = array($cat->slug, $options['post_width']);
  $before_widget = preg_replace($vars, $vals, $before_widget);

  ob_start();
  echo $before_widget . $before_title . $title . $after_title;

  $finished = false;
  $do_sticky_posts = ($options['sticky_cat'] > 0 && $options['sticky_posts'] > 0);
  $sticky_loop_done = false;
  $sticky_posts = array();
  $count = 0;
  while (!$finished) {
    $sticky_class = '';
    $in_sticky_loop = false;
    if ($do_sticky_posts && !$sticky_loop_done) {
      $in_sticky_loop = true;
      // Add the sticky categories to category__and
      $query['showposts'] = $options['sticky_posts'];
      $query['category__and'] = $options['cat'];
      $query['category__and'][] = $options['sticky_cat'];
      if (is_array($options['category__not_in']))
        $query['category__not_in'] = $options['category__not_in'];
      $sticky_class = ' sticky';
    } else {
      $finished = true;
      //$query['showposts'] = $options['showposts'] - $options['sticky_posts'];
      $query['showposts'] = $options['showposts'];
      if (is_array($options['cat']))
        $query['category__and'] = $options['cat'];
      $query['category__not_in'] = $options['category__not_in'];
      // Add the sticky category to category__not_in
      //$query['category__not_in'][] = $options['sticky_cat'];
      if ($do_sticky_posts && $count)
        $sticky_class = ' unsticky';
    }

    $q = new WP_Query($query);
    if ($q->have_posts()) {
      while ($q->have_posts()) {

        // Count posts displayed and break if over showposts
        if ($count > $options['showposts']) {
          $finished = true;
          break;
        }
        $count++;

        $q->the_post();

        // Track and skip sticky posts
        if ($in_sticky_loop) {
          $sticky_posts[] = $GLOBALS['id'];
        } elseif ($do_sticky_posts && in_array($GLOBALS['id'], $sticky_posts)) {
          continue;
        }
?>
		<div class="post<?php echo $sticky_class; ?>" id="post-<?php the_ID(); ?>">
			<?php theme_post_thumbnail(array('before'=>'<div class="post-thumbnail">','after'=>'</div>')); ?>
			<h3><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			<?php if ($options['show_post_meta']): ?>
			<div class="postinfo">
				<div class="byline">
					<span class="author">Written by <?php the_author_archive_link(); ?></span>
				</div>
				<div class="date"><span class="verb">Published</span> on <?php the_time('F jS, Y'); ?></div>
			</div>
			<?php endif; ?>
			<div class="entry">
				<?php theme_excerpt(array('more_link_text' => $post_more_label, 'excerpt_length' => -1)); ?>
			</div>
			<div class="clear"></div>
		</div>
<?php
      }
    }
    if ($do_sticky_posts)
      $sticky_loop_done = true;
}

  //echo $after_widget;
  echo '<div class="widget-more-link"><a href="'. $cat_link .'" rel="category">'. $widget_more_label .'</a></div>';
  echo '</div>';
  wp_cache_add('widget_bender_category_posts', ob_get_flush());
}

/**
 * Register each instance of category posts widgets
 */
function theme_widget_category_posts_register() {
  if (!$options = get_option('widget_bender_category_posts'))
    $options = array();
  if (!is_array($options))
    $options = array($options);

  $id_base = 'category-posts';
  $widget_ops = array('classname' => 'category-posts %slug% %width%', 'description' => __('List of posts with a single category'));
  $control_ops = array('id_base' => $id_base);
  $name = __('Category Posts');

  $registered = false;
  foreach (array_keys($options) as $o) {

    $id = $id_base .'-'. $o;
    wp_register_sidebar_widget($id, $name, 'theme_widget_category_posts', $widget_ops, array('number' => $o));
    wp_register_widget_control($id, $name, 'theme_widget_category_posts_control', $control_ops, array('number' => $o));
    $registered = true;
  }
  //global $wp_registered_widgets;
  //print_r($wp_registered_widgets);

  // If there are none, we register the widget's existance with a generic template
  if (!$registered) {
    wp_register_sidebar_widget($id_base .'-1', $name, 'theme_widget_category_posts', $widget_ops, array('number' => -1));
    wp_register_widget_control($id_base .'-1', $name, 'theme_widget_category_posts_control', $control_ops, array('number' => -1));
  }
}

function theme_delete_category_posts_cache() {
  wp_cache_delete('widget_bender_category_posts', 'widget');
}

function theme_widget_rss($args, $widget_args = 1) {
	extract($args, EXTR_SKIP);
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widegt_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);

	$options = get_option('widget_bender_rss');

	if ( !isset($options[$number]) )
		return;

	if ( isset($options[$number]['error']) && $options[$number]['error'] )
		return;

	$url = $options[$number]['url'];
	while ( strstr($url, 'http') != $url )
		$url = substr($url, 1);
	if ( empty($url) )
		return;

	require_once(ABSPATH . WPINC . '/rss.php');

	$rss = fetch_rss($url);
	$link = clean_url(strip_tags($rss->channel['link']));
	while ( strstr($link, 'http') != $link )
		$link = substr($link, 1);
	$desc = attribute_escape(strip_tags(html_entity_decode($rss->channel['description'], ENT_QUOTES)));
	$title = $options[$number]['title'];
	if ( empty($title) )
		$title = htmlentities(strip_tags($rss->channel['title']));
	if ( empty($title) )
		$title = $desc;
	if ( empty($title) )
		$title = __('Unknown Feed');

	$feed_link = '';
	if ($options[$number]['show_icon']) {
	  $url = clean_url(strip_tags($url));
	  if ( file_exists(dirname(__FILE__) . '/rss.png') )
		$icon = str_replace(ABSPATH, get_option('siteurl').'/', dirname(__FILE__)) . '/rss.png';
	  else
		$icon = get_option('siteurl').'/wp-includes/images/rss.png';
      $feed_link = '<a class="rsswidget" href="'.$url.'" title="' . attribute_escape(__('Syndicate this content')) .'"><img width="14" height="14" src="'.$icon.'" alt="RSS" /></a> ';
	}
	$title = $feed_link.$title;

	$vars = array('/%width%/');
    $vals = array($options[$number]['widget_width']);
    $before_widget = preg_replace($vars, $vals, $before_widget);

    if (isset($options[$number]['before_content'])) {
      $after_title .= '<p class="before_content">'. $options[$number]['before_content'] .'</p>';
    }
    if (isset($options[$number]['after_content'])) {
      $after_widget = '<p class="after_content">'. $options[$number]['after_content'] .'</p>'. $after_widget;
    }
    echo $before_widget;
    echo $before_title . $title . $after_title;

	theme_widget_rss_output( $rss, $options[$number] );

	echo $after_widget;
}

function theme_widget_rss_output( $rss, $args = array() ) {
	if ( is_string( $rss ) ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		if ( !$rss = fetch_rss($rss) )
			return;
	} elseif ( is_array($rss) && isset($rss['url']) ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		$args = $rss;
		if ( !$rss = fetch_rss($rss['url']) )
			return;
	} elseif ( !is_object($rss) ) {
		return;
	}

	extract( $args, EXTR_SKIP );

	$items = (int) $items;
	if ( $items < 1 || 20 < $items )
		$items = 10;
	$show_summary  = (int) $show_summary;
	$show_author   = (int) $show_author;
	$show_date     = (int) $show_date;

	if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
		$rss->items = array_slice($rss->items, 0, $items);
		echo '<ul>';
		foreach ($rss->items as $item ) {
			while ( strstr($item['link'], 'http') != $item['link'] )
				$item['link'] = substr($item['link'], 1);
			$link = clean_url(strip_tags($item['link']));
			$title = attribute_escape(strip_tags($item['title']));
			if ( empty($title) )
				$title = __('Untitled');
			$desc = '';
				if ( isset( $item['description'] ) && is_string( $item['description'] ) )
					$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['description'], ENT_QUOTES))));
				elseif ( isset( $item['summary'] ) && is_string( $item['summary'] ) )
					$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['summary'], ENT_QUOTES))));

			$summary = '';
			if ( isset( $item['description'] ) && is_string( $item['description'] ) )
				$summary = $item['description'];
			elseif ( isset( $item['summary'] ) && is_string( $item['summary'] ) )
				$summary = $item['summary'];

			$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($summary, ENT_QUOTES))));

			if ( $show_summary ) {
				$desc = '';
				$summary = wp_specialchars( $summary );
				$summary = '<div class="rssSummary">'.$summary.'</div>';
			} else {
				$summary = '';
			}

			$date = '';
			if ( $show_date ) {
				if ( isset($item['pubdate']) )
					$date = $item['pubdate'];
				elseif ( isset($item['published']) )
					$date = $item['published'];

				if ( $date ) {
					if ( $date_stamp = strtotime( $date ) )
						$date = '<span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date_stamp ) . '</span>';
					else
						$date = '';
				}
			}

			$author = '';
			if ( $show_author ) {
				if ( isset($item['dc']['creator']) )
					$author = ' <cite>' . wp_specialchars( strip_tags( $item['dc']['creator'] ) ) . '</cite>';
				elseif ( isset($item['author_name']) )
					$author = ' <cite>' . wp_specialchars( strip_tags( $item['author_name'] ) ) . '</cite>';
			}

			echo '<li><a class="rsswidget" href="'.$link.'" title="'.$desc.'">'.$title.'</a>'.$date.$summary.$author.'</li>';
		}
		echo '</ul>';
	} else {
		echo '<ul><li>' . __( 'An error has occurred; the feed is probably down. Try again later.' ) . '</li></ul>';
	}
}

function theme_widget_rss_register() {
	if ( !$options = get_option('widget_bender_rss') )
		$options = array();
	$widget_ops = array('classname' => 'widget_rss %width%', 'description' => __( 'Entries from any RSS or Atom feed' ));
	$control_ops = array('width' => 400, 'height' => 200, 'id_base' => 'rss');
	$name = __('Enhanced RSS');

	wp_unregister_widget_control( 'rss-1' );

	$id = false;
	foreach ( array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['url']) || !isset($options[$o]['title']) || !isset($options[$o]['items']) )
			continue;
		$id = 'rss-'.$o; // Never never never translate an id
		wp_register_sidebar_widget($id, $name, 'theme_widget_rss', $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, 'theme_widget_rss_control', $control_ops, array( 'number' => $o ));
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$id ) {
		wp_register_sidebar_widget( 'rss-1', $name, 'theme_widget_rss', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'rss-1', $name, 'theme_widget_rss_control', $control_ops, array( 'number' => -1 ) );
	}
}

function theme_widget_recent_comments($args) {
  global $wpdb, $comments, $comment;
  extract($args, EXTR_SKIP);
  $options = get_option('widget_recent_comments');
  $title = empty($options['title']) ? __('Recent Comments') : $options['title'];
  $before_content = (!empty($options['before_content'])) ? '<p class="before_content">'. $options['before_content'] .'</p>' : '';
  $after_content = (!empty($options['after_content'])) ? '<p class="after_content">'. $options['after_content'] .'</p>' : '';

  if ( !$number = (int) $options['number'] )
    $number = 5;
  else if ( $number < 1 )
    $number = 1;
  else if ( $number > 15 )
    $number = 15;

  if ( !$comments = wp_cache_get( 'recent_comments', 'widget' ) ) {
    $comments = $wpdb->get_results('SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM '.$wpdb->comments.' WHERE comment_approved = 1 ORDER BY comment_date_gmt DESC LIMIT '.$number);
    wp_cache_add( 'recent_comments', $comments, 'widget' );
  }
  echo $before_widget;
  echo $before_title . $title . $after_title;
  echo $before_content;
?>

			<ul id="recentcomments"><?php
			if ( $comments ) : foreach ($comments as $comment) :
			echo  '<li class="recentcomments">' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
			endforeach; endif;?></ul>
<?php
  echo $after_content;
  echo $after_widget;
}

function theme_widget_recent_comments_register() {
  wp_unregister_sidebar_widget('recent-comments');
  wp_unregister_widget_control('recent-comments');

  $widget_ops = array('classname' => 'widget_recent_comments', 'description' => __( 'The most recent comments' ) );
  wp_register_sidebar_widget('recent-comments', __('Recent Comments'), 'theme_widget_recent_comments', $widget_ops);
  wp_register_widget_control('recent-comments', __('Recent Comments'), 'theme_widget_recent_comments_control');
}

function theme_widget_site_digg($args) {
  extract($args, EXTR_SKIP);
  $options = get_option('widget_site_digg');
  $title = empty($options['title']) ? __('Digg') : $options['title'];

  echo $before_widget;
  echo $before_title . $title . $after_title;
?>

			<div id="tab-container-1" class="tab-container"><?php theme_digg_tabs(); ?></div>
<?php
  echo $after_widget;
}

function theme_widget_site_digg_register() {
  wp_unregister_sidebar_widget('site-digg');
  wp_unregister_widget_control('site-digg');

  $widget_ops = array('classname' => 'widget_digg', 'description' => __( 'A site-wide digg widget' ) );
  wp_register_sidebar_widget('site-digg', __('Site Diggs'), 'theme_widget_site_digg', $widget_ops);
  wp_register_widget_control('site-digg', __('Site Diggs'), 'theme_widget_site_digg_control');
}

function theme_related_posts() {
  global $post, $blog_id;
  $current_post = $post;
  $current_blog = $blog_id;
  $limit = 4;

  if (!function_exists('site_wide_term_related_posts'))
    return;

  // Get more posts than we need since we have to filter out the current post below
  $posts = site_wide_term_related_posts('category', $limit * 2);
  if ($posts) {
    echo '<h3>You might also like:</h3><div id="related-posts">';
    $count = 0;
    $default_img = get_bloginfo('stylesheet_directory') .'/images/generic-post-thumbnail.jpg';
    foreach ($posts as $p) {
      if ($count == $limit)
        break;
      if ($p->blog_id == $current_blog && $p->post_id == $current_post->ID)
        continue;
      $count++;
      switch_to_blog($p->blog_id);
      $post = get_blog_post($p->blog_id, $p->post_id);
      $src = $default_img;
      $caption = '';
      $attachments = get_children('post_parent='. $post->ID .'&post_type=attachment&post_mime_type=image&orderby=menu_order ASC, ID ASC');
      if ($attachments) {
        foreach ($attachments as $id => $attachment) {
          @list($src, $width, $height) = wp_get_attachment_image_src($id);
          $caption = trim($attachment->post_excerpt);
          break;
        }
      }
      $img = '<a href="'. get_permalink() .'"><img src="'. $src .'" width="100" height="100" alt="'. $caption .'" /></a>';
?>
	<div class="related-post"><span class="thumbnail"><?php echo $img; ?></span><span class="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span></div>
<?php
    }
    echo '<div class="clear"></div></div>';
    switch_to_blog($current_blog);
    $post = $current_post;
  }
}

function related_posts_by_tag($post_id=null, $limit=5) {
  global $wpdb, $post;

  if (!$post_id && $post) {
    $post_id = $post->ID;
  }
  $sql = 'SELECT DISTINCT p.ID FROM '. $wpdb->posts .' p
          INNER JOIN '. $wpdb->term_relationships .' t ON p.ID = t.object_id
          WHERE p.ID <> "'. $post_id .'" AND p.post_type = "post"
            AND p.post_status = "publish" AND t.term_taxonomy_id IN (
              SELECT term_taxonomy_id FROM '. $wpdb->term_relationships .'
              WHERE object_id = "'. $post_id .'"
            )
          ORDER BY p.post_date DESC LIMIT '. $limit;
  $results = $wpdb->get_col($sql);
  if ($results) {
    echo '<ul>';
    foreach ($results as $id) {
      echo '<li><a href="'. get_permalink($id) .'">'. get_the_title($id) .'</a></li>';
    }
    echo '</ul>';
  }
}

function theme_the_category_filter($thelist, $separator='', $parents='') {
  if (is_admin())
    return $thelist;

  $pattern = '/('. $separator .' )?<a[^>]+>'. __('Uncategorized') .'<\/a>/';
  $thelist = trim(preg_replace($pattern, '', $thelist));
  if ($thelist)
    return '<div class="cats"><span class="verb">Posted</span> in '. $thelist .'</div>';
}

function theme_category_links_filter($content = '') {
  if (!is_single())
    return $content;

  $links = theme_category_links_content();
  if ($links) {
    $links = str_replace('$', '\$', $links);
    $content = preg_replace('/<span id\=\"(more\-\d+)"><\/span>/', '<span id="\1"></span>'."\n\n". $links ."\n\n", $content);
  }
  return $content;
}

function theme_category_links_content() {
  global $post, $wp_query, $id, $authordata, $page, $numpages, $multipage, $more, $pagenow;

  if (is_array(get_post_meta($post->ID, 'disable_autolinks')))
    return;

  // save globals
  $globals = array($post, $wp_query, $id, $authordata, $page, $numpages, $multipage, $more, $pagenow);

  $categories = get_the_category($post->ID);
  $internal = '';
  $break = false;
  if (!empty($categories) && !(count($categories) == 1 && $categories[0]->name == __('Uncategorized'))) {
    foreach ($categories as $category) {
      if ($category->name != __('Uncategorized')) {
        $sql = '';
        // Get post with shared category
        $q = new WP_Query('cat='. $category->term_id .'&showposts=2');
        while ($q->have_posts()) {
          $q->the_post();
          if (intval($post->ID) != intval($globals[0]->ID)) {
          $internal .= '<li>&raquo; See also: <a href="'. get_permalink($post->ID) .'">'. get_the_title($post->ID) .'</a></li>';
          $break = true;
          break;
        }
      }
      if ($break)
        break;
      }
    }
  }

  // restore globals
  list($post, $wp_query, $id, $authordata, $page, $numpages, $multipage, $more, $pagenow) = $globals;

  $email_domain = str_replace('.', '/', strtolower($_SERVER['SERVER_NAME']));
  $internal .= '<li>&raquo; <a href="/feed/">Get '. get_bloginfo('name') .' by RSS</a> or <a href="http://feedburner.google.com/fb/a/mailverify?uri='. $email_domain .'">sign up by email</a>.</li>';
  return '<ul class="category-links">'. $internal .'</ul>';
}

function theme_refresh_link() {
  global $post;

  $live_blogging = get_post_meta($post->ID, 'live-blogging');
  if ($live_blogging) {
    if (isset($_GET['refresh'])) {
      echo '<div style="margin-bottom:1em;"><a style="font-weight:bold;" href="'. get_permalink() .'">Click here to turn OFF live blogging mode!</a></div>';
    } else {
      echo '<div style="margin-bottom:1em;"><a style="font-weight:bold;" href="?refresh">Click here to turn on live blogging mode!</a></div>';
    }
  }
}

function theme_shortcode_field($atts) {
  global $post;
  $name = $atts['name'];
  if (!empty($name)) return get_post_meta($post->ID, $name, true);
}
add_shortcode('field', 'theme_shortcode_field');


function theme_wt_adsense() {
	global $post;
	
	if (function_exists('wt_print_adsense')) {
		wt_print_adsense( $post->post_author );
	}
}