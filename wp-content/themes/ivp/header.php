<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage IVP
 * @since IVP 1.0
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;



	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	?></title>
<meta name="google-site-verification" content="16RpwfouuM3deRPCArw-4PikuKWVJ502HYlK-PSVT0s" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/js/thickbox/thickbox.css" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
<script type="text/javascript">
    var websiteGlobal = {
    baseUri		:	'',
    baseUrl		:	'<?php echo home_url(); ?>',
    fullUrl		:	'<?php echo get_permalink(get_the_ID()); ?>'
    };
</script>


<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-15922995-1']);
_gaq.push(['_trackPageview']);
(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.4.2.min.js"></script>

</head>

<body <?php body_class(); ?>>
<div class="page-container">
  <?php if (ivp_is_en(get_the_ID())): ?>
  <h1><a href="<?php echo home_url( '/en' ); ?>"><img alt="Instituut voor Psychotrauma" src="<?php echo get_template_directory_uri(); ?>/images/logo.gif" /></a></h1>
  <?php else: ?>
  <h1><a href="<?php echo home_url( '/' ); ?>"><img alt="Instituut voor Psychotrauma" src="<?php echo get_template_directory_uri(); ?>/images/logo.gif" /></a></h1>
  <?php endif; ?>
  <div class="page-content clearfix">
    <div class="page-content-wrapper clearfix">
