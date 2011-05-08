<?php
/**
 * @package WordPress
 * @subpackage Portfolio Press
 */
 
/**
 * Sets up the options panel and default functions
 */
 
require_once(TEMPLATEPATH . '/extensions/options-functions.php');
 
/**
 * Enables the Portfolio custom post type
 */
 
if ( ( of_get_option('disable_portfolio') != 'true' ) &&  ( of_get_option('disable_portfolio', "1") != "0" ) ) {
	require_once(TEMPLATEPATH . '/extensions/portfolio-post-type.php');
}
 
/**
 * If 3.1 isn't installed, loads the Simple Custom Post Type Archives Plug-in:
 * http://www.cmurrayconsulting.com/software/wordpress-custom-post-type-archives/
 */

if ( get_bloginfo('version') <= 3.1 ) {
 
// prevents errors when installing the plugin after theme installation
if ( is_admin() && $pagenow == 'plugins.php' && isset($_GET['action']) && $_GET['action'] == 'activate' && isset($_GET['plugin']) && strstr( $_GET['plugin'], 'simple-custom-post-type-archives.php' ) )
	$activating_scpta = true; 

// load in the plugin if its not installed
if( !isset( $activating_scpta ) && !function_exists( 'is_scpta_post_type' ) )
	require_once(TEMPLATEPATH . '/extensions/simple-custom-post-type-archives.php');
	
}

/**
 * Make theme available for translation
 * Translations can be filed in the /languages/ directory
 */
load_theme_textdomain( 'portfoliopress', TEMPLATEPATH . '/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable( $locale_file ) )
	require_once( $locale_file );

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;
	
/**
 * This theme styles the visual editor with editor-style.css to match the theme style.
 */
	
add_editor_style();

/**
 * This theme uses wp_nav_menu() in one location.
 */
register_nav_menus( array(
	'primary' => __( 'Primary Menu', 'portfoliopress' ),
) );

/**
 * Add default posts and comments RSS feed links to head
 */
add_theme_support( 'automatic-feed-links' );

/**
 * Enqueue Javascripts
 */

if ( !is_admin() ) {
	wp_enqueue_script( 'superfish', get_template_directory_uri() .'/js/superfish.js', array( 'jquery' ) );
	wp_enqueue_script( 'fader', get_template_directory_uri() . '/js/jquery.fader.js', array( 'jquery' ) );
}

/**
 * Makes some changes to the <title> tag, by filtering the output of wp_title().
 *
 * If we have a site description and we're viewing the home page or a blog posts
 * page (when using a static front page), then we will add the site description.
 *
 * If we're viewing a search result, then we're going to recreate the title entirely.
 * We're going to add page numbers to all titles as well, to the middle of a search
 * result title and the end of all other titles.
 *
 * @param string $title Title generated by wp_title()
 * @param string $separator The separator passed to wp_title(). Twenty Ten uses a
 * 	vertical bar, "|", as a separator in header.php.
 * @return string The new title, ready for the <title> tag.
 */
function portfolio_filter_wp_title( $title, $separator ) {
	// Don't affect wp_title() calls in feeds.
	if ( is_feed() )
		return $title;
		
	// The $paged global variable contains the page number of a listing of posts.
	// The $page global variable contains the page number of a single post that is paged.
	// We'll display whichever one applies, if we're not looking at the first page.
	global $paged, $page;

	if ( is_search() ) {
		// If we're a search, let's start over:
		$title = sprintf( __( 'Search results for %s', 'portfoliopress' ), '"' . get_search_query() . '"' );
		// Add a page number if we're on page 2 or more:
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', 'portfoliopress' ), $paged );
		// Add the site name to the end:
		$title .= " $separator " . get_bloginfo( 'name', 'display' );
		// We're done. Let's send the new title back to wp_title():
		return $title;
	}

	// Otherwise, let's start by adding the site name to the end:
	$title .= get_bloginfo( 'name', 'display' );

	// If we have a site description and we're on the home/front page, add the description:
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', 'portfoliopress' ), max( $paged, $page ) );

	// Return the new title to wp_title():
	return $title;
}

add_filter( 'wp_title', 'portfolio_filter_wp_title', 100, 2 );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function portfolio_page_menu_args($args) {
	$args['show_home'] = true;
	$args['menu_class'] = 'menu';
	return $args;
}
add_filter( 'wp_page_menu_args', 'portfolio_page_menu_args' );

/**
 * Class name for wp_nav_menu
 */
function portfolio_wp_nav_menu_args($args)
{
	$args['container_class'] = 'menu';
	$args['menu_class'] = '';
	return $args;
}

add_filter( 'wp_nav_menu_args', 'portfolio_wp_nav_menu_args' );


/**
 * Register widgetized area and update sidebar with default widgets
 */
 
function portfolio_widgets_init() {
	register_sidebar( array (
		'name' => __( 'Sidebar', 'portfoliopress' ),
		'id' => 'sidebar',
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	
register_sidebar(array('name' => __('Footer 1', 'portfoliopress'),'id' => 'footer-1', 'description' => __("Widetized footer", 'portfoliopress'), 'before_widget' => '<div id="%1$s" class="widget-container %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	register_sidebar(array('name' => __('Footer 2', 'portfoliopress'),'id' => 'footer-2', 'description' => __("Widetized footer", 'portfoliopress'), 'before_widget' => '<div id="%1$s" class="widget-container %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	register_sidebar(array('name' => __('Footer 3', 'portfoliopress'),'id' => 'footer-3', 'description' => __("Widetized footer", 'portfoliopress'), 'before_widget' => '<div id="%1$s" class="widget-container %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	register_sidebar(array('name' => __('Footer 4', 'portfoliopress'),'id' => 'footer-4', 'description' => __("Widetized footer", 'portfoliopress'), 'before_widget' => '<div id="%1$s" class="widget-container %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));		
}

add_action( 'init', 'portfolio_widgets_init' );
?>