<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Gesso
 * @since 1.0.0
 */

/**
 * Helper Functions
 */
require_once( get_template_directory() . '/inc/helpers.php' );

/**
 * Core Theme Features
 */

// Setting main content width - update to match the width of your site's main content area.
if ( ! isset( $content_width ) ) {
	$content_width = 840;
}

if ( function_exists( 'add_theme_support' ) ) {
	// Adding theme support for HTML5.
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

	// Adds site name to title tag.
	add_theme_support( 'title-tag' );

	// Add support for automatic links for feeds.
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );

	// Define automatic thumbnail sizes.
	add_image_size( 'large', 700, '', true ); // Large Thumbnail.
	add_image_size( 'medium', 250, '', true ); // Medium Thumbnail.
	add_image_size( 'small', 120, '', true ); // Small Thumbnail.
	// add_image_size( 'custom-size', 700, 200, true ); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');.
}

/**
 * Check for widget and display the dynamic sidebar.
 *
 * @param int $sidebar_id The ID of the sidebar to display.
 *
 * @return bool
 */
function has_visible_widgets( $sidebar_id ) {
	if ( is_active_sidebar( $sidebar_id ) ) {
		ob_start();
		dynamic_sidebar( $sidebar_id );
		$sidebar = ob_get_contents();
		ob_end_clean();
		if ( '' == $sidebar ) {
			return false;
		}
	} else {
		return false;
	}
	return true;
}

/**
 * Setup theme scripts and styles.
 */
function gesso_scripts() {

	// Get Gesso-WP version.
	$gesso_version = wp_get_theme()->get( 'Version' );

	global $wp_scripts;

	// Load jQuery from the Google CDN.
	if ( ! is_admin() ) {

		$ver = '1.12.4'; // Current stable/secure 1.x version.

		if ( isset( $wp_scripts->registered['jquery']->ver ) ) {
			$ver = str_replace( '-wp', '', $wp_scripts->registered['jquery']->ver );
		}

		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/' . $ver . '/jquery.min.js', array(), $ver );

	}

	wp_register_script( 'gessocommon', get_template_directory_uri() . '/js/dist/common.min.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/dist/common.min.js' ) );
	wp_enqueue_script( 'gessocommon' );

	// Modernizr.
	wp_register_script( 'gessomodernizr', get_template_directory_uri() . '/js/libraries/modernizr.min.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/libraries/modernizr.min.js' ) );
	wp_enqueue_script( 'gessomodernizr' );

	// Details polyfill for IE/Edge.
	wp_register_script( 'gessodetailspolyfill', get_template_directory_uri() . '/js/libraries/details-element-polyfill.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/libraries/details-element-polyfill.js' ) );
	wp_enqueue_script( 'gessodetailspolyfill' );

	if ( is_singular() && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Mobile menu.
	wp_register_script( 'gessomobilemenu', get_template_directory_uri() . '/js/dist/mobile-menu.min.js', array( 'jquery', 'gessocommon', 'gessomodernizr' ), filemtime( get_template_directory() . '/js/dist/mobile-menu.min.js' ) );
	wp_enqueue_script( 'gessomobilemenu' );

	// Primary menu.
	wp_register_script( 'gessoprimarymenu', get_template_directory_uri() . '/js/dist/primary-menu.min.js', array( 'jquery', 'gessocommon', 'gessomodernizr' ), filemtime( get_template_directory() . '/js/dist/primary-menu.min.js' ) );
	wp_enqueue_script( 'gessoprimarymenu' );

	// Custom scripts.
	wp_register_script( 'gessoscripts', get_template_directory_uri() . '/js/dist/scripts.min.js', array( 'jquery', 'gessocommon', 'gessomodernizr' ), filemtime( get_template_directory() . '/js/dist/scripts.min.js' ) );
	wp_enqueue_script( 'gessoscripts' );

	/**
	* https://developer.wordpress.org/reference/hooks/style_loader_tag/
	* Using style_loader_tag:
	* - Enqueue the preconnect URL as a style
	* - Change the rel attribute
	* - Remove the type & media attributes
	*/

	// Enqueue Google Font.
	wp_enqueue_style( 'google-fonts-preconnect', 'https://fonts.gstatic.com', false );
	wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i&display=swap', array( 'google-fonts-preconnect' ), null );

	/**
	 * Filter enqueue styles.
	 */
	function gesso_google_font_enqueued_styles( $html, $handle ) {
		$handles = array( 'google-fonts-preconnect' );
		if ( in_array( $handle, $handles ) ) {
			// Change enqueued style from stylesheet to preconnect.
			$html = str_replace( "rel='stylesheet'", "rel='preconnect'", $html );
			// Remove unnecessary attributes.
			$html = str_replace( "type='text/css'", '', $html );
			$html = str_replace( "media='all'", '', $html );
		}
		return $html;
	}
	add_filter( 'style_loader_tag', 'gesso_google_font_enqueued_styles', 10, 4 );

	wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/css/styles.css', array(), filemtime( get_stylesheet_directory() . '/css/styles.css' ), 'all' );

}
add_action( 'wp_enqueue_scripts', 'gesso_scripts' );

