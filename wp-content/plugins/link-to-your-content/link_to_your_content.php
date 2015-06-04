<?php
	/*
		Plugin Name: Link to Your Content
		Plugin URI: http://www.maymond.com/link-to-your-content
		Description: This plugin gives you an easy way to link to your media library, posts, pages, custom post types and taxonomies.
		Version: 1.8.3
		Author: Ray Milstrey
		Author URI: http://www.maymond.com
		Text Domain: link-to-your-content
	  
	    Copyright 2011 Ray Milstrey (email: wp@maymond.com)
	
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.
	
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
	
		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/
	
	// Stop direct call
	if(preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	
	// define paths
	define('LTYCDLFOLDER', plugin_basename(dirname(__FILE__)));
	define('LTYCDLPLUGINPATH', WP_PLUGIN_URL.'/'.LTYCDLFOLDER);
	
	if(!class_exists('LinkToYourContent')) {
		class LinkToYourContent {
			var $pluginName = 'LinkToYourContent';
			var $optionsHook = 'link-to-your-content-options';
			var $pluginFile = 'link-to-your-content/link_to_your_content.php';
			var $page = 1;
			var $pageOffset, $queryBlog, $currentBlog, $customPostType, $taxonomy, $ltycOptions;
		
			// Initialization function for when the plugin is first activated
			function ltyc_activate () {
				global $wpdb; global $ltyc_db_version;
				// Set the database version number
				$ltyc_db_version = "1.8.3";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
				// Add all the default options without the multisite toggle
				if(is_plugin_active($this->pluginFile)) {
				    wp_redirect(plugins_url('network_activation.php', __FILE__));

				    echo '<p><strong>Link to Your Content Error:</strong> plugin cannot be activated network-wide as it is already active on an individual site.</p>';
				    echo '<p><a href="javascript:history.back(-1);">Back</a>';
				    exit();
				}
				else {
					$ltyc_options = array('ltyc_db_version' => $ltyc_db_version, 'ltyc_taxonomy' => 0, 'ltyc_custom_post_type' => 0, 'ltyc_multi_site' => 0, 'ltyc_pagination' => 20);
					add_option('ltyc_options', $ltyc_options);
				}
			}
			
			// Function to be run when the plugin is deactivated
			function ltyc_deactivate() {
				delete_option('ltyc_options');
			}	
			
			// Function to be run when the plugin is uninstalled(deleted)
			static function ltyc_uninstall() {
				// Remove all the options
				delete_option('ltyc_options');			
			}
			
			// Function to be run checking for whether or not the new pagintion value has been set
			function ltyc_update() {
				global $wpdb;
				if(!function_exists('is_plugin_active_for_network')) { require_once(ABSPATH.'/wp-admin/includes/plugin.php'); }
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog(1)) {
						$this->ltycOptions = get_option('ltyc_options');
						if($this->ltycOptions['ltyc_db_version'] < '1.5.1' || !isset($this->ltycOptions['ltyc_pagination'])) {
							$this->ltyc_options['ltyc_pagination'] = 20;
							update_option('ltyc_options', $this->ltycOptions);
						}
						restore_current_blog();
					}
				}
				else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) {
						$this->ltycOptions = get_option('ltyc_options');
						if($this->ltycOptions['ltyc_db_version'] < '1.5.1' || !isset($this->ltycOptions['ltyc_pagination'])) {
							$this->ltyc_options['ltyc_pagination'] = 20;
							update_option('ltyc_options', $this->ltycOptions);
						}
						restore_current_blog();
					}
				}
				else {
					$this->ltycOptions = get_option('ltyc_options');
					if($this->ltycOptions['ltyc_db_version'] < '1.5.1' || !isset($this->ltycOptions['ltyc_pagination'])) {
						$this->ltyc_options['ltyc_pagination'] = 20;
						update_option('ltyc_options', $this->ltycOptions);
					}
				}
			}

			// Function for loading the translations
			function ltyc_lang() {
				load_plugin_textdomain('link-to-your-content', false, LTYCDLFOLDER.'/languages/');
			}		
		
			// Function for adding the options network menu
			function ltyc_network_menu() {
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					add_submenu_page( 'settings.php', __('Link to Your Content Options'), __('Link to Your Content'), 'manage_network', $this->optionsHook, array('LinkToYourContent','ltyc_options') );
				}
			}		
		
			// Function for adding the options menu
			function ltyc_menu() {
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) { return true; }
				else {
					add_options_page(__('Link to Your Content Options'), __('Link to Your Content'), 'moderate_comments', $this->optionsHook, array('LinkToYourContent','ltyc_options'));
				}
			}
			
			// Add the settings link to the plugin admin page
			function ltyc_plugin_link($links, $file) {
				static $this_plugin;
				if(empty($this_plugin)) $this_plugin = $this->pluginFile;
				if($file == $this_plugin) {
					$settings_link = sprintf(__('<a href="%s">Settings</a>', 'link-to-your-content'), admin_url('options-general.php?page='.$this->optionsHook));
					array_unshift($links, $settings_link);
				}
				return $links;
			}
			
			// Add the settings link to the plugin admin page when it has been network activated
			function ltyc_network_plugin_link($links, $file) {
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					static $this_plugin;
					if(empty($this_plugin)) $this_plugin = $this->pluginFile;
					if($file == $this_plugin) {
						$settings_link = sprintf(__('<a href="%s">Settings</a>', 'link-to-your-content'), admin_url('network/settings.php?page='.$this->optionsHook));
						array_unshift($links, $settings_link);
					}
					return $links;
				}
			}
			
			// Includes the options page
			function ltyc_options() { require_once('ltyc_options.php'); }
			
			//register our settings
			function ltyc_register_settings() {
				register_setting( 'ltyc-settings-group', 'ltyc_options', array('LinkToYourContent','ltyc_options_validate') );
			}
			
			// Sanitize and validate input. Accepts an array, return a sanitized array.
			function ltyc_options_validate($input) {
				$input['ltyc_db_version'] = '1.8.3';
				// Our values are either a 0 or 1
				$input['ltyc_taxonomy'] = ( $input['ltyc_taxonomy'] == 1 ? 1 : 0 );
				$input['ltyc_custom_post_type'] = ( $input['ltyc_custom_post_type'] == 1 ? 1 : 0 );
				// Check to see if multi-site is enabled
				if(function_exists('is_multisite') && is_multisite()) {
					$input['ltyc_multi_site'] = ( $input['ltyc_multi_site'] == 1 ? 1 : 0 );
				}
				$input['ltyc_pagination'] = ( (strlen(trim($input['ltyc_pagination'])) > 0 && ctype_digit($input['ltyc_pagination'])) ? $input['ltyc_pagination'] : '20' );
				
				return $input;
			}
		
			// Add the new TinyMCE button if the user is allowed to see it
			function ltyc_initialize() {
				// Don't bother doing this stuff if the current user lacks permissions
				if(!current_user_can('edit_posts') && !current_user_can('edit_pages')) { return; }
								
				// Add only in Rich Editor mode
				if(get_user_option('rich_editing') == 'true') {
					global $tinymce_version;
					if(version_compare($tinymce_version, '4018') >= 0 ) { 
						add_filter('mce_external_plugins', array(&$this, 'ltyc_tinymce_plugin_4'));
					} else {
						add_filter('mce_external_plugins', array(&$this, 'ltyc_tinymce_plugin'));
					}
					add_filter('mce_buttons', array(&$this, 'ltyc_register_button'));
					add_filter('mce_external_languages', array(&$this, 'ltyc_mce_languages'));
				}
			}
			 
			// Register the new TinyMCE button
			function ltyc_register_button($buttons) {
				array_push($buttons, 'separator', $this->pluginName);
				return $buttons;
			}
			 
			// Load the TinyMCE 3.x plugin
			function ltyc_tinymce_plugin($mce_plugins) {
				$mce_plugins[$this->pluginName] = LTYCDLPLUGINPATH.'/editor_plugin.js';
				return $mce_plugins;
			}

			// Load the TinyMCE 4.x plugin
			function ltyc_tinymce_plugin_4($mce_plugins) {
				$mce_plugins[$this->pluginName] = LTYCDLPLUGINPATH.'/plugin.js';
				return $mce_plugins;
			}

			// Set languages for all tinymce addons
			function ltyc_mce_languages($mce_external_languages) {
			    $mce_external_languages[$this->pluginName] = LTYCDLPLUGINPATH.'/mce_langs.php';
			    return $mce_external_languages;
			}

			// Add the CSS file for the dashicon
			function ltyc_enqueue() {
				global $wp_version;
				if(version_compare($wp_version, '3.8') >= 0 ) {
			   		wp_enqueue_style('ltyc-dashicon', LTYCDLPLUGINPATH.'/css/ltyc_icons.css');
				}
			}
			
			// Panel display
			function ltyc_panel_display($type='posts') {
				$panel_display = '';
				if($type == 'cptypes') { $panel_display .= $this->ltyc_cpt_toggle(); }
				else if($type == 'taxonomies') { $panel_display .= $this->ltyc_tax_toggle(); }
				if($type != 'external') {
					$panel_display .= $this->ltyc_pagination($type);
					$panel_display .= '<div class="clear">&nbsp;</div>';
					$panel_display .= '<div class="documentListWrap">';
					$panel_display .= '<table class="striped" border="0" cellpadding="3" cellspacing="0">';
					//if($type == 'search') { $panel_display .= $this->ltyc_search_panel(); }
					/*else {*/ $panel_display .= $this->ltyc_document_list($type, $this->page, $this->pageOffset); //}
					$panel_display .= '</table>';
					$panel_display .= '</div>';
				}
				else {
					$panel_display .= '<div class="clear">&nbsp;</div>';
					$panel_display .= '<div class="linkInformationWrap">';
					$panel_display .= '<input type="text" name="externalLink" id="externalLink" value="http://" size="60" /><br />';
					$panel_display .= __('External URL', 'link-to-your-content').': <input type="radio" name="externalLinkType" value="http" class="ltycRadio marginRight25" checked="checked" />';
					$panel_display .= __('E-mail Address', 'link-to-your-content').': <input type="radio" name="externalLinkType" value="mailto" class="ltycRadio" />';
					$panel_display .= '</div>';
				}
				$panel_display .= '<table border="0" cellpadding="2" cellspacing="0">';
				$panel_display .= '<tr>';
				$panel_display .= '<td><label for="'.$type.'_linkTitle">'.__('Title', 'link-to-your-content').':</label><br /><input type="text" id="'.$type.'_linkTitle" name="'.$type.'_linkTitle" value="" size="60" /></td>';
				$panel_display .= '</tr>';
				$panel_display .= '<tr>';
				$panel_display .= '<td><label for="'.$type.'_linkTarget">'.__('Target', 'link-to-your-content').':</label><br />';
				$panel_display .= '<select name="'.$type.'_linkTarget" id="'.$type.'_linkTarget">';
				$panel_display .= '<option value="_self">'.__('Open in the same window', 'link-to-your-content').'</option>';
				$panel_display .= '<option value="_blank">'.__('Open in a new window', 'link-to-your-content').'</option>';
				$panel_display .= '</select>';
				$panel_display .= '</td>';
				$panel_display .= '</tr>';
				$panel_display .= '</table>';
				
				return $panel_display;
			}

			/*function ltyc_search_panel() {
				global $wpdb;
				$this->queryBlog = $wpdb->prefix; $this->currentBlog = $wpdb->blogid;
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog(1)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else { $this->ltycOptions = get_option('ltyc_options'); }

				$search_panel = '<tr><td>';
				if(function_exists('is_multisite') && is_multisite() && isset($this->ltycOptions) && $this->ltycOptions['ltyc_multi_site'] == 1) {
					$search_panel .= '<p><label for="siteSearch">'.__('Where to search', 'link-to-your-content').':</label> <select name="siteSearch" id="siteSearch">';
					$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
					foreach($blogids as $blog_id) {
						switch_to_blog($blog_id);
						$search_panel .= '<option value="'.$blog_id.'"';
						if($blog_id == $this->currentBlog) { $search_panel .= ' selected="selected"'; }
						$search_panel .= '>'.get_option('blogname').'</option>'."\n";
						restore_current_blog();
					}
					$search_panel .= '</select></p>';
				}
				$search_panel .= '<p><label for="typeSearch">'.__('What to search', 'link-to-your-content').':</label> <select name="typeSearch" id="typeSearch">';
				$search_panel .= '<option value="core">'.__('Posts/Pages/Custom Post Types', 'link-to-your-content').'</option>';
				if(function_exists('get_taxonomies') && isset($this->ltycOptions) && $this->ltycOptions['ltyc_taxonomy'] == 1) {
					$search_panel .= '<option value="tax">'.__('Taxonomies', 'link-to-your-content').'</option>';
				}
				$search_panel .= '<option value="media">'.__('All Media', 'link-to-your-content').'</option>';
				$search_panel .= '</select></p>';
				$search_panel .= '<p><label for="keyword">'.__('Search', 'link-to-your-content').':</label> <input type="text" name="keyword" id="keyword" value="" /></p>';
				$search_panel .= '<p><input type="submit" name="submit" value="'.__('Search', 'link-to-your-content').'" /></p>';
				$search_panel .= __('Search results for:', 'link-to-your-content');
				$search_panel .= __('No matches found.', 'link-to-your-content');
				$search_panel .= __('Search again', 'link-to-your-content');
				$search_panel .= '</td></tr>';

				return $search_panel;
			}*/
			
			// Toggle between sites in the network
			function ltyc_site_toggle() {
				global $wpdb; $siteToggle = '';
				$this->queryBlog = $wpdb->prefix; $this->currentBlog = $wpdb->blogid;
				if(!function_exists('is_plugin_active_for_network')) { require_once(ABSPATH.'/wp-admin/includes/plugin.php'); }
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog(1)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				if(function_exists('is_multisite') && is_multisite() && isset($this->ltycOptions) && $this->ltycOptions['ltyc_multi_site'] == 1) {
					$siteToggle .= '<div class="multisiteToggle">'."\n";
					$siteToggle .= '<p><label for="siteToggle">'.__('Choose Site', 'link-to-your-content').':</label> <select name="siteToggle" id="siteToggle" size="1">'."\n";
					$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
					foreach($blogids as $blog_id) {
						switch_to_blog($blog_id);
						$siteToggle .= '<option value="'.$blog_id.'"';
						if($blog_id == $this->currentBlog) { $siteToggle .= ' selected="selected"'; }
						$siteToggle .= '>'.get_option('blogname').'</option>'."\n";
						restore_current_blog();
					}
					$siteToggle .= '</select></p>'."\n";
					$siteToggle .= '</div>'."\n";
				}
				return $siteToggle;
			}
			
			// Content type tabs
			function ltyc_content_tabs() {
				global $wpdb; $contentTabs = '';
				if(!function_exists('is_plugin_active_for_network')) { require_once(ABSPATH.'/wp-admin/includes/plugin.php'); }
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog(1)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else { $this->ltycOptions = get_option('ltyc_options'); }
				
				$contentTabs .= '<ul>'."\n";
				$contentTabs .= '<li id="posts_tab" class="current"><span><a href="javascript:mcTabs.displayTab(\'posts_tab\',\'posts_panel\');" onMouseDown="return false;">'.__('Posts', 'link-to-your-content').'</a></span></li>'."\n";
				$contentTabs .= '<li id="pages_tab"><span><a href="javascript:mcTabs.displayTab(\'pages_tab\',\'pages_panel\');" onMouseDown="return false;">'.__('Pages', 'link-to-your-content').'</a></span></li>'."\n";
				if(function_exists('get_post_types') && isset($this->ltycOptions) && $this->ltycOptions['ltyc_custom_post_type'] == 1) {
					$post_types = $wpdb->get_col("SELECT DISTINCT(post_type) FROM ".$wpdb->prefix."posts WHERE post_type != 'post' AND post_type != 'page' AND post_type != 'revision' AND post_type != 'attachment' AND post_type != 'nav_menu_item' AND post_status = 'publish'");	
					if($post_types) {
						$contentTabs .= '<li id="cptypes_tab"><span><a href="javascript:mcTabs.displayTab(\'cptypes_tab\',\'cptypes_panel\');" onMouseDown="return false;">'.__('Custom Post Types', 'link-to-your-content').'</a></span></li>'."\n";
					}
				}
				if(function_exists('get_taxonomies') && isset($this->ltycOptions) && $this->ltycOptions['ltyc_taxonomy'] == 1) {
					$contentTabs .= '<li id="taxonomies_tab"><span><a href="javascript:mcTabs.displayTab(\'taxonomies_tab\',\'taxonomies_panel\');" onMouseDown="return false;">'.__('Taxonomies', 'link-to-your-content').'</a></span></li>'."\n";
				}
				$contentTabs .= '<li id="docs_tab"><span><a href="javascript:mcTabs.displayTab(\'docs_tab\',\'docs_panel\');" onMouseDown="return false;">'.__('Docs', 'link-to-your-content').'</a></span></li>'."\n";
				$contentTabs .= '<li id="images_tab"><span><a href="javascript:mcTabs.displayTab(\'images_tab\',\'images_panel\');" onMouseDown="return false;">'.__('Images', 'link-to-your-content').'</a></span></li>'."\n";
				$contentTabs .= '<li id="media_tab"><span><a href="javascript:mcTabs.displayTab(\'media_tab\',\'media_panel\');" onMouseDown="return false;">'.__('Media', 'link-to-your-content').'</a></span></li>'."\n";
				$contentTabs .= '<li id="external_tab"><span><a href="javascript:mcTabs.displayTab(\'external_tab\',\'external_panel\');" onMouseDown="return false;">'.__('External', 'link-to-your-content').'</a></span></li>'."\n";
				//$contentTabs .= '<li id="search_tab"><span><a href="javascript:mcTabs.displayTab(\'search_tab\',\'search_panel\');" onMouseDown="return false;">'.__('Search', 'link-to-your-content').'</a></span></li>'."\n";
				$contentTabs .= '</ul>'."\n";
				
				return $contentTabs;
			}
			
			// Toggle between different custom post types
			function ltyc_cpt_toggle() {
				global $wpdb;
				$cptSelect = ''; $loopCounter = 0;
				if(!function_exists('is_plugin_active_for_network')) { require_once(ABSPATH.'/wp-admin/includes/plugin.php'); }
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog(1)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else { $this->ltycOptions = get_option('ltyc_options'); }

				$cptSelect .= '<p class="entryTypeToggle">'.__('Choose Custom Post Type', 'link-to-your-content').': <select name="cptToggle" id="cptToggle" size="1">'."\n";
				$post_types = $wpdb->get_col("SELECT DISTINCT(post_type) FROM ".$wpdb->prefix."posts WHERE post_type != 'post' AND post_type != 'page' AND post_type != 'revision' AND post_type != 'attachment' AND post_type != 'nav_menu_item' AND post_status = 'publish'");
								
				foreach($post_types as $post_type) {					
					if($loopCounter == 0 && !isset($this->customPostType)) { $this->customPostType = $post_type; }
					$cptObj = get_post_type_object($post_type);
					if(!is_object($cptObj)) {
						$cptObj = new stdClass();
						$cptObj->labels = new stdClass();
						$cptObj->labels->name = ucfirst($post_type);
					}

					$cptSelect .= '<option value="'.$post_type.'~'.$wpdb->blogid.'"';
					if($post_type == $this->customPostType) { $cptSelect .= ' selected="selected"'; }
					$cptSelect .= '>'.$cptObj->labels->name.'</option>'."\n";
						
					$loopCounter++;
				}	
				
				$cptSelect .= '</select></p>'."\n";

				return $cptSelect;
			}	
			
			// Toggle between different taxonomies
			function ltyc_tax_toggle() {
				global $wpdb;
				$taxSelect = ''; $loopCounter = 0;
				if(!function_exists('is_plugin_active_for_network')) { require_once(ABSPATH.'/wp-admin/includes/plugin.php'); }
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog(1)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else { $this->ltycOptions = get_option('ltyc_options'); }
				
				$taxSelect .= '<p class="entryTypeToggle">'.__('Choose Taxonomy', 'link-to-your-content').': <select name="taxToggle" id="taxToggle" size="1">'."\n";
				$taxonomies = $wpdb->get_col("SELECT DISTINCT(taxonomy) FROM ".$wpdb->prefix."term_taxonomy WHERE taxonomy != 'link_category'");
				
				foreach($taxonomies as $taxonomy) {					
					if($loopCounter == 0 && !isset($this->taxonomy)) { $this->taxonomy = $taxonomy; }
					$taxObj = get_taxonomy($taxonomy);
					if(!is_object($taxObj)) {
						$taxObj = new stdClass();
						$taxObj->labels = new stdClass();
						$taxObj->labels->name = ucfirst($taxonomy);
					}

					$taxSelect .= '<option value="'.$taxonomy.'~'.$wpdb->blogid.'"';
					if($taxonomy == $this->taxonomy) { $taxSelect .= ' selected="selected"'; }
					$taxSelect .= '>'.$taxObj->labels->name.'</option>'."\n";
						
					$loopCounter++;
				}	
				
				$taxSelect .= '</select></p>'."\n";

				return $taxSelect;
			}
			
			// Switch the active site view
			function ltyc_site_switch() {
				check_ajax_referer("ltyc_change_active_network_site");
				global $wpdb; $json_response = array();
				if(!function_exists('is_plugin_active_for_network')) { require_once(ABSPATH.'/wp-admin/includes/plugin.php'); }
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog(1)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}

				if(isset($_POST['ajax_site']) && trim($_POST['ajax_site']) != '' && ctype_digit($_POST['ajax_site'])) {
					if(function_exists('switch_to_blog') && switch_to_blog($_POST['ajax_site']) === true) {
						$this->queryBlog = $wpdb->prefix;
						$this->currentBlog = $wpdb->blogid;
						$json_response['posts_panel'] = $this->ltyc_panel_display('posts');
						$json_response['pages_panel'] = $this->ltyc_panel_display('pages');
						if(function_exists('get_post_types') && isset($this->ltycOptions) && $this->ltycOptions['ltyc_custom_post_type'] == 1) {
							$json_response['cptypes_panel'] = $this->ltyc_panel_display('cptypes');
						}
						if(function_exists('get_taxonomies') && isset($this->ltycOptions) && $this->ltycOptions['ltyc_taxonomy'] == 1) {
							$json_response['taxonomies_panel'] = $this->ltyc_panel_display('taxonomies');
						}
						$json_response['docs_panel'] = $this->ltyc_panel_display('docs');
						$json_response['images_panel'] = $this->ltyc_panel_display('images');
						$json_response['media_panel'] = $this->ltyc_panel_display('media');
						$json_response['content_tabs'] = $this->ltyc_content_tabs();
						$json_response['ajax_site'] = $_POST['ajax_site'];
						$json_response['error'] = '0';
					}
					else { $json_response['error'] = '2'; die(json_encode($json_response)); }
				} else { $json_response['error'] = '1'; die(json_encode($json_response)); }
				
				die(json_encode($json_response));	
			}	
			
			// Pagination Control
			function ltyc_page_switch() {
				check_ajax_referer("ltyc_change_active_type_page");
				global $wpdb; $json_response = array();
			
				if(isset($_POST['ajax_page']) && trim($_POST['ajax_page']) != '' && ctype_digit($_POST['ajax_page']) && isset($_POST['ajax_type']) && 
				trim($_POST['ajax_type']) != '') {
					if(!function_exists('is_plugin_active_for_network')) { require_once(ABSPATH.'/wp-admin/includes/plugin.php'); }
					if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
						if(function_exists('switch_to_blog') && switch_to_blog(1)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
					}
					else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
						if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
					}
					else { $this->ltycOptions = get_option('ltyc_options'); }

					$this->page = $_POST['ajax_page'];
					if(function_exists('switch_to_blog') && switch_to_blog($_POST['ajax_site']) === true) {
						$this->queryBlog = $wpdb->prefix;
						$this->currentBlog = $wpdb->blogid;
					}
					else { $this->queryBlog = $wpdb->prefix; }

					switch($_POST['ajax_type']) {
						case 'posts' :
							$json_response['posts_panel'] = $this->ltyc_panel_display('posts');
							break;
						case 'pages' :
							$json_response['pages_panel'] = $this->ltyc_panel_display('pages');
							break;
						case 'cptypes' :
							if(function_exists('get_post_types') && isset($this->ltycOptions) && $this->ltycOptions['ltyc_custom_post_type'] == 1) {
								$this->customPostType = $_POST['ajax_cpt'];
								$json_response['cptypes_panel'] = $this->ltyc_panel_display('cptypes');
							}
							break;
						case 'taxonomies' :
							if(function_exists('get_taxonomies') && isset($this->ltycOptions) && $this->ltycOptions['ltyc_taxonomy'] == 1) {
								$this->taxonomy = $_POST['ajax_tax'];
								$json_response['taxonomies_panel'] = $this->ltyc_panel_display('taxonomies');
							}
							break;
						case 'docs' :
							$json_response['docs_panel'] = $this->ltyc_panel_display('docs');
							break;
						case 'images' :
							$json_response['images_panel'] = $this->ltyc_panel_display('images');
							break;
						case 'media' :
							$json_response['media_panel'] = $this->ltyc_panel_display('media');
							break;
						default : 
							$json_response['posts_panel'] = $this->ltyc_panel_display('posts');
							break;
					}
					$json_response['ajax_type'] = $_POST['ajax_type'];
					$json_response['error'] = '0';
				} else { $json_response['error'] = '1'; die(json_encode($json_response)); }
				
				die(json_encode($json_response));
			}
			
			// Switch the active custom post type or taxonomy view
			function ltyc_panel_switch() {
				check_ajax_referer("ltyc_change_active_site_panel");
				global $wpdb; $json_response = array();
				switch($_POST['ajax_type']) {
					case 'cptypes' : 					
						if(isset($_POST['ajax_cpt']) && trim($_POST['ajax_cpt']) != '') {
							$cptTilde = strpos($_POST['ajax_cpt'], '~');
							$this->customPostType = substr($_POST['ajax_cpt'], 0, $cptTilde);
							$this->currentBlog = substr($_POST['ajax_cpt'], ($cptTilde + 1));
							if(function_exists('switch_to_blog') && switch_to_blog($this->currentBlog)) {
								$this->queryBlog = $wpdb->prefix;
								$json_response['cptypes_panel'] = $this->ltyc_panel_display('cptypes');
								restore_current_blog();
							}
							else {
								$this->queryBlog = $wpdb->prefix;
								$json_response['cptypes_panel'] = $this->ltyc_panel_display('cptypes');
							}
							
						} else { $json_response['error'] = '1'; die(json_encode($json_response)); }
						break;
					case 'taxonomies' : 					
						if(isset($_POST['ajax_tax']) && trim($_POST['ajax_tax']) != '') {
							$taxTilde = strpos($_POST['ajax_tax'], '~');
							$this->taxonomy = substr($_POST['ajax_tax'], 0, $taxTilde);
							$this->currentBlog = substr($_POST['ajax_tax'], ($taxTilde + 1));
							if(function_exists('switch_to_blog') && switch_to_blog($this->currentBlog)) {
								$this->queryBlog = $wpdb->prefix;
								$json_response['taxonomies_panel'] = $this->ltyc_panel_display('taxonomies');
								restore_current_blog();
							}
							else {
								$this->queryBlog = $wpdb->prefix;
								$json_response['taxonomies_panel'] = $this->ltyc_panel_display('taxonomies');
							}
							
						} else { $json_response['error'] = '1'; die(json_encode($json_response)); }
						break;
					default : 
						$json_response['error'] = '2'; die(json_encode($json_response));
						break;
				}
				die(json_encode($json_response));
			}	
		
			// Creates the page heirarchy output
			function ltyc_page_heirarchy_output($entryId, $type) {
				global $wpdb; $documentSubList = '';
				switch($type) {
					case 'pages' :
						$queryPostType = 'page';
						break;
					case 'cptypes' :
						$queryPostType = $this->customPostType;
						break;
					default :
						$queryPostType = 'page';
						break;
				}
				if($type == 'taxonomies') {
					$subEntries = 'SELECT term_id, name, slug FROM '.$this->queryBlog.'terms NATURAL JOIN '.$this->queryBlog.'term_taxonomy WHERE '.$this->queryBlog.'term_taxonomy.parent = '.$entryId.' ORDER BY name ASC';
				}
				else {
					$subEntries = 'SELECT post_title, guid, ID, post_mime_type FROM '.$this->queryBlog.'posts WHERE post_type = "'.$queryPostType.'" AND post_status = "publish" AND post_parent = '.$entryId.' ORDER BY menu_order ASC';
				}
				$subEntryResults = $wpdb->get_results($subEntries, ARRAY_A);
				if($wpdb->num_rows > 0) {
					foreach($subEntryResults as $subEntry) {	
						if($type == 'taxonomies') {
							if(taxonomy_exists($this->taxonomy) === true) {
								$currentLevel = count(get_ancestors($subEntry['term_id'], $this->taxonomy));
								$subEntryTitle = $subEntry['name'];
								$subEntryId = $subEntry['term_id'];
								$subEntryPermalink = get_term_link($subEntry['slug'], $this->taxonomy);
							}
							else {
								$currentLevel = $this->ltyc_tax_ancestors($subEntry['term_id']);
								$subEntryTitle = $subEntry['name'];
								$subEntryId = $subEntry['term_id'];
								if(function_exists('switch_to_blog') && switch_to_blog($this->currentBlog)) {
									$subEntryPermalink = get_bloginfo('url').'/?'.$this->taxonomy.'='.$subEntry['slug'];
									restore_current_blog();
								}
								else {
									$subEntryPermalink = get_bloginfo('url').'/?'.$this->taxonomy.'='.$subEntry['slug'];
								}
							}
						}
						else {
							$currentLevel = count(get_post_ancestors($subEntry['ID']));
							$subEntryTitle = $subEntry['post_title'];
							$subEntryId = $subEntry['ID'];
							$subEntryPermalink = get_permalink($subEntryId);
						}
						$documentSubList .= '<tr>'."\n";
						$documentSubList .= '<td width="'.(480 - ($currentLevel * 10)).'" style="padding-left: '.($currentLevel * 10).'px;">';
						for($i=0;$i<$currentLevel;$i++) {
							$documentSubList .= '&ndash;';
						}
						$documentSubList .= ' '.$subEntryTitle.'</td>'."\n";		
						$documentSubList .= '<td width="85"><a href="'.$subEntryPermalink.'" title="'.$subEntryTitle.'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
						$documentSubList .= '</tr>'."\n";
						$documentSubList .= $this->ltyc_page_heirarchy_output($subEntryId, $type);
					}
					return $documentSubList;
				}
			}

			// Recursive function for getting the top ancestor for taxonomies
			function ltyc_tax_ancestors($term_id, &$currentLevel = 0) {
				global $wpdb;
				$ancestorCheck = $wpdb->prepare('SELECT parent FROM '.$this->queryBlog.'term_taxonomy WHERE term_id = %d', $term_id);
				$ancestor = $wpdb->get_var($ancestorCheck);
				if($ancestor != 0) {
					$currentLevel++;
					$ancestorRecursion = $this->ltyc_tax_ancestors($ancestor, $currentLevel);
				}
				return $currentLevel;
			}
			
			// Pagination control when viewing documents lists
			function ltyc_pagination($type) {
				global $wpdb; $cpt_page_link = ''; $tax_page_link = '';
				if(!function_exists('is_plugin_active_for_network')) { require_once(ABSPATH.'/wp-admin/includes/plugin.php'); }
				if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog(1)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else if(function_exists('is_multisite') && is_multisite() && function_exists('is_plugin_active_for_network') && !is_plugin_active_for_network($this->pluginFile)) {
					if(function_exists('switch_to_blog') && switch_to_blog($wpdb->blogid)) { $this->ltycOptions = get_option('ltyc_options'); restore_current_blog(); }
				}
				else { $this->ltycOptions = get_option('ltyc_options'); }
				// Get the list of documents count based on the current view
				if($type == 'posts') {
					$queryCount = 'SELECT ID FROM '.$this->queryBlog.'posts WHERE post_type = "post" AND post_status = "publish"';
					$paginationTitle = __('Select Post', 'link-to-your-content').':';
				} else if($type == 'pages') {
					$queryCount = 'SELECT ID FROM '.$this->queryBlog.'posts WHERE post_type = "page" AND post_status = "publish"';
					$paginationTitle = __('Select Page', 'link-to-your-content').':';
				} else if($type == 'cptypes') {
					$queryCount = 'SELECT ID FROM '.$this->queryBlog.'posts WHERE post_type = "'.$this->customPostType.'" AND post_status = "publish"';
					if(post_type_exists($this->customPostType) === true) {
						$cpt_obj = get_post_type_object($this->customPostType);
						$paginationTitle = __('Select', 'link-to-your-content').' '.$cpt_obj->labels->singular_name.':';
						$cpt_hierarchical = $cpt_obj->hierarchical;
						$cpt_page_link = '&cpt='.$this->customPostType;
					}
					else {
						$paginationTitle = __('Select', 'link-to-your-content').' '.ucfirst($this->customPostType).':';
						$entriesTest = 'SELECT post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "'.$this->customPostType.'" AND post_status = "publish" ORDER BY menu_order ASC';
						$entryTest = $wpdb->get_results($entriesTest, ARRAY_A);
						$hierarchical = 0;
						foreach($entryTest as $entry) {
							if($entry['post_parent'] != 0) { $hierarchical = 1; }
						}
						if($hierarchical == 1) { $cpt_hierarchical = 1; }
						else { $cpt_hierarchical = 0; }
						$cpt_page_link = '&cpt='.$this->customPostType;
					}
				} else if($type == 'taxonomies') {
					$queryCount = 'SELECT term_id FROM '.$this->queryBlog.'terms NATURAL JOIN '.$this->queryBlog.'term_taxonomy WHERE '.$this->queryBlog.'term_taxonomy.taxonomy = "'.$this->taxonomy.'"';
					if(taxonomy_exists($this->taxonomy) === true) {
						$tax_obj = get_taxonomy($this->taxonomy);
						$paginationTitle = __('Select', 'link-to-your-content').' '.$tax_obj->labels->singular_name.':';	
						$tax_hierarchical = $tax_obj->hierarchical;
						$tax_page_link = '&tax='.$this->taxonomy;
					}
					else {
						$paginationTitle = __('Select', 'link-to-your-content').' '.ucfirst($this->taxonomy).':';
						$entriesTest = 'SELECT parent FROM '.$this->queryBlog.'terms NATURAL JOIN '.$this->queryBlog.'term_taxonomy WHERE '.$this->queryBlog.'term_taxonomy.taxonomy = "'.$this->taxonomy.'" ORDER BY name ASC';
						$entryTest = $wpdb->get_results($entriesTest, ARRAY_A);
						$hierarchical = 0;
						foreach($entryTest as $entry) {
							if($entry['parent'] != 0) { $hierarchical = 1; }
						}
						if($hierarchical == 1) { $tax_hierarchical = 1; }
						else { $tax_hierarchical = 0; }
						$tax_page_link = '&tax='.$this->taxonomy;
					}
				} else if($type == 'docs') {
					$queryCount = 'SELECT ID FROM '.$this->queryBlog.'posts WHERE post_type = "attachment" AND (SUBSTRING(post_mime_type, 1, 11) = "application" OR SUBSTRING(post_mime_type, 1, 4) = "text")';
					$paginationTitle = __('Select Document', 'link-to-your-content').':';
				} else if($type == 'images') {
					$queryCount = 'SELECT ID FROM '.$this->queryBlog.'posts WHERE post_type = "attachment" AND SUBSTRING(post_mime_type, 1, 5) = "image"';
					$paginationTitle = __('Select Image', 'link-to-your-content').':';
				} else if($type == 'media') {
					$queryCount = 'SELECT ID FROM '.$this->queryBlog.'posts WHERE post_type = "attachment" AND (SUBSTRING(post_mime_type, 1, 11) != "application" AND SUBSTRING(post_mime_type, 1, 4) != "text" AND SUBSTRING(post_mime_type, 1, 5) != "image")';
					$paginationTitle = __('Select Media', 'link-to-your-content').':';
				} else {
					$queryCount = 'SELECT ID FROM '.$this->queryBlog.'posts WHERE post_type = "post" AND post_status = "publish"';
					$paginationTitle = __('Select Entry', 'link-to-your-content').':';
				} $queryResults = $wpdb->get_results($queryCount, ARRAY_A); $totalResults = $wpdb->num_rows;					
				$this->pageOffset = $this->ltycOptions['ltyc_pagination']; //25
				$pageCount = ceil($totalResults / $this->pageOffset);
				$paginationDisplay = '';
				
				if(isset($this->page) && ctype_digit($this->page) && $this->page >= 1 && $this->page > $pageCount) { $this->page = $pageCount; }
				$site_link = '&site='.$this->currentBlog;
				
				if(($pageCount > 1 && $type != 'pages' && $type != 'cptypes' && $type != 'taxonomies') || ($pageCount > 1 && $type == 'cptypes' && $cpt_hierarchical == 0) || ($pageCount > 1 && $type == 'taxonomies' && $tax_hierarchical == 0)) {
					$paginationDisplay .= '<p class="pagination">';
					if($this->page <= 1) { $prevPage = 1; }
					else { $prevPage = $this->page - 1; }
					if($this->page > 1) {
						$paginationDisplay .= '<a href="?page=1&type='.$type.$site_link.$cpt_page_link.$tax_page_link.'">&#8249;</a>';
						$paginationDisplay .= '<a href="?page='.$prevPage.'&type='.$type.$site_link.$cpt_page_link.$tax_page_link.'">&laquo;</a>';
					}
					
					if($pageCount > 4) {
						$paginationLimit = ($this->page + 3);
						if($paginationLimit < $pageCount) {
							for($dot=$this->page;$dot<=$paginationLimit;$dot++) {
								$paginationDisplay .= '<a href="?page='.$dot.'&type='.$type.$site_link.$cpt_page_link.$tax_page_link.'"';
								if($dot == $this->page) { $paginationDisplay .= ' id="currentPage"'; }
								$paginationDisplay .= '>'.$dot.'</a>';
							}
							$paginationDisplay .= ' &hellip; ';
							$paginationDisplay .= '<a href="?page='.$pageCount.'&type='.$type.$site_link.$cpt_page_link.$tax_page_link.'">'.$pageCount.'</a>';
						}
						else {
							$pageDifferential = $this->page - ($paginationLimit - $pageCount);
							for($dot=$pageDifferential;$dot<=$pageCount;$dot++) {
								$paginationDisplay .= '<a href="?page='.$dot.'&type='.$type.$site_link.$cpt_page_link.$tax_page_link.'"';
								if($dot == $this->page) { $paginationDisplay .= ' id="currentPage"'; }
								$paginationDisplay .= '>'.$dot.'</a>';
							}
						}
					}
					else {
						for($dot=0;$dot<$pageCount;$dot++) {
							$paginationDisplay .= '<a href="?page='.($dot + 1).'&type='.$type.$site_link.$cpt_page_link.$tax_page_link.'"';
							if(($dot + 1) == $this->page) { $paginationDisplay .= ' id="currentPage"'; }
							$paginationDisplay .= '>'.($dot + 1).'</a>';
						}
					}
					
					if($this->page >= $pageCount) { $nextPage = 1; }
					else { $nextPage = $this->page + 1; }
					if($this->page < $pageCount) {	
						$paginationDisplay .= '<a href="?page='.$nextPage.'&type='.$type.$site_link.$cpt_page_link.$tax_page_link.'">&raquo;</a>';
						$paginationDisplay .= '<a href="?page='.$pageCount.'&type='.$type.$site_link.$cpt_page_link.$tax_page_link.'">&#8250;</a>';
					}
					$paginationDisplay .= '</p>';
				}
				$paginationDisplay .= '<p class="documentHeader"><strong>'.$paginationTitle.'</strong></p>';
				// Return the pagination display to the document list popup
				return $paginationDisplay;
			}
			
			// Outputs the list of documents that have been uploaded
			function ltyc_document_list($type, $page, $pageOffset) {
				global $wpdb; $documentList = ''; global $wp_post_types;
				// Get the list of documents based on the current view
				if($type == 'posts') {
					$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "post" AND post_status = "publish" ORDER BY post_date DESC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
				} else if($type == 'pages') {
					$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "page" AND post_status = "publish" AND post_parent = 0 ORDER BY menu_order ASC';
				} else if($type == 'cptypes') {
					if(post_type_exists($this->customPostType) === true) {
						$cpt_obj = get_post_type_object($this->customPostType);
						$cpt_hierarchical = $cpt_obj->hierarchical;
						if($cpt_hierarchical == 0) { 
							$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "'.$this->customPostType.'" AND post_status = "publish" AND post_parent = 0 ORDER BY menu_order ASC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
						}
						else {
							$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "'.$this->customPostType.'" AND post_status = "publish" AND post_parent = 0 ORDER BY menu_order ASC';
						}
					}
					else {
						$entries = 'SELECT post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "'.$this->customPostType.'" AND post_status = "publish" ORDER BY menu_order ASC';
						$entryTest = $wpdb->get_results($entries, ARRAY_A);
						foreach($entryTest as $entry) {
							if($entry['post_parent'] != 0) { $cpt_hierarchical = true; }
						}
						if(isset($cpt_hierarchical) && $cpt_hierarchical === true) { 
							$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "'.$this->customPostType.'" AND post_status = "publish" AND post_parent = 0 ORDER BY menu_order ASC';
						}
						else {
							$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "'.$this->customPostType.'" AND post_status = "publish" AND post_parent = 0 ORDER BY menu_order ASC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
						}
					}
				} else if($type == 'taxonomies') {
					if(taxonomy_exists($this->taxonomy) === true) {
						$tax_obj = get_taxonomy($this->taxonomy);
						$tax_hierarchical = $tax_obj->hierarchical;
						if($tax_hierarchical == 0) { 
							$entries = 'SELECT term_id, name, slug, '.$this->queryBlog.'term_taxonomy.parent FROM '.$this->queryBlog.'terms NATURAL JOIN '.$this->queryBlog.'term_taxonomy WHERE '.$this->queryBlog.'term_taxonomy.taxonomy = "'.$this->taxonomy.'" AND '.$this->queryBlog.'term_taxonomy.parent = 0 ORDER BY name ASC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
						}
						else {
							$entries = 'SELECT term_id, name, slug, '.$this->queryBlog.'term_taxonomy.parent FROM '.$this->queryBlog.'terms NATURAL JOIN '.$this->queryBlog.'term_taxonomy WHERE '.$this->queryBlog.'term_taxonomy.taxonomy = "'.$this->taxonomy.'" AND '.$this->queryBlog.'term_taxonomy.parent = 0 ORDER BY name ASC';
						}
					}
					else {
						$entries = 'SELECT parent FROM '.$this->queryBlog.'term_taxonomy NATURAL JOIN '.$this->queryBlog.'terms WHERE '.$this->queryBlog.'term_taxonomy.taxonomy = "'.$this->taxonomy.'" ORDER BY name ASC';
						$entryTest = $wpdb->get_results($entries, ARRAY_A);
						foreach($entryTest as $entry) {
							if($entry['parent'] != 0) { $tax_hierarchical = true; }
						}
						if(isset($tax_hierarchical) && $tax_hierarchical === true) { 
							$entries = 'SELECT term_id, name, slug, '.$this->queryBlog.'term_taxonomy.parent FROM '.$this->queryBlog.'terms NATURAL JOIN '.$this->queryBlog.'term_taxonomy WHERE '.$this->queryBlog.'term_taxonomy.taxonomy = "'.$this->taxonomy.'" AND '.$this->queryBlog.'term_taxonomy.parent = 0 ORDER BY name ASC';
						}
						else {
							$entries = 'SELECT term_id, name, slug, '.$this->queryBlog.'term_taxonomy.parent FROM '.$this->queryBlog.'terms NATURAL JOIN '.$this->queryBlog.'term_taxonomy WHERE '.$this->queryBlog.'term_taxonomy.taxonomy = "'.$this->taxonomy.'" AND '.$this->queryBlog.'term_taxonomy.parent = 0 ORDER BY name ASC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
						}
					}
				} else if($type == 'docs') {
					$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "attachment" AND (SUBSTRING(post_mime_type, 1, 11) = "application" OR SUBSTRING(post_mime_type, 1, 4) = "text") ORDER BY post_title ASC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
				} else if($type == 'images') {
					$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "attachment" AND SUBSTRING(post_mime_type, 1, 5) = "image" ORDER BY post_title ASC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
				} else if($type == 'media') {
					$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "attachment" AND (SUBSTRING(post_mime_type, 1, 11) != "application" AND SUBSTRING(post_mime_type, 1, 4) != "text" AND SUBSTRING(post_mime_type, 1, 5) != "image") ORDER BY post_title ASC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
				} else {
					$entries = 'SELECT post_title, guid, ID, post_mime_type, post_parent FROM '.$this->queryBlog.'posts WHERE post_type = "post" AND post_status = "publish" ORDER BY post_title ASC LIMIT '.(($page * $pageOffset) - $pageOffset).', '.$pageOffset;
				} $entryResults = $wpdb->get_results($entries, ARRAY_A);

				if($wpdb->num_rows > 0) {
					if(post_type_exists($this->customPostType) === true) {
						$cpt_obj = get_post_type_object($this->customPostType);
					}
					else {
						foreach($entryResults as $entry) {
							if(is_array($entry) && isset($entry['post_parent']) && $entry['post_parent'] !== 0) { $cpt_hierarchical = true; }
						}
					}
					if(taxonomy_exists($this->taxonomy) === true) {
						$tax_obj = get_taxonomy($this->taxonomy);
					}
					else {
						foreach($entryResults as $entry) {
							if(is_array($entry) && isset($entry['parent']) && $entry['parent'] !== 0) { $tax_hierarchical = true; }
						}
					}
					if($type == 'docs' || $type == 'images' || $type == 'media') {
						if(is_array($entryResults)) {				
							foreach($entryResults as $entry) {
								$documentList .= '<tr>'."\n";
								$mimePos = strrpos($entry['guid'], ".");
								$attachmentType = strtoupper(substr($entry['guid'], ($mimePos + 1)));
								$documentList .= '<td width="600">'.$entry['post_title'].'</td>'."\n";
								$documentList .= '<td width="50">('.$attachmentType.')</td>'."\n";
								$permalink = $entry['guid'];
								$documentList .= '<td width="85"><a href="'.$permalink.'" title="'.$entry['post_title'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
								$documentList .= '</tr>'."\n";
							}
						}
					}
					else if($type == 'pages' || ($type == 'cptypes' && (isset($cpt_obj->hierarchical) && $cpt_obj->hierarchical == true) || (isset($cpt_hierarchical) && $cpt_hierarchical == true))) {
						if(is_array($entryResults)) {
							foreach($entryResults as $entry) {
								if(is_array($entry) && isset($entry['post_title']) && $entry['post_title'] !== 0) {
									$documentList .= '<tr>'."\n";
									$documentList .= '<td width="650">'.$entry['post_title'].'</td>'."\n";	
									$documentList .= '<td width="85"><a href="'.get_permalink($entry['ID']).'" title="'.$entry['post_title'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
									$documentList .= '</tr>'."\n";
									$documentList .= $this->ltyc_page_heirarchy_output($entry['ID'], $type);
								}
							}
						}					
					}
					else if($type == 'taxonomies') {
						if((isset($tax_obj->hierarchical) && $tax_obj->hierarchical == true) || (isset($tax_hierarchical) && $tax_hierarchical == true)) {
							if(is_array($entryResults)) {
								foreach($entryResults as $entry) {
									$documentList .= '<tr>'."\n";
									$documentList .= '<td width="650">'.$entry['name'].'</td>'."\n";	
									if(taxonomy_exists($this->taxonomy) === true) {
										$documentList .= '<td width="85"><a href="'.get_term_link($entry['slug'], $this->taxonomy).'" title="'.$entry['name'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
									}
									else {
										if(function_exists('switch_to_blog') && switch_to_blog($this->currentBlog)) {
											$documentList .= '<td width="85"><a href="'.get_bloginfo('url').'/?'.$this->taxonomy.'='.$entry['slug'].'" title="'.$entry['name'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
											restore_current_blog();
										}
										else {
											$documentList .= '<td width="85"><a href="'.get_bloginfo('url').'/?'.$this->taxonomy.'='.$entry['slug'].'" title="'.$entry['name'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
										}
									}
									$documentList .= '</tr>'."\n";
									$documentList .= $this->ltyc_page_heirarchy_output($entry['term_id'], $type);
								}
							}
						}
						else {	
							if(is_array($entryResults)) {			
								foreach($entryResults as $entry) {
									$documentList .= '<tr>'."\n";
									$documentList .= '<td width="650">'.$entry['name'].'</td>'."\n";
									if(taxonomy_exists($this->taxonomy) === true) {
										$documentList .= '<td width="85"><a href="'.get_term_link($entry['slug'], $this->taxonomy).'" title="'.$entry['name'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
									}
									else {
										if(function_exists('switch_to_blog') && switch_to_blog($this->currentBlog)) {
											$documentList .= '<td width="85"><a href="'.get_bloginfo('url').'/?'.$this->taxonomy.'='.$entry['slug'].'" title="'.$entry['name'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
											restore_current_blog();
										}
										else {
											$documentList .= '<td width="85"><a href="'.get_bloginfo('url').'/?'.$this->taxonomy.'='.$entry['slug'].'" title="'.$entry['name'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
										}
									}
									$documentList .= '</tr>'."\n";
								}
							}
						}
					}
					else {
						if(is_array($entryResults)) {				
							foreach($entryResults as $entry) {
								$documentList .= '<tr>'."\n";
								$documentList .= '<td width="650">'.$entry['post_title'].'</td>'."\n";
								$permalink = get_permalink($entry['ID']);
								$documentList .= '<td width="85"><a href="'.$permalink.'" title="'.$entry['post_title'].'" class="selectDocument">'.__('Select', 'link-to-your-content').'</a></td>'."\n";
								$documentList .= '</tr>'."\n";
							}
						}
					}
				}
				// Return the document list display to the document list popup
				return $documentList;
			}
		}

		if(class_exists('LinkToYourContent')) { $linkTYC = new LinkToYourContent(); }
		
		//Actions and Filters
		if(isset($linkTYC)) {
			// Called upon activation of the plugin
			register_activation_hook(__FILE__, array(&$linkTYC, 'ltyc_activate'));
			// Called upon deactivation of the plugin
			register_deactivation_hook(__FILE__, array(&$linkTYC, 'ltyc_deactivate'));
			// Called upon uninstall(deletion) of the plugin
			register_uninstall_hook(__FILE__, 'ltyc_uninstall');
			// init process for button control
			add_action('init', array(&$linkTYC, 'ltyc_initialize'));
			// Plugins loaded hook for adding the translations
			add_action('plugins_loaded', array(&$linkTYC, 'ltyc_lang'));
			// Enqueue hook for the dashicon stylesheet
			add_action('admin_enqueue_scripts', array(&$linkTYC, 'ltyc_enqueue'));
			// Add the settings link to the plugin admin page
			add_filter('plugin_action_links', array(&$linkTYC, 'ltyc_plugin_link'), 10, 2);	
			add_filter('network_admin_plugin_action_links', array(&$linkTYC, 'ltyc_network_plugin_link'), 10, 2);	
			// Hook for adding options menu
			add_action('network_admin_menu', array(&$linkTYC, 'ltyc_network_menu'));
			add_action('admin_menu', array(&$linkTYC, 'ltyc_menu'));
			// Call register settings function
			add_action('admin_init', array(&$linkTYC, 'ltyc_register_settings'));
			add_action('admin_init', array(&$linkTYC, 'ltyc_update'));
			// Add the AJAX action for when switching views
			add_action('wp_ajax_ltyc_panel_switch', array(&$linkTYC, 'ltyc_panel_switch'));
			add_action('wp_ajax_ltyc_site_switch', array(&$linkTYC, 'ltyc_site_switch'));
			add_action('wp_ajax_ltyc_page_switch', array(&$linkTYC, 'ltyc_page_switch'));
		}
	}		
?>