<?php
/*
Plugin Name: Best Guest Post Plugin
Plugin URI: http://www.wdclub.com/plugins/
Description: Now your users can add posts (Content, Articles) as guests. The Best Plugin For Guest Posting is brought to you by WDclub.com. Submit your online marketing / self help related article to our PR3, 9 year old resource today. 
Author: WDclub
Version: 1.0
Author URI: http://www.wdclub.com/
*/

// NO EDITING REQUIRED - PLEASE SET PREFERENCES IN THE WP ADMIN!
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

// create custom plugin settings menu
add_action('admin_menu', 'gp_create_menu');
add_action('admin_enqueue_scripts', 'gp_add_styles_head');
add_action('admin_enqueue_scripts', 'gp_add_scripts_head');
add_action('admin_init', 'gp_force_users_to_add_settings');
add_action('wp_enqueue_scripts', 'gp_add_scripts_frontend_head');
add_action('wp', 'gp_catch_post_requests');
add_image_size( 'gp_thumbnail', 620, 180);

//wp_editor( $content, 'gpContentOfArticle' );

function gp_create_menu() {

	//create submenu settings page
	add_submenu_page('options-general.php', 'Best Guest Poster Settings', 'Best Guest Poster Settings', 'manage_options', 'gp-page-elements', 'gp_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_gpsettings' );
}

function gp_add_scripts_head() {
    if( 'options-general.php' != $hook ) {
		wp_enqueue_script( 'jQtransform', plugins_url('/js/jquery.jqtransform.js', __FILE__) );
		wp_enqueue_script( 'gpFunctions', plugins_url('/js/gpfunctions.js', __FILE__) );
		wp_enqueue_script( 'jquery-form');
	}
}

function gp_add_styles_head() {
        wp_register_style( 'jQtransformcss', plugins_url('/css/jqtransform.css', __FILE__), false, '1.0.0' );
        wp_enqueue_style( 'jQtransformcss' );
}

function gp_add_scripts_frontend_head() {
		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'jQmsg', plugins_url('/js/zebra_dialog.src.js', __FILE__) );
		wp_enqueue_script( 'gPfrontend', plugins_url('/js/gpfrontendfunc.js', __FILE__) );
		wp_enqueue_script( 'jquery-form');
		
        wp_register_style( 'jQtransformcss', plugins_url('/css/jqtransform.css', __FILE__), false, '1.0.0' );
        wp_register_style( 'jQmsgcss', plugins_url('/css/zebra_dialog.css', __FILE__), false, '1.0.0' );
        wp_register_style( 'gpFront', plugins_url('/css/frontend.css', __FILE__), false, '1.0.0' );
        wp_enqueue_style( 'jQtransformcss' );
        wp_enqueue_style( 'jQmsgcss' );
        wp_enqueue_style( 'gpFront' );
}

function gp_force_users_to_add_settings() {
	$admin = wp_get_current_user();
	$adminID = $admin->ID;
	
	$category = get_option('gp-uploaded-posts-category');
	$publish = get_option('gp-publish-instantly');
	$button = get_option('gp-try-again-button-text');
	$count = get_option('gp-links-count-in-bio');
	$dofollow = get_option('gp-do-follow-links');
	$image = get_option('gp-image-upload-feature');
	$author = get_option('gp-author-id');

	if(empty($category) && empty($publish) && empty($count) && empty($dofollow) && empty($image) && empty($author))
	{
		add_option('gp-uploaded-posts-category', 1);
		add_option('gp-publish-instantly', 0);
		add_option('gp-try-again-button-text', 'Submit Another Article.');
		add_option('gp-links-count-in-bio', 1);
		add_option('gp-do-follow-links', 1);
		add_option('gp-image-upload-feature',1);
		add_option('gp-author-id', $adminID);
	}
}

function register_gpsettings() {
	//register our settings
	register_setting( 'gp-settings-group', 'gp-uploaded-posts-category' );
	register_setting( 'gp-settings-group', 'gp-thank-you-text' );
	register_setting( 'gp-settings-group', 'gp-iport-fail-text' );
	register_setting( 'gp-settings-group', 'gp-publish-instantly' );
	register_setting( 'gp-settings-group', 'gp-author-id' );
	register_setting( 'gp-settings-group', 'gp-try-again-button-text' );
	register_setting( 'gp-settings-group', 'gp-links-count-in-bio' );
	register_setting( 'gp-settings-group', 'gp-do-follow-links' );
	register_setting( 'gp-settings-group', 'gp-image-upload-feature' );
}