/**
 * Setup admin specific scripts.
 */
function gesso_admin_scripts() {

	// $pagenow, is a global variable referring to the filename of the current page,
	// such as ‘admin.php’, ‘post-new.php’
	global $pagenow;

	// Custom editor scripts.
	if ( is_admin() && ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) ) {
		wp_register_script( 'gessoeditorscripts', get_template_directory_uri() . '/js/dist/editor.min.js', array( 'acf' ), filemtime( get_template_directory() . '/js/dist/editor.min.js' ) );
		wp_enqueue_script( 'gessoeditorscripts' );
	}

}
// add_action( 'admin_enqueue_scripts', 'gesso_admin_scripts' );.

/**
 * Register the site navigation.
 */
function register_gesso_menu() {
	register_nav_menus(
		array(
			'primary' => __( 'Primary', 'gesso' ),
			'secondary' => __( 'Secondary', 'gesso' ),
		)
	);
}
add_action( 'init', 'register_gesso_menu' );


/**
 * Add page slug to body class. Credit: Starkers Wordpress Theme.
 *
 * @param array $classes The array of body classes.
 *
 * @return array
 */
function add_slug_to_body_class( $classes ) {
	global $post;
	if ( is_home() ) {
		$key = array_search( 'blog', $classes );
		if ( $key > -1 ) {
			unset( $classes[ $key ] );
		}
	} elseif ( is_page() ) {
		$classes[] = sanitize_html_class( $post->post_name );
	} elseif ( is_singular() ) {
		$classes[] = sanitize_html_class( $post->post_name );
	}

	return $classes;
}
add_filter( 'body_class', 'add_slug_to_body_class' );

/**
 * Initial Sidebar and Footer Widget Areas.
 */
function gesso_widgets_init() {
	register_sidebar(
		array(
			'name' => __( 'Widget Area 1', 'gesso' ),
			'description' => __( 'Widget Area 1', 'gesso' ),
			'id' => 'widget-area-1',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget__title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => __( 'Footer Widgets', 'gesso' ),
			'description' => __( 'Footer Widgets', 'gesso' ),
			'id' => 'footer-widgets',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget__title">',
			'after_title' => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'gesso_widgets_init' );

/**
 * Setup theme pagination.
 */
function gesso_pagination() {
	global $wp_query;
	$big = 999999999;
	echo wp_kses_post(
		paginate_links(
			array(
				'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total' => $wp_query->max_num_pages,
			)
		)
	);
}
add_action( 'init', 'gesso_pagination' );

/**
 * Adds proper markup to pages content.
 */
function gesso_link_pages() {
	$gesso_links = array(
		'before'    => '<nav role="navigation" aria-labelledby="pagination-heading"><h2 id="pagination-heading" class="visually-hidden">Pagination</h2><ul class="pager">',
		'after'     => '</ul></nav>',
		'link_before' => '<li class="pager__item>',
		'link_after'  => '</li>',
	);
	wp_link_pages( $gesso_links );
}


/**
 * Remove thumbnail dimensions.
 *
 * @param string $html The HTML content.
 *
 * @return string
 */
function remove_thumbnail_dimensions( $html ) {
	$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );
	return $html;
}
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 ); // Remove width and height attributes from thumbnails.
add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 ); // Remove width and height attributes from post images.

