<?php
/**
 * @package WordPress
 * @subpackage Bender_Theme
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php theme_pagetitle(); ?></title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?v=20090304" type="text/css" media="screen, tv, projection" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/print.css?v=20080703" type="text/css" media="print" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
</head>

<body<?php theme_body_attrs(); ?> id="body1">
<?php
global $wpdb;
$blogid = $wpdb->blogid;
$bsa_site_array = array(1294);
if (in_array($blogid, $bsa_site_array)) { 
?>
<!-- BuySellAds.com Ad Code -->
<script type="text/javascript">
(function(){
  var bsa = document.createElement('script');
     bsa.type = 'text/javascript';
     bsa.async = true;
     bsa.src = '//s3.buysellads.com/ac/bsa.js';
  (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);
})();
</script>
<!-- END BuySellAds.com Ad Code -->
<?php
}
?>
<?php  
	if (function_exists('ubiquity_print')) {
		ubiquity_print("tracker_bodytop");
		ubiquity_print("navigation");
	}
?>
<div id="page_container">

   <?php do_action('theme_page_top'); ?>

<div id="page">
	<div id="header">
		<a href="<?php echo get_option('home'); ?>/" id="sitelogo"><?php theme_masthead(); ?></a>
		<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<?php do_action('theme_masthead'); ?>
	</div>

<?php do_action('theme_navmenu'); ?>

	<div id="content_container">
