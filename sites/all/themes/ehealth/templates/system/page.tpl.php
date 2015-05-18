<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $secondary_menu_heading: The title of the menu used by the secondary links.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['branding']: Items for the branding region.
 * - $page['header']: Items for the header region.
 * - $page['navigation']: Items for the navigation region.
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see omega_preprocess_page()
 */
global $base_url;
$themepath =  $base_url."/".drupal_get_path('theme', 'ehealth');
?>
<?php
if(load_theme_hospital()) {
?>
<div class="top-header-wrapper-hospital">
	<?php print render($page['page_top_timer']); ?>
</div>
<header class="container">
	<?php if ($logo): ?>
        <div class="logo"><a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="site-logo"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" /></a></div>
    <?php endif; ?>
<script type="text/javascript">
var $jq = jQuery.noConflict();
</script>
<div class="header-right">
	<?php print render($page['top-header-hospital']); ?>
</div>
<div class="hospital-logo">
	<?php print render($page['hospital-logo']); ?>
</div>
</header>
<!-- MAIN CONTENT -->
<content class="container">
<div class="hospital-menu"><?php print render($page['hospital-menu']); ?></div>
<?php if($page['hospital-sb-1']) { ?>
<div class="hospital-sidebar-1"><?php print render($page['hospital-sb-1']); ?></div>
<?php } ?>
<div class="hospital-content"><?php print render($page['hospital-content']); ?>
			  <?php if ($title): ?>
				<h1><?php print $title; ?></h1>
			  <?php endif; ?>
			  <?php //print render($title_suffix); ?>
			  <?php //print $messages; ?>
			  <?php print render($tabs); ?>
			  <?php //print render($page['help']); ?>
			  <?php print render($page['content']); ?>
</div>
<div class="hospital-sidebar-2"><?php print render($page['hospital-sb-2']); ?></div>

<!-- FOOTER CONTENT -->
<footer class="hs-container">
	<?php print render($page['footer-hospital']); ?>
</footer>
<?php
} else {
?>
<header class="container">
	<?php if ($logo): ?>
        <div class="logo"><a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="site-logo"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" /></a></div>
    <?php endif; ?>
<script type="text/javascript">
var $jq = jQuery.noConflict();
</script>
<div class="header-right">
<?php print render($page['top-header']); ?><div class="search-bar">
</header>
<!-- MAIN CONTENT -->
<content class="container">
<div class="banner"><?php print render($page['slideshow']); ?></div>
<?php if($page['dashboard-menu']) { ?>
<div class="dashboard-menu">
	<?php print render($page['dashboard-menu']); ?>
</div>
<?php } ?>
<?php if(drupal_is_front_page()) { ?>
<div class="content-top">
	<div class="content-top-first">		
	<div name="autosnippet:basic-combo:yes"><?php print render($page['sidebar_first']); ?></div>
	
	</div>
	<div class="content-top-second">
		<?php print render($page['sidebar_second']); ?>	
	</div>
	<div class="content-top-third">
	<?php print render($page['sidebar_third']); ?>	
	</div>
	<div class="content-top-fourth"> 
	<?php print render($page['sidebar_fourth']); ?>	
	</div>
	<div class="content-top-fifth">
	</div>
</div>
<?php } else { ?>
<div class="l-content" role="main">
			  <?php print render($page['highlighted']); ?>
			  <?php //print $breadcrumb; ?>
			  
			  <?php print render($title_prefix); ?>
			  <?php if ($title): ?>
				<h1><?php print $title; ?></h1>
			  <?php endif; ?>
			  <?php //print render($title_suffix); ?>
			  <?php //print $messages; ?>
			  <?php print render($tabs); ?>
			  <?php //print render($page['help']); ?>
			  <?php if ($action_links): ?>
				<ul class="action-links"><?php //print render($action_links); ?></ul>
			  <?php endif; ?>
			  <?php print render($page['content']); ?>
			  <?php print $feed_icons; ?>
		</div>
<?php } ?>

<div class="content-left">
	<?php print render($page['mission_vision']); ?>
</div>
<div class="content-right">
	<?php print render($page['schemes_programes']); ?>
</div>

<div class="more-to-watch">
	<?php print render($page['more_to_watch']); ?>
</div>
<div class="video-link">
	<?php print render($page['videos']); ?>
</div>
<div class="download-videos">
	<?php print render($page['download_videos']); ?>
</div>
</content>
<!-- FOOTER CONTENT -->

<footer class="container"><!--
	<div class="menu-section">
		<div class="menu-first">
		<?php //print render($page['footer1']); ?>
		</div>
		
		<div class="menu-second">
		<?php //print render($page['footer2']); ?>
		</div>
		
		<div class="menu-third">
		<?php //print render($page['footer3']); ?>
		</div>
		
		<div class="menu-fourth">
		<?php //print render($page['footer4']); ?>
		</div>
	</div>-->
<div class="footer-bottom">
<?php print render($page['footer']); ?>
</div>
</footer>
<?php } ?>
