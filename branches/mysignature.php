<?php
/*
Plugin Name: Author Signature
Plugin URI: https://wordpress.org/plugins/author-signature/
Description: Displays the image of signature after each post and/or page.
Version: 1.2.1
Author: Zakir Sajib
Author URI: https://zakirsajib.online
License: GPL2

Copyright 2017  Zakir Sajib  (email : zakirsajib@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists("MySignature")){
	class MySignature {
		function __construct(){
			add_action('admin_enqueue_scripts', array($this,'my_media_lib_uploader_enqueue'));
			add_action('admin_menu', array($this,'mysignature_menu'));
			add_filter('the_content', array($this, 'post_signature'));
		}
		
		function my_media_lib_uploader_enqueue() {
		 	wp_enqueue_style( 'signature-style', plugins_url( 'style.css' , __FILE__ ) );
		 	wp_enqueue_media();
		 	wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
		 	wp_enqueue_script( 'media-lib-uploader-js' );
		}
		
					
		# what this plugin does
		function post_signature($content){
			global $post;
			$options = get_option('adminPage-group');
			$author_id = $post->post_author;
			$author = get_userdata($author_id);
			$sig = '<p class="my-signature">&mdash; '.$author->display_name.'</p>';
			
			$size = $options['size']; echo $size;
						
			if($size == 'thumbnail'):
				$thumbnail_options_h = get_option('thumbnail_size_h');
				$thumbnail_options_w = get_option('thumbnail_size_w');
			
				$author_signature_image_pages = '<img src='.$options['image']. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Pages">';
			
				$author_signature_image_posts = '<img src='.$options['image']. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Posts">';
			
			
				if (is_page() and $options['pages']){
					$content .= $sig;
					$content .= $author_signature_image_pages;
				}
				if (is_single() and $options['posts']){
					$content .= $sig;
					$content .= $author_signature_image_posts;
				}
				return $content;
			endif;
			if($size == 'medium'):
				$medium_options_h = get_option('medium_size_h');
				$medium_options_w = get_option('medium_size_w');
			
				$author_signature_image_pages = '<img src='.$options['image']. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Pages">';
			
				$author_signature_image_posts = '<img src='.$options['image']. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Posts">';
			
			
				if (is_page() and $options['pages']){
					$content .= $sig;
					$content .= $author_signature_image_pages;
				}
				if (is_single() and $options['posts']){
					$content .= $sig;
					$content .= $author_signature_image_posts;
				}
				return $content;
			endif;
			if($size == 'custom'):
			
				$author_signature_image_pages = '<img src='.$options['image']. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Pages">';
			
				$author_signature_image_posts = '<img src='.$options['image']. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Posts">';
			
			
				if (is_page() and $options['pages']){
					$content .= $sig;
					$content .= $author_signature_image_pages;
				}
				if (is_single() and $options['posts']){
					$content .= $sig;
					$content .= $author_signature_image_posts;
				}
				return $content;
			endif;
		}

		# Settings page
		function adminPage(){?>							
			<div class="wrap">
			<h1><?php _e('Author Signature', 'mysignature'); ?></h1>
					
			<form method="post" id="mysignature" action="options.php">
				<?php settings_fields('adminPage-group'); ?>
				<?php do_settings_sections('adminPage-group'); ?>
				<?php $options = get_option('adminPage-group'); ?>
						
			<h2 class="title">Upload Signature</h2>
			<p>You can upload your signature image as jpg/jpeg,png format. Recommended size is 320px by 100px.</p>
			<input id="image-url" type="text" class="medium-text" name="adminPage-group[image]" value="<?php echo $options['image']?>"/>
			<input id="upload-button" type="button" class="button" value="Upload Signature" />
			
			<h2 class="title">Signature Preview</h2>
			<div class="preview">
				<img id="img-preview" src="<?php echo $options['image']?>" alt="Signature Preview">
			</div>
									
			<h2 class="title">Settings</h2>
			<p>Set your signature's visibility.</p>					
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php _e('Display', 'adminPage-group');?></th>
				<td>
					<p><input type="checkbox" name="adminPage-group[posts]" value="1" <?php checked(1, isset($options['posts'])) ?> />
					<?php _e("Add signature below the contents of posts.", 'mysignature'); ?></p>
				
					<p><input type="checkbox" name="adminPage-group[pages]" value="1" <?php checked(1, isset($options['pages'])) ?> />
					<?php _e("Add signature below the contents of pages.", 'mysignature'); ?></p>
				</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Size', 'adminPage-group');?></th>
					<td>
						<p><input type="radio" name="adminPage-group[size]" value="thumbnail" <?php checked('thumbnail', $options['size']) ?> />
						<?php _e("Thumbnail (150x150)", 'mysignature'); ?></p>							
						
						<p><input type="radio" name="adminPage-group[size]" value="medium" <?php checked('medium', $options['size']) ?> />
						<?php _e("Medium (300x300)", 'mysignature'); ?></p>
						
						
						<p><input type="radio" name="adminPage-group[size]" value="custom" <?php checked('custom', $options['size']) ?> />
						<?php _e("Custom", 'mysignature'); ?>
						
						<?php _e("Width", 'mysignature'); ?>
<input class="small-text" type="number" name="adminPage-group[customwidth]" value="<?php echo $options['customwidth'] ?>" />
						<?php _e("Height", 'mysignature'); ?>
<input class="small-text" type="number" name="adminPage-group[customheight]" value="<?php echo $options['customheight'] ?>" />
						</p>

					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'mysignature') ?>" />
			</p>
			</form>
			</div>
		<?php
		}
			
		# admin panel into Settings menu
		function mysignature_menu(){
			if(function_exists('add_options_page')){
									
				add_menu_page('My Signature', 'Signature', 'administrator', __FILE__, array($this, 'adminPage'), 'dashicons-sticky');
				
				# set defaults
				$options = array(
					'posts' => 1,
					'pages' => 0
				);
				add_option('adminPage-group', $options, '', 'yes'); 
				add_action('admin_init', array($this, 'register_settings'));
			}
		}
		
		# Wordpress internal registration
		function register_settings(){
			register_setting('adminPage-group', 'adminPage-group');
			register_setting('adminPage-group', 'adminPage-group');
		}
			
	}// End Class
}

# Object Creation here: Important
if (class_exists("MySignature")){		
	$signature_obj = new MySignature();
}?>