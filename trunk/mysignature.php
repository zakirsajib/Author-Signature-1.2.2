<?php
/*
Plugin Name: Author Signature
Plugin URI: https://wordpress.org/plugins/author-signature/
Description: Displays the image of signature after each post and/or page. Supports multiusers.
Version: 1.2.4
Author: Zakir Sajib
Author URI: https://zakirsajib.netlify.app
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

define( 'AS_VERSION', '1.2.3' );

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'MySignature' ) ) {
	class MySignature {
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'media_lib_uploader_MySignature' ) );
			add_action( 'admin_menu', array( $this, 'mysignature_menu' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'media_lib_uploader_frontend_MySignature' ) );
			add_action( 'show_user_profile', array( $this, 'custom_user_profile_fields_MySignature' ) );
			add_action( 'edit_user_profile', array( $this, 'custom_user_profile_fields_MySignature' ) );
			add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields_MySignature' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields_MySignature' ) );
			add_filter( 'the_content', array( $this, 'post_signature' ) );
		}
		
		public static function activate_MySignature() {
			flush_rewrite_rules();
		} 
	
			 
		public static function deactivate_MySignature() {
			flush_rewrite_rules();
		} 

		
		public function media_lib_uploader_MySignature() {
			wp_enqueue_style( 
				'signature-style-admin', 
				plugins_url( '/admin/css/style.css', __FILE__ ),
				array(),
				filemtime( plugin_dir_path( __FILE__ ) . 'admin/css/style.css' )
			);
			wp_enqueue_media();
			wp_register_script( 
				'media-lib-uploader-js', 
				plugins_url( '/admin/js/media-lib-uploader.js', __FILE__ ), 
				array( 'jquery' ),
				AS_VERSION,
				true
			);
			wp_enqueue_script( 'media-lib-uploader-js' );
		}
		
		public function media_lib_uploader_frontend_MySignature() {
			wp_enqueue_style( 
				'signature-style', 
				plugins_url( '/public/css/style.css', __FILE__ ),
				array(),
				filemtime( plugin_dir_path( __FILE__ ) . 'public/css/style.css' )
			);
		}
		
					
		# what this plugin does
		public function post_signature( $content ) {
			global $post;
			   $options   = get_option( 'signature-group' );
			   $author_id = $post->post_author;
			   $author    = get_userdata( $author_id );
			
			$sig = '<p class="my-signature">&mdash; ' . $author->display_name . '</p>';         
			
			$user_signature = esc_attr( get_the_author_meta( 'user_signature', $author_id ) );
			
			$post = $options['post'];
			$page = $options['page'];
			
			$alignment = $options['alignment'];
			$size      = $options['size'];
			$before    = $options['before'];
			
			$user_name = $options['name'];
						
			if ( $size === 'thumbnail' ) :
				
				$thumbnail_options_h = get_option( 'thumbnail_size_h' );
				$thumbnail_options_w = get_option( 'thumbnail_size_w' );
			
				if ( $alignment == 'left' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $thumbnail_options_w . ' height=' . $thumbnail_options_h . ' alt="Author Signature for Pages" class="alignleft">';
				
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $thumbnail_options_w . ' height=' . $thumbnail_options_h . ' alt="Author Signature for Posts" class="alignleft">';
				elseif ( $alignment == 'center' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $thumbnail_options_w . ' height=' . $thumbnail_options_h . ' alt="Author Signature for Pages" class="aligncenter">';
				
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $thumbnail_options_w . ' height=' . $thumbnail_options_h . ' alt="Author Signature for Posts" class="aligncenter">';
				elseif ( $alignment == 'right' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $thumbnail_options_w . ' height=' . $thumbnail_options_h . ' alt="Author Signature for Pages" class="alignright">';
				
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $thumbnail_options_w . ' height=' . $thumbnail_options_h . ' alt="Author Signature for Posts" class="alignright">';              
				endif;
			
			
				if ( is_page() ) { 
					if ( class_exists( 'woocommerce' ) ) {
						if ( ( $page == 'pages' )
						&& ( ! is_cart() ) 
						&& ( ! is_checkout() ) 
						&& ( ! is_account_page() ) ) {
							$content .= '<div class="woo-activated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name == 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					} else {
						if ( $page == 'pages' ) {
							$content .= '<div class="woo-deactivated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name == 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				
				if ( is_single() ) {
					if ( class_exists( 'woocommerce' ) ) {
						if ( ( $post == 'posts' ) && ( ! is_product() ) ) {
							$content .= '<div class="woo-activated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name == 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					} else {
						if ( $post == 'posts' ) {
							$content .= '<div class="woo-deactivated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name == 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				return $content;
			endif;
			
			
			if ( $size === 'medium' ) :
							
				$medium_options_h = get_option( 'medium_size_h' );
				$medium_options_w = get_option( 'medium_size_w' );
			
				if ( $alignment == 'left' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $medium_options_w . ' height=' . $medium_options_h . ' alt="Author Signature for Pages" class="alignleft">';
			
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $medium_options_w . ' height=' . $medium_options_h . ' alt="Author Signature for Posts" class="alignleft">';
				
				elseif ( $alignment == 'center' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $medium_options_w . ' height=' . $medium_options_h . ' alt="Author Signature for Pages" class="aligncenter">';
			
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $medium_options_w . ' height=' . $medium_options_h . ' alt="Author Signature for Posts" class="aligncenter">';
				elseif ( $alignment == 'right' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $medium_options_w . ' height=' . $medium_options_h . ' alt="Author Signature for Pages" class="alignright">';
			
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $medium_options_w . ' height=' . $medium_options_h . ' alt="Author Signature for Posts" class="alignright">';
				endif;  
			
				if ( is_page() ) { 
					if ( class_exists( 'woocommerce' ) ) {
						if ( ( $page === 'pages' )
						&& ( ! is_cart() ) 
						&& ( ! is_checkout() ) 
						&& ( ! is_account_page() ) ) {
							$content .= '<div class="woo-activated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name === 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					} else {
						if ( $page === 'pages' ) {
							$content .= '<div class="woo-deactivated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name === 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				
				if ( is_single() ) {
					if ( class_exists( 'woocommerce' ) ) {
						if ( ( $post === 'posts' ) && ( ! is_product() ) ) {
							$content .= '<div class="woo-activated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name === 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					} else {
						if ( $post === 'posts' ) {
							$content .= '<div class="woo-deactivated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name === 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				return $content;
			endif;
			if ( $size === 'custom' ) :
				
				if ( $alignment === 'left' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $options['customwidth'] . ' height=' . $options['customheight'] . ' alt="Author Signature for Pages" class="alignleft">';
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $options['customwidth'] . ' height=' . $options['customheight'] . ' alt="Author Signature for Posts" class="alignleft">';
				elseif ( $alignment === 'center' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $options['customwidth'] . ' height=' . $options['customheight'] . ' alt="Author Signature for Pages" class="aligncenter">';
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $options['customwidth'] . ' height=' . $options['customheight'] . ' alt="Author Signature for Posts" class="aligncenter">';
				elseif ( $alignment === 'right' ) :
					$author_signature_image_pages = '<img src=' . $user_signature . ' width=' . $options['customwidth'] . ' height=' . $options['customheight'] . ' alt="Author Signature for Pages" class="alignright">';
					$author_signature_image_posts = '<img src=' . $user_signature . ' width=' . $options['customwidth'] . ' height=' . $options['customheight'] . ' alt="Author Signature for Posts" class="alignright">';
				endif;
			
				if ( is_page() ) { 
					if ( class_exists( 'woocommerce' ) ) {
						if ( ( $page === 'pages' )
						&& ( ! is_cart() ) 
						&& ( ! is_checkout() ) 
						&& ( ! is_account_page() ) ) {
							$content .= '<div class="woo-activated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name == 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					} else {
						if ( $page === 'pages' ) {
							$content .= '<div class="woo-deactivated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name == 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					}
				}
				if ( is_single() ) {
					if ( class_exists( 'woocommerce' ) ) {
						if ( ( $post === 'posts' ) && ( ! is_product() ) ) {
							$content .= '<div class="woo-activated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name == 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
								$content .= $author_signature_image_posts;
							endif;
							$content .= '</div>';
						}
					} else {
						if ( $post === 'posts' ) {
							$content .= '<div class="woo-deactivated signature-' . $alignment . '">';
							$content .= '<div class="signature-name">' . $before . '</div>';
							if ( $user_name == 1 ) :
								$content .= $sig;
								$content .= $author_signature_image_posts;
							else :
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
		public function custom_user_profile_fields_MySignature( $user ) {
		?>
			<table class="form-table">
				<tr>
					<th>
						<label for="user_signature"><?php esc_attr_e( 'Upload Signature' ); ?></label>
					</th>
					<td>
						<input 
							type="text" 
							name="user_signature" 
							id="image-url" 
							value="<?php echo esc_url( get_the_author_meta( 'user_signature', $user->ID ) ); ?>" class="medium-text" 
						/>
						<input id="upload-button" type="button" class="button" value="Upload Signature" />
						<br><span class="description"><?php esc_attr_e( 'You can upload your signature image as jpg/jpeg,png format. Recommended size is 320px by 100px..', 'mysignature' ); ?></span>
					</td>
				</tr>
				<?php if ( ! empty( get_the_author_meta( 'user_signature', $user->ID ) ) ) : ?>
				<tr>	
					<th>
						<label for="user_signature"><?php esc_attr_e( 'Signature Preview' ); ?></label>
					</th>
					<td>
						
						<div class="preview">
							<img 
								id="img-preview" 
								src="<?php echo esc_url( get_the_author_meta( 'user_signature', $user->ID ) ); ?>" alt="Signature Preview"
								width="50"
								height="50"
							>
						</div>
						
					</td>
				</tr>
				<?php endif; ?>
			</table>
			<?php
		}
		
		

		public function save_extra_user_profile_fields_MySignature( $user_id ) {
			if ( ! current_user_can( 'edit_user', $user_id ) ) { 
				return false; 
			}
			if ( ! empty( $_POST['user_signature'] ) ) :
				$user_signature = esc_url( sanitize_text_field( wp_unslash( $_POST['user_signature'] ) ) );
				update_user_meta( $user_id, 'user_signature', $user_signature );
			endif;
		}
		
		
		# Settings page
		public function adminPage_MySignature() {
		?>							
			<div class="wrap">
			<h1><?php esc_attr_e( 'Author Signature', 'mysignature' ); ?></h1>
					
			<form method="post" id="mysignature" action="options.php">
				<?php settings_fields( 'signature-group' ); ?>
				<?php do_settings_sections( 'signature-group' ); ?>
				<?php settings_errors(); ?>
				<?php $options = get_option( 'signature-group' ); ?>
						
							
			<h2 class="title">Settings</h2>
			<p>Set your signature's visibility, size and alignment.</p>					
			
			
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php esc_attr_e( 'Display', 'signature-group' ); ?></th>
				<td>
					<p><input type="checkbox" name="signature-group[post]" value="posts" <?php checked( $options['post'], 'posts' ); ?>/>
					<?php esc_attr_e( 'Add signature below the contents of posts.', 'mysignature' ); ?></p>
				
					<p><input type="checkbox" name="signature-group[page]" value="pages" <?php checked( $options['page'], 'pages' ); ?>/>
					<?php esc_attr_e( 'Add signature below the contents of pages.', 'mysignature' ); ?></p>
				</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php esc_attr_e( 'User Name', 'signature-group' ); ?></th>
				<td>
					<p><input type="checkbox" name="signature-group[name]" value="1" <?php checked( 1, $options['name'] ); ?>/>
					<?php esc_attr_e( 'Show User Name above signature.', 'mysignature' ); ?></p>
				</td>
				</tr>
			</table>
			
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_attr_e( 'Size', 'signature-group' ); ?></th>
					<td>
						
				<p><input type="radio" name="signature-group[size]" value="thumbnail" <?php checked( 'thumbnail', $options['size'] ); ?>/>
				<?php esc_attr_e( 'Thumbnail (150x150)', 'mysignature' ); ?></p>							
						
				<p><input type="radio" name="signature-group[size]" value="medium" <?php checked( 'medium', $options['size'] ); ?>/>
				<?php esc_attr_e( 'Medium (300x300) (default)', 'mysignature' ); ?></p>
						
				<p><input type="radio" name="signature-group[size]" value="custom" <?php checked( 'custom', $options['size'] ); ?>/>
						<?php esc_attr_e( 'Custom', 'mysignature' ); ?>
						
						<?php esc_attr_e( 'Width', 'mysignature' ); ?>
<input class="small-text" type="number" name="signature-group[customwidth]" value="<?php echo esc_attr( $options['customwidth'] ); ?>" />
						<?php esc_attr_e( 'Height', 'mysignature' ); ?>
<input class="small-text" type="number" name="signature-group[customheight]" value="<?php echo esc_attr( $options['customheight'] ); ?>" />
						</p>

					</td>
				</tr>
			</table>
			
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_attr_e( 'Alignment', 'signature-group' ); ?></th>
					<td>
						<img 
							class="alignment-signature" 
							src="<?php echo esc_url( plugins_url( 'admin/img/alignment-left.jpg', __FILE__ ) ); ?>" 
							alt="Alignmnent Left"
						>
						<p><input type="radio" name="signature-group[alignment]" value="left" <?php checked( 'left', $options['alignment'] ); ?> />
						<?php esc_attr_e( 'Left', 'mysignature' ); ?></p>							
					</td>
					<td>	
						<img 
							class="alignment-signature" 
							src="<?php echo esc_url( plugins_url( 'admin/img/alignment-center.jpg', __FILE__ ) ); ?>" 
							alt="Alignmnent Center"
						>
						<p><input type="radio" name="signature-group[alignment]" value="center" <?php checked( 'center', $options['alignment'] ); ?> />
						<?php esc_attr_e( 'Center', 'mysignature' ); ?></p>
					</td>
					<td>	
						<img 
							class="alignment-signature" 
							src="<?php echo esc_url( plugins_url( 'admin/img/alignment-right.jpg', __FILE__ ) ); ?>" 
							alt="Alignmnent Right"
						>
						<p><input type="radio" name="signature-group[alignment]" value="right" <?php checked( 'right', $options['alignment'] ); ?> />
						<?php esc_attr_e( 'Right (default)', 'mysignature' ); ?></p>
					</td>
				</tr>
			</table>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_attr_e( 'Before Signature', 'signature-group' ); ?></th>
					<td>
						<p><input type="text" name="signature-group[before]" class="medium" value="<?php echo esc_attr( $options['before'] ); ?>" />
						<?php esc_attr_e( 'Example: Best Regards', 'mysignature' ); ?></p>							
					</td>
				</tr>
			</table>
			
			
			<p class="submit"><input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'mysignature' ); ?>" /></p>
			</form>
			</div>
			<?php
		}
			
		# admin panel into Settings menu
		public function mysignature_menu() {
			if ( function_exists( 'add_options_page' ) ) {
				add_menu_page( 
					'My Signature', 
					'Signature', 
					'administrator', 
					__FILE__, 
					array( $this, 'adminPage_MySignature' ), 
					'dashicons-sticky' 
				);
				# set defaults
				$options = array(
					'post'      => 'posts',
					'page'      => 'pages',
					'name'      => '1',
					'alignment' => 'right',
					'size'      => 'medium',
					'before'    => 'Best Regards',
				);
				add_option( 'signature-group', $options, '', 'yes' ); 
				register_setting( 'signature-group', 'signature-group', $options );
			}
		}
			
	}// End Class
}

# Object Creation here: Important
if ( class_exists( 'MySignature' ) ) {   
	register_activation_hook( __FILE__, array( 'WP_Plugin_Template', 'activate_MySignature' ) );
	register_deactivation_hook( __FILE__, array( 'WP_Plugin_Template', 'deactivate_MySignature' ) );
	$signature_obj = new MySignature();
}?>
