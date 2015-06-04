<?php
/** Tell WordPress to run ivp_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'ivp_setup' );

require_once(ABSPATH . '/wp-admin/includes/plugin.php');
require_once(ABSPATH . WPINC . '/pluggable.php');

if ( ! function_exists( 'ivp_setup' ) ):

function ivp_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Post Format support. You can also use the legacy "gallery" or "asides" (note the plural) categories.
	add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );


	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'ivp', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require( $locale_file );


	register_nav_menus( array(
		'nl_top_menu' => __( 'NL Top Menu', 'ivp' ),
        'nl_side_menu' => __( 'NL Side Menu', 'ivp' ),
        'en_top_menu' => __( 'EN Top Menu', 'ivp' ),
        'en_side_menu' => __( 'EN Side Menu', 'ivp' ),
	) );

    add_action('admin_menu', 'ivp_options_menu');

    add_action('init', 'ivp_call_me_back_form');

}
endif;


function ivp_widgets_init() {

	register_sidebar( array(
		'name' => __( 'Right Sidebar Widget Area', 'ivp' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The right sidebar widget area - NL', 'ivp' ),
		'before_widget' => '<div class="information-block">',
		'after_widget' => '<span class="decor-shadow"></span></div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Left Sidebar Widget Area', 'ivp' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The left sidebar widget area - NL', 'ivp' ),
		'before_widget' => '<li>',
		'after_widget' => '</li>',
		'before_title' => '',
		'after_title' => '',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget Area', 'ivp' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The footer widget area - NL', 'ivp' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<span class="footer-widget-title">',
		'after_title' => '</span>',
	) );


}
/** Register sidebars by running ivp_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'ivp_widgets_init' );

add_action( 'widgets_init', 'ivp_register_widgets' );
function ivp_register_widgets() {
	require('php/Reference_Widget.php');
	register_widget( 'Reference_Widget' );
}



if ( ! function_exists( 'ivd_media_breadcrumbs' ) ) :
/**
 * Creates breadcrumbs links
 *
 * @param object $post - last link in breadcrumbs
 * @param string $section - site section (en, nl), default 'nl'
 * @return array with breadcrumbs (post_link,)
 *
 * @since VHD Media 1.0
 */
function ivd_media_breadcrumbs($post, $section = 'nl') {

    $breadcrumbs = array();

    $breadcrumbs[$post->post_name] = array('post_title' => $post->post_title,
			  'ID' => $post->ID,
			  'link' => get_page_link($post->ID)
			  );

	$currentPost = $post;
	$parentId = $currentPost->post_parent;

	$category = get_the_category();
	if (!empty($category[0])) {
		$currentPost = get_page_by_title( $category[0]->cat_name );
        if ($currentPost) {
            $parentId = $currentPost->ID;
        }
	}

	$i = 0;
	while($parentId) {
		$currentPost = get_post($parentId);

		if ($currentPost) {
			$parentId = $currentPost->post_parent;
			$breadcrumbs[$currentPost->post_name] = array (
				'post_title' => $currentPost->post_title,
				'ID' => $currentPost->ID,
				'link' => get_page_link($currentPost->ID),
			);
		} else {
			$parentId = 0;
		}
	}

    // add nl index page as first page in breadcrumbs
    if ($section == 'nl') {
        $posts = get_posts('meta_key=is_nl_index&meta_value=1&post_type=page');
        if ($posts[0]) {
            $breadcrumbs[$posts[0]->post_name] = array('post_title' => $posts[0]->post_title,
                      'ID' => $posts[0]->ID,
                      'link' => get_page_link($posts[0]->ID)
                      );
        }
    }

	return array_reverse($breadcrumbs);

}
endif;

function ivp_sort_by_position($a, $b) {
    if ($a['position'] > $b['position']) return 1;
    if ($a['position'] < $b['position']) return -1;

    return 0;
}

