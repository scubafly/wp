<?php
/*
Plugin Name: Multicons
Plugin URI: http://www.doc4design.com/plugins/multicons
Description: Auto generates code for both a favicon and an apple favicon into the header of your website
Version: 2.1
Author: Doc4
Author URI: http://www.doc4design.com
*/

/******************************************************************************

Copyright 2010  Doc4 : info@doc4design.com

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The license is also available at http://www.gnu.org/copyleft/gpl.html

*********************************************************************************/


$ver= '2.1';

$gfile = dirname(__FILE__) . '/multicons.php';
/* Causing problems with some users
if(file_exists($gfile)){
unlink($gfile);
}*/

function personal_setup_menu() {
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) return;
	} else {
		global $user_level;
		get_currentuserinfo();
		if ($user_level < 8) return;
	}
	if (function_exists('add_options_page')) {
		add_options_page(__('Multicons'), __('Multicons'), 1, __FILE__, 'personal_setup_page');
	}
} 

// Install the options page
add_action('admin_menu', 'personal_setup_menu');

function personal_setup_page(){
	global $wpdb;
	if (isset($_POST['update'])) {
	    $options['global_url'] = trim($_POST['global_url'],'{}');
		$options['admin_url'] = trim($_POST['admin_url'],'{}');
	    $options['iphone_original_url'] = trim($_POST['iphone_original_url'],'{}');
	    $options['iphone_precomposed_url'] = trim($_POST['iphone_precomposed_url'],'{}');
		
		update_option('fav_url_option', $options);
		// Show a message to indicate something has happened
		echo '<div class="updated"><p>' . __('Options saved') . '</p></div>';
	} else {
		
		$options = get_option('fav_url_option');
	}
	
	?>
		
     <!-- Favion Settings -->
     <div class="wrap">
     <div class="icon32" id="icon-options-general"><br/></div><h2><?php echo __('Multicons [ Multiple Favicons ] Quick Setup'); ?></h2>

	 <form method="post" action="">
     
     <h3><?php echo __('Website Favicon'); ?></h3>
     
     <table class="form-table"><tbody>
     
     <tr><th scope="row"><?php _e('Favicon URL') ?></th>
	 <td><label><input name="global_url" type="text" id="global_url" value="<?php echo $options['global_url']; ?>" size="60" /></label><br/>
	 <?php printf(__('Indicate the absolute path to the Favicon image.<br />
					   Example: <em>http://www.yoursite.com/favicon.ico</em>')); ?>
	 </td></tr>
     
     <tr><th scope="row"><?php _e('Instructions') ?></th>
	 <td>
	 <?php printf(__('1. Name your Favicon [favicon.ico]<br />
	                   2. Place the Favicon image file in the root dirctory of your website. [same location as wp-content]')); ?>
     
     </tbody>
     </table>
     
     
     <h3><?php echo __('Dashboard Favicon'); ?></h3>
     
     <table class="form-table"><tbody>
     
     <tr><th scope="row"><?php _e('Dashboard Favicon URL') ?></th>
	 <td><label><input name="admin_url" type="text" id="admin_url" value="<?php echo $options['admin_url']; ?>" size="60" /></label><br/>
	 <?php printf(__('Indicate the absolute path to the Admin Favicon image.<br />
	                   Example: <em>http://www.yoursite.com/wp-content/favicon.ico</em>')); ?>
	 </td></tr>
     
     <tr><th scope="row"><?php _e('Instructions') ?></th>
	 <td>
	 <?php printf(__('1. Leave field blank to use the Website Favicon from above.<br/>
	 				   2. Name your Dashboard Favicon [favicon.ico]<br />
					   3. Place the Dashboard Favicon image file in the wp-content folder of your website. [same location as themes]')); ?>
     
     </tbody>
     </table>
     
     
     <h3><?php echo __('Apple Touch Original Icon'); ?></h3>
     
     <table class="form-table"><tbody>
     
     <tr><th scope="row"><?php _e('Apple Touch Original Icon URL') ?></th>
	 <td><label><input name="iphone_original_url" type="text" id="iphone_original_url" value="<?php echo $options['iphone_original_url']; ?>" size="60" /></label><br/>
	 <?php printf(__('Indicate the absolute path to the Apple Touch Original Icon image.<br />
	                   Example: <em>http://www.yoursite.com/apple-touch-icon.png</em>')); ?>
	 </td></tr>
     
     <tr><th scope="row"><?php _e('Instructions') ?></th>
	 <td>
	 <?php printf(__('1. Only use one Apple Touch Icon link (Do not add a url to both Original and Precomposed)<br />
	  				   2. Name your Apple Touch Original Icon [apple-touch-icon.png]<br />
	                   3. Place the Apple Touch Original Icon image file in the root dirctory of your website. [same location as wp-content]<br />
					   4. Rounded corners and Gloss effects are automatically applied by Apple devices searching for this icon.')); ?>
	 </td></tr>
     
     </tbody>
     </table>
     
     
     <h3><?php echo __('Apple Touch Precomposed Icon'); ?></h3>
     
     <table class="form-table"><tbody>
     
     <tr><th scope="row"><?php _e('Apple Touch Precomposed Icon URL') ?></th>
	 <td><label><input name="iphone_precomposed_url" type="text" id="iphone_precomposed_url" value="<?php echo $options['iphone_precomposed_url']; ?>" size="60" /></label><br/>
	 <?php printf(__('Indicate the absolute path to the Apple Touch Precomposed Icon image.<br />
	                   Example: <em>http://www.yoursite.com/apple-touch-icon-precomposed.png</em>')); ?>
	 </td></tr>
     
     <tr><th scope="row"><?php _e('Instructions') ?></th>
	 <td>
	 <?php printf(__('1. Only use one Apple Touch Icon link (Do not add a url to both Original and Precomposed)<br />
	 				   2. Name your Apple Touch Precomposed Icon [apple-touch-icon-precomposed.png]<br />
	                   3. Place the Apple Touch Precomposed Icon image file in the root dirctory of your website. [same location as wp-content]<br />
					   4. No effects are applied by Apple devices searching for this icon.')); ?>
	 </td></tr>
     
     </tbody>
     </table>
	
     <p class="submit">
	 <input name="update" class="button-primary" value="<?php echo _e('Save Changes');?>" type="submit" />
	 </p>

     </form> 
		       		
	 </div>

	       
	 <?php }
			   
            function GlobalFavicon() {
			$options = get_option('fav_url_option');
            
			 if(empty($options['global_url'])){
			 echo "";
			 } else {
			 echo"<link rel=\"shortcut icon\" href=\"$options[global_url]\" />\n<link rel=\"icon\" type=\"image/png\" href=\"$options[global_url]\" />\n";
			 }
            } 
			add_action('wp_head', 'GlobalFavicon');
			

            function Dashboardfavicon() {
            $options = get_option('fav_url_option');
			 // Decide what to do if the dashboard option is empty
			 if(empty($options['admin_url'])){
			   $options = get_option('fav_url_option');
			   echo"<link rel=\"shortcut icon\" href=\"$options[global_url]\" />";
			 } else {
			 echo"<link rel=\"shortcut icon\" href=\"$options[admin_url]\" />";
			 }
            }
            add_action('admin_head', 'Dashboardfavicon');
			
			
			function iPhone_originalfavicon() {
            $options = get_option('fav_url_option');
			
			 if(empty($options['iphone_original_url'])){
			 echo "";
			 } else {
			 echo"<link rel=\"apple-touch-icon\" href=\"$options[iphone_original_url]\" />\n";
			 echo"\n";
			 }
            }
            add_action('wp_head', 'iPhone_originalfavicon');
            
            
            function iPhone_precomposedfavicon() {
            $options = get_option('fav_url_option');
			
			 if(empty($options['iphone_precomposed_url'])){
			 echo "";
			 } else {
			 echo"<link rel=\"apple-touch-icon-precomposed\" href=\"$options[iphone_precomposed_url]\" />\n";
			 echo"\n";
			 }
            }
            add_action('wp_head', 'iPhone_precomposedfavicon');
			
			

		   
		   ?>