function gp_settings_page() {
?>
<div class="wrap">
<h2>Best Guest Poster Settings</h2>
<div class="updated settings-error">
<p><b>The Best Plugin For Guest Posting is brought to you by WDclub.com. Submit your online
marketing related article to our PR3, 9 year old resource today.</p>
</div>
<form method="post" action="options.php" class="jqtransform">
    <?php settings_fields( 'gp-settings-group' ); ?>
    <?php do_settings_sections( 'gp-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Please select posts Category:</th>
        <td><?php wp_dropdown_categories('show_count=0&hierarchical=1&hide_empty=0&name=gp-uploaded-posts-category&selected='.get_option('gp-uploaded-posts-category').'&hide_if_empty=0'); ?></td>
        </tr>
         
		
        <tr valign="top">
        <th scope="row">Thank you text:</th>
        <td><textarea name="gp-thank-you-text" style="width: 400px; height: 120px;"><?php echo get_option('gp-thank-you-text'); ?></textarea></td>
        </tr>

        <tr valign="top">
        <th scope="row">Error message:</th>
        <td><textarea name="gp-iport-fail-text" style="width: 400px; height: 120px;"><?php echo get_option('gp-iport-fail-text'); ?></textarea></td>
        </tr>
		
        <tr valign="top">
        <th scope="row">Try again button text:</th>
        <td><input name="gp-try-again-button-text" value="<?php echo get_option('gp-try-again-button-text'); ?>" /></td>
        </tr>
		
        <tr valign="top">
        <th scope="row">Publish instantly?</th>
        <td><label><input type="radio" value="1" name="gp-publish-instantly" <?php $t = get_option('gp-publish-instantly'); if(isset($t) && $t == 1) echo 'checked="checked" '; ?>/>Yes</label><label><input type="radio" value="0" name="gp-publish-instantly" <?php $t = get_option('gp-publish-instantly'); if(isset($t) && $t == 0) echo 'checked="checked" '; ?>/>No</label></td>
        </tr>
		
        <tr valign="top">
        <th scope="row">Enable Image Upload Feature?</th>
        <td><label><input type="radio" value="1" name="gp-image-upload-feature" <?php $t = get_option('gp-image-upload-feature'); if(isset($t) && $t == 1) echo 'checked="checked" '; ?>/>Yes</label><label><input type="radio" value="0" name="gp-image-upload-feature" <?php $t = get_option('gp-image-upload-feature'); if(isset($t) && $t == 0) echo 'checked="checked" '; ?>/>No</label></td>
        </tr>
		
        <tr valign="top">
        <th scope="row">How many links to allow in author bio?</th>
        <td><label><input type="radio" value="1" name="gp-links-count-in-bio" <?php $t = get_option('gp-links-count-in-bio'); if(isset($t) && $t == 1) echo 'checked="checked" '; ?>/>1</label>
		<label><input type="radio" value="2" name="gp-links-count-in-bio" <?php $t = get_option('gp-links-count-in-bio'); if(isset($t) && $t == 2) echo 'checked="checked" '; ?>/>2</label>
		<label><input type="radio" value="3" name="gp-links-count-in-bio" <?php $t = get_option('gp-links-count-in-bio'); if(isset($t) && $t == 3) echo 'checked="checked" '; ?>/>3</label>
		<label><input type="radio" value="4" name="gp-links-count-in-bio" <?php $t = get_option('gp-links-count-in-bio'); if(isset($t) && $t == 4) echo 'checked="checked" '; ?>/>4</label>
		<label><input type="radio" value="5" name="gp-links-count-in-bio" <?php $t = get_option('gp-links-count-in-bio'); if(isset($t) && $t == 5) echo 'checked="checked" '; ?>/>5</label>
		</td>
        </tr>
		
        <tr valign="top">
        <th scope="row">Enable Do-Follow links?</th>
        <td><label><input type="radio" value="1" name="gp-do-follow-links" <?php $t = get_option('gp-do-follow-links'); if(isset($t) && $t == 1) echo 'checked="checked" '; ?>/>Yes</label><label><input type="radio" value="0" name="gp-do-follow-links" <?php $t = get_option('gp-do-follow-links'); if(isset($t) && $t == 0) echo 'checked="checked" '; ?>/>No</label></td>
        </tr>
		
        <tr valign="top">
        <th scope="row">Select user to whom assign posts:</th>
        <td><?php wp_dropdown_users(array('selected' => get_option('gp-author-id'),'name' => 'gp-author-id')); ?></td>
        </tr>

    </table>
    
    <?php submit_button(); ?>
	<p>To display the Best Guest Post plugin by WDclub.com enter the following code on any "Page" or "Post".<br />Shortcode: <strong>[ByWDclub.com]</strong></p>
</form>
</div>
<?php } 

function gp_catch_post_requests() {
	global $post;
	global $pagenow;
	require('simple_html_dom.php');
	//if ( !is_page('login') && !is_user_logged_in() && is_page(get_option('gp-submit-page'))){ 
	//	auth_redirect(); 
	//} else {
		$_POST['post_type'] = 'post';
		if(isset($_POST) && isset($_POST['gpNameOfArticle']) && isset($_POST['gpContentOfArticle']) && isset($_POST['gpBioOfTheAuthor'])) {
			
			$title = stripslashes($_POST['gpNameOfArticle']);
			$content = stripslashes($_POST['gpContentOfArticle']);
			$authorbio = $_POST['gpBioOfTheAuthor'];
			$authorID = get_option('gp-author-id');

			$category = get_option('gp-uploaded-posts-category');
			$ins = get_option('gp-publish-instantly');
			
			if($ins == 1) {
				$status = 'publish';
			} else {
				$status = 'draft';
			}
			
			// Create post object
			$my_post = array(
			  'post_title'    => $title,
			  'post_content'  => $content,
			  'post_status'   => $status,
			  'post_author'   => $authorID,
			  'post_category' => array($category)
			);

			// Insert the post into the database
			$insertion = wp_insert_post( $my_post );
			
			if ($insertion) {
				if(!empty($_FILES['gpImageOfArticle']['name']))
				{
					//if no errors...
					if(!$_FILES['gpImageOfArticle']['error'])
					{
						$valid_file = true;
						
						//now is the time to modify the future file name and validate the file

						$new_file_name = strtolower($_FILES['gpImageOfArticle']['name']); //rename file
						if($_FILES['gpImageOfArticle']['size'] > (2048000) && $new_file_name == '') //can't be larger than 2 MB
						{
							$valid_file = false;
							$message = 'Oops!  Your file\'s size is to large.';
						}
						
						//if the file has passed the test
						if($valid_file)
						{
							$image_id = media_handle_sideload( $_FILES['gpImageOfArticle'], (int) $insertion);
							//set_post_thumbnail( (int)$insertion, (int)$image_id );
							$my_post = array();
							$my_post['ID'] = $insertion;
							$image = wp_get_attachment_image_src($image_id, 'gp_thumbnail');
							$content = '<img src="'.$image[0].'" title="'.$title.'" class="aligncenter"/><br />'.$content;
							$my_post['post_content'] = $content;
							wp_update_post( $my_post );
						}
					}
					//if there is an error...
					else
					{
						//set that to be the returned message
						$message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['gpImageOfArticle']['error'];
					}
				}	
				echo get_option('gp-thank-you-text');
				
				$authorbio = stripslashes($authorbio);
				$authorbio = html_entity_decode($authorbio);

				$html = str_get_html($authorbio);

				$linkcount = count($html->find('a'));

				$optionlinks = get_option('gp-links-count-in-bio');
				//echo $optionlinks;
				$dofollow = get_option('gp-do-follow-links');
				
				$i = 0;
				foreach($html->find('a') as $element) {
					if($i >= $optionlinks) {
						$text = $element->innertext;
						$element->outertext = $text;
					} else {
						if($dofollow == 1) {
						$element->rel = 'dofollow';
						}
					}
					$i++;
				}
				
				$authorinfomod = (string)$html;

				//add_post_meta($insertion, '_gp_author_website', $authorwebsite, true);
				add_post_meta($insertion, '_gp_author_bio', $authorinfomod, true);
			} else {
				echo get_option('gp-iport-fail-text');
			}	
			exit();
		} 
	//} 
}


//Adding Shortcode
function gp_section_feed_shortcode( $atts ) {
	extract( shortcode_atts( array( 'limit' => -1), $atts ) );
	global $post;
	$html = '';
	if(is_page($post->ID) || is_single($post->ID)) {
	ob_start();
?>
    <!--BEGIN #signup-form -->
    <div id="signup-form">
        
        <!--BEGIN #subscribe-inner -->
        <div id="signup-inner">


            
            <form id="gp-send-form" name="gp-send-form" action="" method="POST" enctype="multipart/form-data">
            	
                <p>

                <label for="gpNameOfArticle">Title of Article *</label>
                <input id="gpNameOfArticle" type="text" name="gpNameOfArticle" value="" />
                </p>
				<?php $t = get_option('gp-image-upload-feature'); if(isset($t) && $t == 1) : ?>
                <p>
                <label for="gpImageOfArticle">Article Image</label>
                <input id="gpImageOfArticle" type="file" name="gpImageOfArticle" value="" />
                </p>
				<?php endif; ?>
                <p>
                <label for="gpContentOfArticle">Article Body *</label>
                <?php wp_editor( '', 'gpContentOfArticle', array('teeny' => true, 'media_buttons' => false,'textarea_rows' => 5, 'quicktags' => false) ); ?>
                </p>
                <p>
                <label for="gpBioOfTheAuthor">Author Bio *</label>
				<?php 
				wp_editor( '', 'gpBioOfTheAuthor', array('teeny' => true, 'media_buttons' => false,'textarea_rows' => 5, 'quicktags' => false)); ?>
				<?php //<textarea id="gpBioOfTheAuthor" name="gpBioOfTheAuthor"></textarea> ?>
                </p>
                <p>
                <button id="submit-button" type="submit">Submit</button>
                </p>
				<?php $t = get_option('gp-register-users'); if(isset($t) && $t == 1) : ?>
                <p><small><strong>NOTE:</strong> You must activate your account after sign up</small></p>
				<?php endif; ?>
            </form>
            
			<div id="required">

			</div>

        </div>
			<div id="thank-you-message" style="display: none;"><a href="#" id="gp-submit-try-again" class="gp-try-again-button"><?php echo get_option('gp-try-again-button-text'); ?></a></div>
        <!--END #signup-inner -->
    </div>
        
    <!--END #signup-form -->  
<?php
	$html = ob_get_clean();
	}
	
	return $html;
}

add_shortcode('ByWDclub.com', 'gp_section_feed_shortcode');


/* Define the custom box */

add_action( 'add_meta_boxes', 'gpbox_add_custom_box' );

/* Do something with the data entered */
add_action( 'save_post', 'gpbox_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function gpbox_add_custom_box() {
	global $post;
	$name = get_post_meta( $post->ID, '_gp_author_name', true );
	$bio = get_post_meta( $post->ID, '_gp_author_bio', true );
	$link = get_post_meta( $post->ID, '_gp_author_website', true );
	if(!empty($name) || !empty($bio) || !empty($link)) {
		$screens = array( 'post' );
		foreach ($screens as $screen) {
			add_meta_box(
				'gpbox_sectionid',
				__( 'Author info boxes', 'gpbox_textdomain' ),
				'gpbox_inner_custom_box',
				$screen
			);
		}
	}
}

/* Prints the box content */
function gpbox_inner_custom_box( $post ) {

  wp_nonce_field( plugin_basename( __FILE__ ), 'gpbox_noncename' );
  $bio = get_post_meta( $post->ID, '_gp_author_bio', true );
   echo '<p><label for="_gp_author_bio" style="width: 150px; display: block; float: left;">';
       _e("Author Bio", 'gpbox_textdomain' );
  echo '</label> ';
  echo '<textarea id="_gp_author_bio" name="_gp_author_bio" style="width: 350px;" rows="8">'.esc_attr($bio).'</textarea></p>';
}

/* When the post is saved, saves our custom data */
function gpbox_save_postdata( $post_id ) {
  if ( 'post' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) )
        return;
  } else {
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;
  }

  if ( ! isset( $_POST['gpbox_noncename'] ) || ! wp_verify_nonce( $_POST['gpbox_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  $post_ID = $_POST['post_ID'];

  $authorbio = sanitize_text_field( $_POST['_gp_author_bio'] );

  add_post_meta($post_ID, '_gp_author_bio', $authorbio, true) or
  update_post_meta($post_ID, '_gp_author_bio', $authorbio);
  
}

function gp_display_after_posts($content){
  global $post;
  if (is_single()) {
	$bio = get_post_meta( $post->ID, '_gp_author_bio', true );

    $content .= '<div style="margin: 25px 0; padding: 15px 0; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;">'.$bio.'</div>';

  }
  return $content;
}
add_filter( "the_content", "gp_display_after_posts" );

?>