function ivp_media_media_list($attr, $content = null) {
	extract(shortcode_atts(array(
		'type' => '',
	), $attr));

	if (empty($attr['type'])) return '';

	global $post;

	$media = get_posts('meta_key=_ivp-media-template-type&meta_value=employees&post_type=attachment&numberposts=-1');

	$results = array();

	if (empty($media)) return '';

	$i = 0;
	foreach ($media as $mediaEntry) {

		$type = get_post_meta($mediaEntry->ID, "_ivp-media-template-type", true);

		if ( $type != $attr['type']) continue;

		$position = (int) get_post_meta($mediaEntry->ID, "_ivp-media-position", true);


		$results[$i] = array (
			'ID' =>  $mediaEntry->ID,
			'function-nl' => get_post_meta($mediaEntry->ID, "_ivp-media-function-nl", true),
            'function-en' => get_post_meta($mediaEntry->ID, "_ivp-media-function-en", true),
			'type' => get_post_meta($mediaEntry->ID, "_ivp-media-template-type", true),
			'mime_type' => $mediaEntry->post_mime_type,
			'caption' => $mediaEntry->post_excerpt,
			'content' => $mediaEntry->post_content,
			'position' => $position
		);

		$i++;
	}

	if (empty($results)) return '';

	usort($results, 'ivp_sort_by_position');

	switch ($type) {
		case 'employees':
			ob_start();
			require('tpl/employees_list.php');
			$html = ob_get_contents();
			ob_end_clean();
		break;
		case 'partners':
			ob_start();
			require('tpl/partners_list.php');
			$html = ob_get_contents();
			ob_end_clean();
		break;
		case 'reference':
			ob_start();
			require('tpl/reference_list.php');
			$html = ob_get_contents();
			ob_end_clean();
		break;
		default:
			$html = '';
	}



	return $html;

}
add_shortcode('media-list', 'ivp_media_media_list');

// process shortcodes in text widget, custom widget link field
add_filter('widget_text', 'do_shortcode');
add_filter('ref_widget_link', 'do_shortcode');