/**
 * Allowing styles for post editor to match how it will actually be visually represented.
 */
function gesso_add_editor_styles() {
	add_editor_style( 'css/custom-editor-styles.css' );
}
add_action( 'admin_init', 'gesso_add_editor_styles' );


// ------------------------------------------------------
// Timber Support - Starter Theme Functions
// ------------------------------------------------------
if ( ! class_exists( 'Timber' ) ) {
	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);
	return;
}

Timber::$dirname = array( 'templates' );

/**
 * New site class extended from Timber.
 */
class StarterSite extends TimberSite {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// add_theme_support( 'post-formats' ); // uncomment to enable post formats.
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		parent::__construct();
	}

	/**
	 * Add items to Timber context.
	 *
	 * @param array $context The Timber context array.
	 *
	 * @return array The Timber context array.
	 */
	public function add_to_context( $context ) {
		$context['foo'] = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::get_context();';
		$context['primary_menu'] = new Timber\Menu( 'primary' );
		$context['secondary_menu'] = new Timber\Menu( 'secondary' );
		$context['menu'] = new Timber\Menu();
		$context['current_year'] = gmdate( 'Y' );
		$context['site'] = $this;
		$context['gesso_image_path'] = get_template_directory_uri() . '/images';
		return $context;
	}

	/**
	 * Method used to add custom Twig functions.
	 *
	 * @param \Twig_Environment $twig The Twig object.
	 *
	 * @return \Twig_Environment
	 */
	public function add_to_twig( \Twig_Environment $twig ) {
		/**
		 * This is where you can add your own fuctions to twig.
		 *
		 * @see https://timber.github.io/docs/guides/extending-timber/#adding-to-twig
		 */
		$twig->addExtension( new \Twig_Extension_StringLoader() );
		$twig->addFilter( new \Twig_SimpleFilter( 'myfoo', 'my_foo' ) );
		return $twig;
	}

}

new StarterSite();


/**
 * Override default WordPress gallery markup, outputs in BEM format.
 *
 * @param string $gallery The gallery raw HTML.
 * @param array  $attr    The gallery attributes.
 *
 * @return string
 */
function gesso_bem_gallery( $gallery, $attr ) {
	// [ thumbnail | medium | large | full ]
	$size   = 'thumbnail';
	$output = '<div class="gallery">';
	$posts  = get_posts(
		array(
			'include' => $attr['ids'],
			'post_type' => 'attachment',
		)
	);

	foreach ( $posts as $image_post ) {
		$src      = wp_get_attachment_image_src( $image_post->ID, $size );
		$alt_text = get_post_meta( $image_post->ID, '_wp_attachment_image_alt', true );
		$output .= '<div class="gallery__item">';
		$output .= '<a href="' . $src[0] . '"><img alt="' . $alt_text . '" class="gallery__item-image" src="' . $src[0] . '"></a>';
		$output .= '<div class="gallery__item-title">' . $image_post->post_title . '</div>';
		$output .= '<div class="gallery__item-caption">' . $image_post->post_excerpt . '</div>';
		$output .= '<div class="gallery__item-description">' . $image_post->post_content . '</div>';
		$output .= '</div>';
	}

	$output .= '</div>';

	return $output;
}
add_filter( 'post_gallery', 'gesso_bem_gallery', 10, 2 );

/**
 * Register Twig namespaces to Pattern Lab patterns.
 *
 * @param Twig_Loader_Filesystem $loader
 * @return Twig_Loader_Filesystem
 */
add_filter(
	'timber/loader/loader',
	function( $loader ) {
		$loader->addPath( __DIR__ . '/source/_patterns/02-base', 'base' );
		$loader->addPath( __DIR__ . '/source/_patterns/03-layouts', 'layouts' );
		$loader->addPath( __DIR__ . '/source/_patterns/04-components', 'components' );
		$loader->addPath( __DIR__ . '/source/_patterns/05-templates', 'templates' );
		$loader->addPath( __DIR__ . '/source/_patterns/06-pages', 'pages' );
		$loader->addPath( __DIR__ . '/source/_macros', 'macros' );
		return $loader;
	}
);
