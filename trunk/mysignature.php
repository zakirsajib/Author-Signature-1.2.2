<?php
/*
Plugin Name: Author Signature
Plugin URI: https://wordpress.org/plugins/author-signature/
Description: Displays the image of signature after each post and/or page. Supports multiusers.
Version: 1.2.2
Author: Zakir Sajib
Author URI: https://www.upwork.com/o/profiles/users/_~0173a11de60c8f353e/
License: GPL2

Copyright 2018  Zakir Sajib  (email : zakirsajib@gmail.com)

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
			add_action('show_user_profile', array($this, 'custom_user_profile_fields'));
			add_action('edit_user_profile', array($this, 'custom_user_profile_fields'));
			add_action( 'personal_options_update', array($this, 'save_extra_user_profile_fields' ));
			add_action( 'edit_user_profile_update', array($this, 'save_extra_user_profile_fields' ));
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
			
			$user_signature = esc_attr( get_the_author_meta( 'user_signature', $author_id ) );
			
			$alignment = $options['alignment'];
			$size = $options['size'];
						
			if($size == 'thumbnail'):
				
				$thumbnail_options_h = get_option('thumbnail_size_h');
				$thumbnail_options_w = get_option('thumbnail_size_w');
			
				if($alignment == 'left'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Pages" class="alignleft">';
				
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Posts" class="alignleft">';
				elseif($alignment == 'center'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Pages" class="aligncenter">';
				
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Posts" class="aligncenter">';
				elseif($alignment == 'right'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Pages" class="alignright">';
				
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Posts" class="alignright">';
				else:				
				endif;
			
			
				if ( is_page() 
					and isset($options['pages']) 
					and (!is_cart()) 
					and (!is_checkout()) 
					and (!is_account_page())
				){
					$content .= $sig;
					$content .= $author_signature_image_pages;
				}
				if (is_single() and isset($options['posts']) and (!is_product()) ){
					$content .= $sig;
					$content .= $author_signature_image_posts;
				}
				return $content;
			endif;
			if($size == 'medium'):
							
				$medium_options_h = get_option('medium_size_h');
				$medium_options_w = get_option('medium_size_w');
			
				if($alignment == 'left'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Pages" class="alignleft">';
			
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Posts" class="alignleft">';
				
				elseif($alignment == 'center'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Pages" class="aligncenter">';
			
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Posts" class="aligncenter">';
				elseif($alignment == 'right'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Pages" class="alignright">';
			
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Posts" class="alignright">';
				else:
				endif;	
			
				if ( is_page() 
					and isset($options['pages']) 
					and (!is_cart()) 
					and (!is_checkout()) 
					and (!is_account_page())
				){
					$content .= $sig;
					$content .= $author_signature_image_pages;
				}
				if (is_single() and isset($options['posts']) and (!is_product()) ){
					$content .= $sig;
					$content .= $author_signature_image_posts;
				}
				return $content;
			endif;
			if($size == 'custom'):
				
				if($alignment == 'left'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Pages" class="alignleft">';
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Posts" class="alignleft">';
				elseif($alignment == 'center'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Pages" class="aligncenter">';
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Posts" class="aligncenter">';
				elseif($alignment == 'right'):
					$author_signature_image_pages = '<img src='.$options['image']. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Pages" class="alignright">';
					$author_signature_image_posts = '<img src='.$options['image']. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Posts" class="alignright">';
				
				else:
				endif;
			
			if ( is_page() 
				and isset($options['pages']) 
				and (!is_cart()) 
				and (!is_checkout()) 
				and (!is_account_page())
			){
					$content .= $sig;
					$content .= $author_signature_image_pages;
				}
				if (is_single() and isset($options['posts']) and (!is_product()) ){
					$content .= $sig;
					$content .= $author_signature_image_posts;
				}
				return $content;
			endif;
		}
		
		
		// Multi-user capabilities 
		function custom_user_profile_fields($user) {
		?>
			<table class="form-table">
				<tr>
					<th>
						<label for="user_signature"><?php _e('Upload Signature'); ?></label>
					</th>
					<td>
						<input type="text" name="user_signature" id="image-url" value="<?php echo esc_attr( get_the_author_meta( 'user_signature', $user->ID ) ); ?>" class="medium-text" />
						<input id="upload-button" type="button" class="button" value="Upload Signature" />
						<br><span class="description"><?php _e('You can upload your signature image as jpg/jpeg,png format. Recommended size is 320px by 100px..', 'mysignature'); ?></span>
					</td>
				</tr>
				<?php if(!empty( get_the_author_meta( 'user_signature', $user->ID ) )):?>
				<tr>	
					<th>
						<label for="user_signature"><?php _e('Signature Preview'); ?></label>
					</th>
					<td>
						
						<div class="preview">
							<img id="img-preview" src="<?php echo esc_attr( get_the_author_meta( 'user_signature', $user->ID ) ); ?>" alt="Signature Preview">
						</div>
						
					</td>
				</tr>
				<?php endif;?>
			</table>
		<?php
		}
		
		

		function save_extra_user_profile_fields( $user_id ) {
		    if ( !current_user_can( 'edit_user', $user_id ) ) { 
		        return false; 
		    }
		    update_user_meta( $user_id, 'user_signature', $_POST['user_signature'] );
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
			<?php
				if($options['image']):?>
					<input id="image-url" type="text" class="medium-text" name="adminPage-group[image]" value="<?php echo $options['image']?>"/>
				<?php else:?>
					<input id="image-url" type="text" class="medium-text" name="adminPage-group[image]" value=""/>
				<?php endif;?>
					<input id="upload-button" type="button" class="button" value="Upload Signature" />
					<p class="description">You can upload your signature image as jpg/jpeg,png format. Recommended size is 320px by 100px.</p>
			
			<?php if($options['image']):?>
				<h2 class="title">Signature Preview</h2>
				<div class="preview">
					<img id="img-preview" src="<?php echo $options['image']?>" alt="Signature Preview">
				</div>
			<?php endif;?>							
			<h2 class="title">Settings</h2>
			<p>Set your signature's visibility, size and alignment.</p>					
			
			
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php _e('Display', 'adminPage-group');?></th>
				<td>
					<p><input type="checkbox" name="adminPage-group[posts]" value="1" <?php checked($options['posts'], 1)?>/>
					<?php _e("Add signature below the contents of posts.", 'mysignature'); ?></p>
				
					<p><input type="checkbox" name="adminPage-group[pages]" value="1" <?php checked($options['pages'], 1)?>/>
					<?php _e("Add signature below the contents of pages.", 'mysignature'); ?></p>
				</td>
				</tr>
			</table>
			
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Size', 'adminPage-group');?></th>
					<td>
						
				<p><input type="radio" name="adminPage-group[size]" value="thumbnail" <?php checked("thumbnail", $options['size']) ?>/>
				<?php _e("Thumbnail (150x150)", 'mysignature'); ?></p>							
						
				<p><input type="radio" name="adminPage-group[size]" value="medium" <?php checked("medium", $options['size']) ?>/>
				<?php _e("Medium (300x300) (default)", 'mysignature'); ?></p>
						
				<p><input type="radio" name="adminPage-group[size]" value="custom" <?php checked("custom", $options['size'])?>/>
						<?php _e("Custom", 'mysignature'); ?>
						
						<?php _e("Width", 'mysignature'); ?>
<input class="small-text" type="number" name="adminPage-group[customwidth]" value="<?php echo $options['customwidth']?>" />
						<?php _e("Height", 'mysignature'); ?>
<input class="small-text" type="number" name="adminPage-group[customheight]" value="<?php echo $options['customheight']?>" />
						</p>

					</td>
				</tr>
			</table>
			
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Alignment', 'adminPage-group');?></th>
					<td>
						<img class="alignment-signature" src="<?php echo plugins_url( 'img/alignment-left.jpg' , __FILE__ )?>" alt="Alignmnent Left">
						<p><input type="radio" name="adminPage-group[alignment]" value="left" <?php checked('left', $options['alignment']) ?> />
						<?php _e("Left", 'mysignature'); ?></p>							
					</td>
					<td>	
						<img class="alignment-signature" src="<?php echo plugins_url( 'img/alignment-center.jpg' , __FILE__ )?>" alt="Alignmnent Center">
						<p><input type="radio" name="adminPage-group[alignment]" value="center" <?php checked('center', $options['alignment']) ?> />
						<?php _e("Center", 'mysignature'); ?></p>
					</td>
					<td>	
						<img class="alignment-signature" src="<?php echo plugins_url( 'img/alignment-right.jpg' , __FILE__ )?>" alt="Alignmnent Right">
						<p><input type="radio" name="adminPage-group[alignment]" value="right" <?php checked('right', $options['alignment'])?> />
						<?php _e("Right (default)", 'mysignature'); ?></p>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', 'mysignature') ?>" /></p>
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
					'pages' => 0,
					'alignment'	=> 'right',
					'size'	=> 'medium'
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