/* For adding custom field to gallery popup */
function ivp_media_attachment_fields_to_edit($form_fields, $post) {

	$selectValue = get_post_meta($post->ID, "_ivp-media-template-type", true);
	$selectOptions = array(
		'employees' => 'employees',
	);
	$select = '<select name="attachments['.$post->ID.'][ivp-media-template-type]" id="ivp-media-template-type">';
	$select .= '<option value="">none</option>';
	foreach ($selectOptions as $selectOptionIndex => $selectOptionValue):
	$selected = ($selectValue == $selectOptionIndex) ? ' selected="selected"': '';
	$select .= '<option value="'.$selectOptionIndex.'"'.$selected.'>'.$selectOptionValue.'</option>';
	endforeach;
	$select .= '</select>';


	$form_fields["ivp-media-template-type"] = array(
		"label" => __("Template Type"),
		"input" => "html",
		"html" => $select,
		"value" => get_post_meta($post->ID, "_ivp-media-template-type", true),
                "helps" => __("The type of media you are adding, use none if doesn't apply;
							  media with the same type can be inserted in a post/page using
							  the shortcode [media-list type=\"the-type\"] where the-type can be: employees"),
	);

	$form_fields["ivp-media-position"] = array(
		"label" => __("Order"),
		'id' => 'ivp-media-position',
		"input" => "text",
		"value" => get_post_meta($post->ID, "_ivp-media-position", true),
                "helps" => __("Used for ordering (ASC) when displaying in page."),
	);

	$form_fields["ivp-media-function-nl"] = array(
		"label" => __("Function NL"),
		"input" => "text",
		"value" => get_post_meta($post->ID, "_ivp-media-function-nl", true),
                "helps" => __("Job position NL"),
	);

    $form_fields["ivp-media-function-en"] = array(
		"label" => __("Function EN"),
		"input" => "text",
		"value" => get_post_meta($post->ID, "_ivp-media-function-en", true),
                "helps" => __("Job position EN"),
	);




   return $form_fields;
}
// now attach our function to the hook
add_filter("attachment_fields_to_edit", "ivp_media_attachment_fields_to_edit", null, 2);


function ivp_media_attachment_fields_to_save($post, $attachment) {
	if (isset($attachment['ivp-media-template-type'])) {
		update_post_meta($post['ID'], '_ivp-media-template-type', $attachment['ivp-media-template-type']);
	}
	if (isset($attachment['ivp-media-position'])) {
		update_post_meta($post['ID'], '_ivp-media-position', $attachment['ivp-media-position']);
	}
	if (isset($attachment['ivp-media-function-nl'])) {
		update_post_meta($post['ID'], '_ivp-media-function-nl', $attachment['ivp-media-function-nl']);
	}
    if (isset($attachment['ivp-media-function-en'])) {
		update_post_meta($post['ID'], '_ivp-media-function-en', $attachment['ivp-media-function-en']);
	}

	return $post;
}
// now attach our function to the hook.
add_filter("attachment_fields_to_save", "ivp_media_attachment_fields_to_save", null , 2);


function ivp_add_help_tab () {
    $screen = get_current_screen();

    ob_start();
    require('tpl/help.php');
    $html = ob_get_contents();
    ob_end_clean();


    // Add my_help_tab if current screen is My Admin Page
    $screen->add_help_tab( array(
        'id'	=> 'custom_features',
        'title'	=> __('Custom Features'),
        'content'	=> $html,
    ) );
}

add_action("load-post.php", 'ivp_add_help_tab');
add_action("load-post-new.php", 'ivp_add_help_tab');

function ivp_media_get_site_url() {
	return get_site_url();
}

add_shortcode('site-url', 'ivp_media_get_site_url');


function ivp_en_menu_links($current_page) {

    $postId = 0;
    $posts = get_posts('meta_key=is_en_index&meta_value=1&post_type=page');
    if (!empty($posts[0])) {
        $post = $posts[0];
        $postId = $post->ID;
    }
    //$args = array
    $posts_children = get_posts('post_parent='.$postId.'&orderby=menu_order&order=ASC&post_type=page&numberposts=-1');

    $menu_links = array();
    foreach ($posts_children as $post_child) {
        $menu_links[] = array(
            'link' => get_permalink($post_child->ID),
            'title' => $post_child->post_title,
            'is_current'=> ($post_child->ID == $current_page->ID) ? true: false
        );
    }

    return $menu_links;
}

function ivp_menu_links($current_page) {

    $postId = 0;
    $posts = get_posts('meta_key=show_in_main_nav&meta_value=1&post_type=page&orderby=menu_order&order=ASC&numberposts=-1');

    $menu_links = array();
    foreach ($posts as $post) {
        $menu_links[] = array(
            'link' => get_permalink($post->ID),
            'title' => $post->post_title,
            'is_current'=> (!empty($current_page) && $post->ID == $current_page->ID) ? true: false
        );
    }

    return $menu_links;
}


function ivp_is_en($current_page_id) {

    $permalink = get_permalink( $current_page_id );

    if (strpos($permalink, '/en/') !== FALSE) {
        return true;
    }
    return false;
}

add_filter('sp_template_image-widget_widget.php', 'ivp_image_plugin_template_filter');
function ivp_image_plugin_template_filter($template) {
	return get_template_directory() . '/tpl/partners.php';
}


/**
 * adds extra views to admin page list
 */
add_filter('views_edit-page', 'ivp_edit_page_views');
function ivp_edit_page_views($views) {

    $enIds = array();
    $posts = get_posts('meta_key=is_en_index&meta_value=1&post_type=page');
    if (!empty($posts[0])) {
        $post = $posts[0];
        $postId = $post->ID;
    }
    $enIds[] = $post->ID;
    $posts_children = get_posts('post_parent='.$postId.'&orderby=menu_order&order=ASC&post_type=page&numberposts=-1');

    foreach ($posts_children as $post_child) {
        $enIds[] = $post_child->ID;
    }

    $total_posts_en = count($posts_children) + count($posts);



    //$class =
    $class = ( isset($_REQUEST['include']) && $_REQUEST['include'] == $enIds ) ? ' class="current"' : '';
    $data = array(
        'post_type' => 'page',
        'include' => $enIds,
        'sentence' => 'en',
        'numberposts'=>-1,
    );
    $query = http_build_query($data);
	$views['en'] = "<a href='edit.php?$query'$class>" . sprintf( _nx( 'EN pages <span class="count">(%s)</span>', 'EN pages <span class="count">(%s)</span>', $total_posts_en, 'posts' ), number_format_i18n( $total_posts_en ) ) . '</a>';


    $data = array(
        'post_type' => 'page',
        'exclude' => $enIds,
        'sentence' => 'nl',
        'numberposts'=>-1,
    );
    $posts = get_posts($data);
    $total_posts_nl = count($posts);
    //print_r($posts);

    $query = http_build_query($data);
    $class = ( isset($_REQUEST['exclude']) && $_REQUEST['exclude'] == $enIds ) ? ' class="current"' : '';
    $views['nl'] = "<a href='edit.php?$query'$class>" . sprintf( _nx( 'NL pages <span class="count">(%s)</span>', 'NL pages <span class="count">(%s)</span>', $total_posts_nl, 'posts' ), number_format_i18n( $total_posts_nl ) ) . '</a>';


    return $views;
}


/**
 * adds extra views to admin page list
 */
add_filter('views_edit-post', 'ivp_edit_post_views');
function ivp_edit_post_views($views) {


    $posts = get_posts('category_name=vacatures&post_type=post&numberposts=-1');
    $total_posts = count($posts);

    $class = ( isset($_REQUEST['category_name']) && $_REQUEST['category_name'] == 'vacatures' ) ? ' class="current"' : '';
    $views['vacatures'] = "<a href='edit.php?post_type=post&category_name=vacatures'$class>" . sprintf( _nx( 'Vacatures <span class="count">(%s)</span>', 'Vacatures <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

    $posts = get_posts('category_name=news&post_type=post&numberposts=-1');
    $total_posts = count($posts);

    $class = ( isset($_REQUEST['category_name']) && $_REQUEST['category_name'] == 'news' ) ? ' class="current"' : '';
    $views['news'] = "<a href='edit.php?post_type=post&category_name=news'$class>" . sprintf( _nx( 'News <span class="count">(%s)</span>', 'News <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

    return $views;
}

add_action('pre_get_posts', 'ivp_edit_page_views_query_params');

/**
 * add extra params to query if they are set in request (for admin page views)
 */
function ivp_edit_page_views_query_params($wpQuery) {
    if (!is_admin()) return;

    // this is to add only to a specific query request identified by sentence
    if (isset($_REQUEST['exclude']) && !$wpQuery->get('post__not_in') && $wpQuery->get('sentence') == 'nl') {
        $wpQuery->set('sentence', null);
        $wpQuery->set('post__not_in', $_REQUEST['exclude']);
    }

    if (isset($_REQUEST['include']) && !$wpQuery->get('post__in') && $wpQuery->get('sentence') == 'en') {
        $wpQuery->set('sentence', null);
        $wpQuery->set('post__in', $_REQUEST['include']);
    }

    if (isset($_REQUEST['meta_value']) && !$wpQuery->get('meta_value')
        && in_array($wpQuery->get('sentence'), array('employees'))) {
        $wpQuery->set('sentence', null);
        $wpQuery->set('meta_value', $_REQUEST['meta_value']);
        $wpQuery->set('meta_key', $_REQUEST['meta_key']);
    }
}


/**
 * adds extra views to admin upload list
 */
add_filter('views_upload', 'ivp_upload_views');
function ivp_upload_views($views) {


    $posts = get_posts('meta_key=_ivp-media-template-type&meta_value=employees&post_type=attachment&numberposts=-1');
    $total_posts = count($posts);

    $class = ( isset($_REQUEST['meta_key']) && $_REQUEST['meta_key'] == '_ivp-media-template-type' && $_REQUEST['meta_value'] == 'employees') ? ' class="current"' : '';
    $views['employees'] = "<a href='upload.php?sentence=employees&meta_key=_ivp-media-template-type&meta_value=employees&post_type=attachment&numberposts=-1'$class>" . sprintf( _nx( 'Employees <span class="count">(%s)</span>', 'Employees <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

    return $views;
}

/**
 * Add new mime type to post mime type otherwise the ivp_upload_views filter doesn't work
 * this will automatically add the mime type to admin media list views
 *
 */
add_filter('post_mime_types', 'ivp_add_post_mime_types');

function ivp_add_post_mime_types($post_mime_types) {
    $post_mime_types['application/pdf'] = array(__('PDFs'), __('Manage PDFs'), _n_noop('PDFs <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>'));
    return $post_mime_types;
}


function ivp_contact_form() {
	ob_start();
	require('tpl/contact_form.php');
	$html = ob_get_contents();
    ob_end_clean();
	return $html;
}

add_shortcode('contact-form', 'ivp_contact_form');

function ivp_newsletter_form() {
	ob_start();
	require('tpl/newsletter_form.php');
	$html = ob_get_contents();
    ob_end_clean();
	return $html;
}

add_shortcode('newsletter-form', 'ivp_newsletter_form');




function ivp_vacatures_list() {

    $posts = get_posts('category_name=vacatures&post_type=post&orderby=menu_order&order=ASC&numberposts=-1');

    $vacatures = array();

    foreach ($posts as $post) {
        $vacatures[] = array(
            'post_title' => $post->post_title,
            'post_link' => get_permalink($post->ID),
        );
    }

	ob_start();
	require(dirname(__FILE__).'/tpl/vacatures_list.php');
	$html = ob_get_contents();
    ob_end_clean();
    //$html = json_encode($vacatures);
	return $html;
}

add_shortcode('vacatures', 'ivp_vacatures_list');


function ivp_news_list($attr) {
    extract(shortcode_atts(array(
		'limit' => '',
	), $attr));

    $numberposts = '-1';
    if (!empty($attr['limit'])) {
        $numberposts = $attr['limit'];
    }


    $posts = get_posts('category_name=news&post_type=post&orderby=post_date&order=DESC&numberposts='.$numberposts);

    $news = array();

    foreach ($posts as $post) {
        $url = get_post_meta( $post->ID, '_links_to', true);
        $targetBlank = get_post_meta( $post->ID, '_links_to_target', true );

        $news[] = array(
            'post_date' => mysql2date(get_option('date_format'), $post->post_date),
            'post_title' => $post->post_title,
            'target' => $targetBlank,
            'post_link' => ($url)? $url: get_permalink($post->ID),
        );
    }

	ob_start();
	require('tpl/news_list.php');
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}

add_shortcode('news', 'ivp_news_list');



function add_jquery_data() {
    global $parent_file;

    if ( $parent_file == 'upload.php') {
?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			hideAll = function () {
				jQuery('tr.ivp-media-position').css({display: 'none'});
				jQuery('tr.ivp-media-function-en').css({display: 'none'});
                jQuery('tr.ivp-media-function-nl').css({display: 'none'});
			}

			showEmployees = function() {
				jQuery('tr.ivp-media-position').css({display: 'table-row'});
				jQuery('tr.ivp-media-function-en').css({display: 'table-row'});
				jQuery('tr.ivp-media-function-nl').css({display: 'table-row'});
			}

			showBlock = function () {
				switch (jQuery('#ivp-media-template-type').val()) {
					case 'employees':
						showEmployees();
					break;
                    default:
                        hideAll();
				}
			}

			hideAll();
			showBlock();

			jQuery('#ivp-media-template-type').bind('change', showBlock);
		});
		</script>
<?php
    }
}

add_filter('admin_head', 'add_jquery_data');

function ivp_options_menu() {
    // default settings for options
    add_option('ivp_email_address_weekdays', '');
    add_option('ivp_email_address_weekend', '');
	add_option('ivp_ip_restriction', '127.0.0.1');

    $callbackView = 'ivp_options_view';
    add_options_page('Manage IVP Theme Settings', 'IVP Theme', 'manage_options', 'ivp-options', $callbackView);

    // name of the function that does validation
    $validationCallbackEmail = 'ivp_validate_email_field';
    register_setting( 'ivp_settings', 'ivp_email_address_weekdays', $validationCallbackEmail);
    register_setting( 'ivp_settings', 'ivp_email_address_weekend', $validationCallbackEmail);
    register_setting( 'ivp_settings', 'ivp_ip_restriction', null);//no validation

}
function ivp_options_view() {
    include('tpl/options.php');
}

function ivp_validate_email_field($input) {
    if (!is_email($input)) {
        $message = 'Please add a valid email address';
        $setting = 'ivp_email_address_weekdays';
        $id = 'ivp_email_address_weekdays_validation_error';
        add_settings_error( $setting, $id, $message, $type = 'error' );
        return '';
    }
    return $input;
}

function ivp_call_me_back(){
    include('tpl/call-me-back-form.php');
}


function get_page_link_by_slug($page_slug) {
  $page = get_page_by_path($page_slug);
  if ($page) :
    return get_permalink( $page->ID );
  else :
    return "#";
  endif;
}


function ivp_call_me_back_form() {
    require_once('tpl/call-me-back-form-process.php');
}
