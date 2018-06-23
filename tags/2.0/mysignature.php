<?php
/*
Plugin Name: Author Signature
Plugin URI: https://wordpress.org/plugins/author-signature/
Description: Displays the image of signature after each post and/or page. Supports multiusers.
Version: 1.2.3
Author: Zakir Sajib
Author URI: zakirsajib@gmail.com
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
if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}
if (!class_exists("MySignature")){
	class MySignature {
		function __construct(){
			add_action('admin_enqueue_scripts', array($this,'my_media_lib_uploader_enqueue'));
			add_action('admin_menu', array($this,'mysignature_menu'));
			add_action('wp_enqueue_scripts', array($this,'mysignature_menu_frontend') );
			add_action('show_user_profile', array($this, 'custom_user_profile_fields'));
			add_action('edit_user_profile', array($this, 'custom_user_profile_fields'));
			add_action( 'personal_options_update', array($this, 'save_extra_user_profile_fields' ));
			add_action( 'edit_user_profile_update', array($this, 'save_extra_user_profile_fields' ));
			add_filter('the_content', array($this, 'post_signature'));
		}
		
		public static function activate()
        {
            // Do nothing
        } // END public static function activate
    
             
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate

		
		function my_media_lib_uploader_enqueue() {
		 	wp_enqueue_style( 'signature-style-admin', plugins_url( '/admin/style.css' , __FILE__ ) );
		 	wp_enqueue_media();
		 	wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
		 	wp_enqueue_script( 'media-lib-uploader-js' );
		}
		
		function mysignature_menu_frontend(){
			wp_enqueue_style( 'signature-style', plugins_url( 'style.css' , __FILE__ ) );
		}
		
					
		# what this plugin does
		function post_signature($content){
			global $post;
			$options = get_option('signature-group');
			$author_id = $post->post_author;
			$author = get_userdata($author_id);
			
			$sig = '<p class="my-signature">&mdash; '.$author->display_name.'</p>';			
			
			$user_signature = esc_attr( get_the_author_meta( 'user_signature', $author_id ) );
			
			$post = $options['post'];
			$page = $options['page'];
			
			$alignment = $options['alignment'];
			$size = $options['size'];
			$before = $options['before'];
			
			$user_name = $options['name'];
						
			if($size == 'thumbnail'):
				
				$thumbnail_options_h = get_option('thumbnail_size_h');
				$thumbnail_options_w = get_option('thumbnail_size_w');
			
				if($alignment == 'left'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Pages" class="alignleft">';
				
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Posts" class="alignleft">';
				elseif($alignment == 'center'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Pages" class="aligncenter">';
				
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Posts" class="aligncenter">';
				elseif($alignment == 'right'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Pages" class="alignright">';
				
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$thumbnail_options_w.' height='.$thumbnail_options_h.' alt="Author Signature for Posts" class="alignright">';
				else:				
				endif;
			
			
				if ( is_page()){ 
					if ( class_exists( 'woocommerce' ) ){
						if ( ($page =='pages')
						and (!is_cart()) 
						and (!is_checkout()) 
						and (!is_account_page()) ){
							$content .= '<div class="woo-activated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}else{
						if ($page =='pages'){
							$content .= '<div class="woo-deactivated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				
				if (is_single()){
					if ( class_exists( 'woocommerce' ) ){
						if( ($post == 'posts') and (!is_product())){
							$content .= '<div class="woo-activated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}else{
						if($post == 'posts'){
							$content .= '<div class="woo-deactivated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				return $content;
			endif;
			
			
			if($size == 'medium'):
							
				$medium_options_h = get_option('medium_size_h');
				$medium_options_w = get_option('medium_size_w');
			
				if($alignment == 'left'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Pages" class="alignleft">';
			
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Posts" class="alignleft">';
				
				elseif($alignment == 'center'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Pages" class="aligncenter">';
			
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Posts" class="aligncenter">';
				elseif($alignment == 'right'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Pages" class="alignright">';
			
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$medium_options_w.' height='.$medium_options_h.' alt="Author Signature for Posts" class="alignright">';
				else:
				endif;	
			
				if ( is_page()){ 
					if ( class_exists( 'woocommerce' ) ){
						if ( ($page =='pages')
						and (!is_cart()) 
						and (!is_checkout()) 
						and (!is_account_page()) ){
							$content .= '<div class="woo-activated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}else{
						if ($page =='pages'){
							$content .= '<div class="woo-deactivated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				
				if (is_single()){
					if ( class_exists( 'woocommerce' ) ){
						if( ($post == 'posts') and (!is_product())){
							$content .= '<div class="woo-activated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}else{
						if($post == 'posts'){
							$content .= '<div class="woo-deactivated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				return $content;
			endif;
			if($size == 'custom'):
				
				if($alignment == 'left'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Pages" class="alignleft">';
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Posts" class="alignleft">';
				elseif($alignment == 'center'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Pages" class="aligncenter">';
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Posts" class="aligncenter">';
				elseif($alignment == 'right'):
					$author_signature_image_pages = '<img src='.$user_signature. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Pages" class="alignright">';
					$author_signature_image_posts = '<img src='.$user_signature. ' width='.$options['customwidth'].' height='.$options['customheight'].' alt="Author Signature for Posts" class="alignright">';
				
				else:
				endif;
			
				if ( is_page()){ 
					if ( class_exists( 'woocommerce' ) ){
						if ( ($page =='pages')
						and (!is_cart()) 
						and (!is_checkout()) 
						and (!is_account_page()) ){
							$content .= '<div class="woo-activated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}else{
						if ($page =='pages'){
							$content .= '<div class="woo-deactivated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				if (is_single()){
					if ( class_exists( 'woocommerce' ) ){
						if( ($post == 'posts') and (!is_product())){
							$content .= '<div class="woo-activated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}else{
						if($post == 'posts'){
							$content .= '<div class="woo-deactivated signature-'.$alignment.'">';
							$content .= '<div class="signature-name">'.$before.'</div>';
							if($user_name == 1):
							$content .= $sig;
							$content .= $author_signature_image_posts;
							else:
							$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
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
				<?php settings_fields('signature-group'); ?>
				<?php do_settings_sections('signature-group'); ?>
				<?php settings_errors(); ?>
				<?php $options = get_option('signature-group'); ?>
						
							
			<h2 class="title">Settings</h2>
			<p>Set your signature's visibility, size and alignment.</p>					
			
			
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php _e('Display', 'signature-group');?></th>
				<td>
					<p><input type="checkbox" name="signature-group[post]" value="posts" <?php checked($options['post'], "posts")?>/>
					<?php _e("Add signature below the contents of posts.", 'mysignature'); ?></p>
				
					<p><input type="checkbox" name="signature-group[page]" value="pages" <?php checked($options['page'], "pages")?>/>
					<?php _e("Add signature below the contents of pages.", 'mysignature'); ?></p>
				</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php _e('User Name', 'signature-group');?></th>
				<td>
					<p><input type="checkbox" name="signature-group[name]" value="1" <?php checked(1, $options['name'])?>/>
					<?php _e("Show User Name above signature ?", 'mysignature'); ?></p>
				</td>
				</tr>
			</table>
			
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Size', 'signature-group');?></th>
					<td>
						
				<p><input type="radio" name="signature-group[size]" value="thumbnail" <?php checked("thumbnail", $options['size']) ?>/>
				<?php _e("Thumbnail (150x150)", 'mysignature'); ?></p>							
						
				<p><input type="radio" name="signature-group[size]" value="medium" <?php checked( "medium", $options['size']) ?>/>
				<?php _e("Medium (300x300) (default)", 'mysignature'); ?></p>
						
				<p><input type="radio" name="signature-group[size]" value="custom" <?php checked( "custom", $options['size'])?>/>
						<?php _e("Custom", 'mysignature'); ?>
						
						<?php _e("Width", 'mysignature'); ?>
<input class="small-text" type="number" name="signature-group[customwidth]" value="<?php echo $options['customwidth']?>" />
						<?php _e("Height", 'mysignature'); ?>
<input class="small-text" type="number" name="signature-group[customheight]" value="<?php echo $options['customheight']?>" />
						</p>

					</td>
				</tr>
			</table>
			
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Alignment', 'signature-group');?></th>
					<td>
						<img class="alignment-signature" src="<?php echo plugins_url( 'img/alignment-left.jpg' , __FILE__ )?>" alt="Alignmnent Left">
						<p><input type="radio" name="signature-group[alignment]" value="left" <?php checked('left', $options['alignment']) ?> />
						<?php _e("Left", 'mysignature'); ?></p>							
					</td>
					<td>	
						<img class="alignment-signature" src="<?php echo plugins_url( 'img/alignment-center.jpg' , __FILE__ )?>" alt="Alignmnent Center">
						<p><input type="radio" name="signature-group[alignment]" value="center" <?php checked('center', $options['alignment'] ) ?> />
						<?php _e("Center", 'mysignature'); ?></p>
					</td>
					<td>	
						<img class="alignment-signature" src="<?php echo plugins_url( 'img/alignment-right.jpg' , __FILE__ )?>" alt="Alignmnent Right">
						<p><input type="radio" name="signature-group[alignment]" value="right" <?php checked('right', $options['alignment'])?> />
						<?php _e("Right (default)", 'mysignature'); ?></p>
					</td>
				</tr>
			</table>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Before Signature', 'signature-group');?></th>
					<td>
						<p><input type="text" name="signature-group[before]" class="medium" value="<?php echo $options['before']?>" />
						<?php _e("Example: Best Regards", 'mysignature'); ?></p>							
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
					'post' => 'posts',
					'page' => 'pages',
					'name' => '1',
					'alignment'	=> 'right',
					'size'	=> 'medium',
					'before' =>	'Best Regards'
				);
				add_option('signature-group', $options, '', 'yes'); 
				//add_action('admin_init', array($this, 'register_settings'));
				register_setting('signature-group', 'signature-group', $options);
			}
		}
			
	}// End Class
}

# Object Creation here: Important
if (class_exists("MySignature")){	
	register_activation_hook(__FILE__, array('WP_Plugin_Template', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_Plugin_Template', 'deactivate'));
	$signature_obj = new MySignature();
}?>