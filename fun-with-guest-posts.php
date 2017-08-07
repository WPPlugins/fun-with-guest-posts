<?php
/*
Plugin Name: Fun With Guest Posts
Plugin URI: http://www.wp-fun.com/fun-with-guest-posts/
Description: Allows you to include a post on your blog that has been published elsewhere without stealing their thunder.
Author: Andrew Rickmann
Version: 1.1
Author URI: http://www.arickmann.co.uk
*/ 

/*  Copyright 2007  Andrew Rickmann  (email : mail@arickmann.co.uk)

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


if (!class_exists('fw_guest_posts')) {
	class fw_guest_posts{
	
	function updatePostUI(){
		
		if ( function_exists('add_meta_box')){return;}
		
	global $post;
	if ( isset($post) ) {
		$guest_post_url = clean_url(get_post_meta($post->ID, '_fw_guest_posted_to' , $single = true));	
	} else {
		if (isset($_POST['fw_guest_url'])) {
		$guest_post_url = clean_url($_POST['fw_guest_url']);
		} else {
		$guest_post_url = '';
		}
	}

	?>
	<div class="dbx-b-ox-wrapper">
		<fieldset id="trackbacksdiv" class="dbx-box">
			<div class="dbx-h-andle-wrapper">
				<h3 class="dbx-handle">Guest Post Options</h3>
			</div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">
					<label for="fw_guest_url"><?php _e('Guest Posted to: (enter full web address)', 'Guest post'); ?></label>
<input type="text" name="fw_guest_url" style="width:415px;" value="<?php echo $guest_post_url;?>" />
<input type="hidden" name="funwithguestposts-nonce" value="<?php echo wp_create_nonce('funwithguestposts') ?>" />
				</div>
			</div>
		</fieldset>
	</div>
	<?php
	
	}
	
	function interceptPostSave($id){
		if (isset($_POST['fw_guest_url'])) {
			if (!wp_verify_nonce($_POST['funwithguestposts-nonce'], 'funwithguestposts')) {
         		return;
		 	}
			if (!isset($id)) {
				$id = $_POST['post_ID'];
				if (!is_numeric($id)) {
					return;
				}
			}
			$meta_key = '_fw_guest_posted_to';
			$meta_value = $_POST['fw_guest_url'];
			if (add_post_meta($id, $meta_key, $meta_value, $unique = true) === false) {
				//if add_post returns false a key already exists so go to update mode without allowing for previous values
				update_post_meta($id, $meta_key, $meta_value);
			}
		}
	}
	
	function replaceContent($content){
		global $post;
		if ($guest_url = is_guest_post()) {
			$newContent = '<p>'.$post->post_excerpt.'</p>';
			$newContent .= '<p><a href="'.$guest_url.'" title="Read the rest of the article">Read the rest at '.$guest_url.'</a></p>';
			return $newContent;
		}
		return $content;
	}
	
	function replaceExcerpt($excerpt){
		if ($guest_url = is_guest_post()) {
			$excerpt = '<p>'.$excerpt.'</p>' . '<p><a href="'.$guest_url.'" title="Read the rest of the article">Read the rest at:'.$guest_url.'</a></p>';
		}
		return $excerpt;
	}
	
		/**
		 * add_meta_boxes runs in the admin head to add boxes to the edit_post page.
		 * @version 2.5
		 *  
		 */
		public function add_meta_boxes(){
			if ( !function_exists('add_meta_box')){return;}
			add_meta_box('fw_guest_posts', 'Guest Posted', array(&$this,'add_post_option'), 'post', 'advanced');
		}
		
		/**
		 * add_post_option - the contents of the meta box
		 * @return 
		 */
		public function add_post_option(){
			global $post;
			if ( isset($post) ) {
				$guest_post_url = clean_url(get_post_meta($post->ID, '_fw_guest_posted_to' , $single = true));	
			} else {
				if (isset($_POST['fw_guest_url'])) {
				$guest_post_url = clean_url($_POST['fw_guest_url']);
			} else {
				$guest_post_url = '';
			}
			}
			?>
			<label for="fw_guest_url"><?php _e('Guest Posted to: (enter full web address)', 'Guest post'); ?></label>
			<input type="text" name="fw_guest_url" style="width:90%;" value="<?php echo $guest_post_url;?>" />
			<input type="hidden" name="funwithguestposts-nonce" value="<?php echo wp_create_nonce('funwithguestposts') ?>" />
			<?php
		}
	
	}
$fw_guest_posts = new fw_guest_posts();
}

if (isset($fw_guest_posts)) {

	//action to add to the post ui
	add_filter('dbx_post_advanced', array($fw_guest_posts, 'updatePostUI'));
	//correction for compatibility with version 2.5
	add_action('admin_head', array($fw_guest_posts,'add_meta_boxes'));
	
	//action to intercept the post save
	add_filter('edit_post',    array($fw_guest_posts, 'interceptPostSave'));
	add_filter('publish_post', array($fw_guest_posts, 'interceptPostSave'));
	add_filter('save_post',    array($fw_guest_posts, 'interceptPostSave'));
	
	//filter to filter the content
	add_filter('the_content',     array($fw_guest_posts, 'replaceContent'), 1);
	add_filter('the_content_rss', array($fw_guest_posts, 'replaceContent'), 1);
	add_filter('the_excerpt',     array($fw_guest_posts, 'replaceExcerpt'), 1);


}

//simple additional function
function is_guest_post(){
	global $post;
	if ($meta_val = get_post_meta($post->ID, '_fw_guest_posted_to', true)){
		if ( strpos($meta_val,'http://') === 0 ) {
			return $meta_val;
		} else {
			return false;
		}
	}
	//if still not returned then must be false
	return false;
}

?>