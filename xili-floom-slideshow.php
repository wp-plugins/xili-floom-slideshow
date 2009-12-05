<?php
/*
Plugin Name: xili-floom-slideshow
Plugin URI: http://dev.xiligroup.com/xili-floom-slideshow/
Description: xili-floom-slideshow integrate the floom slideshow from Oskar in wordpress theme -
Author: MS dev.xiligroup team
Version: 0.9.3
Author URI: http://dev.xiligroup.com
*/ 

# This plugin is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public
# License as published by the Free Software Foundation; either
# version 2.1 of the License, or (at your option) any later version.
#
# This plugin is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
# Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public
# License along with this plugin; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

# 0.9.3 add gold params
# 0.9.2 add for pictures orderby menu_order - add hooks - fix quoted captions
# 0.9.1 xilitheme-select plugin compatibility, add options to fireEvents, fixes,...
# 0.9.0 First public
# Version date 091126 (beta) - MS

/** * multilingue for admin pages and menu */

load_plugin_textdomain('xilifloomslideshow',PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
/** * version */
define('XILIFLOOM_VER','0.9.3');

if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
define('XILIFLOOM_ABSPATH', WP_CONTENT_DIR.'/plugins/' . dirname(plugin_basename(__FILE__)) . '/');

	
define('FLOOMPATH',XILIFLOOM_ABSPATH.'floom');
define('FLOOMURL',get_bloginfo('wpurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/floom');
/* the personalization are saved inside the theme and not plugin */
define('XILIFLOOM_INCURTHEMEURL',get_bloginfo('template_directory'));
define('XILIFLOOM_INCURTHEMEPATH',get_template_directory());
define('XILIFLOOM_SUBFLOOMFOLDER','floom');

/**
 * Insert mootools and floom js and css in wp_head()
 *
 *
 */
if (!is_admin()) {
	
	$xilifloom_name_selector = ""; /* used in array callback to subselect image filename */
	/**
	 * option if mootools is elsewhere and managed in functions.php by a xilifloom_theme_header function 
	 */
	function is_floom_intheme () {
		if (function_exists('xilifloom_theme_header')) {
			return false;
		} else	{
			return true;
		}	
	}
	function xilifloom_header () {
		/* option if mootools is elsewhere */
		if (is_floom_intheme()) {
			wp_enqueue_script('mootools-core',FLOOMURL.'/js/mootools-core.js','','1.2.4');
			wp_enqueue_script('mootools-more',FLOOMURL.'/js/mootools-more.js',array('mootools-core'),'1.2.4.2');
			wp_enqueue_script('floom',FLOOMURL.'/js/floom-1.0.js',array('mootools-core','mootools-more'),'1.0');
		}
	}
	add_action('wp_print_scripts', 'xilifloom_header');
	/**
	 *
	 * @ updated 0.9.1 for xilitheme-select compatibility
	 *
	 */
	function xilifloom_stylesheet () {
		global $wp_ismobile;
		if (class_exists('xilithemeselector')) {
			if ($wp_ismobile->iphone) {
				$ThemeStyleFilePath = str_replace(get_option('template'),'',XILIFLOOM_INCURTHEMEPATH).$wp_ismobile->newfolder.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/floom.css';
				$ThemeStyleFileURL = str_replace(get_option('template'),'',XILIFLOOM_INCURTHEMEURL).$wp_ismobile->newfolder.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/floom.css';
				$DefaultStyleFilePath = FLOOMPATH.'/css/floom_4touch.css';
				$DefaultStyleFileURL = FLOOMURL.'/css/floom_4touch.css';
			} else {
				$ThemeStyleFilePath = XILIFLOOM_INCURTHEMEPATH.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/floom.css';
				$ThemeStyleFileURL = XILIFLOOM_INCURTHEMEURL.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/floom.css';
				$DefaultStyleFilePath = FLOOMPATH.'/css/floom.css';
				$DefaultStyleFileURL = FLOOMURL.'/css/floom.css';
			}	
		} else {
			$ThemeStyleFilePath = XILIFLOOM_INCURTHEMEPATH.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/floom.css';
			$ThemeStyleFileURL = XILIFLOOM_INCURTHEMEURL.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/floom.css';
			$DefaultStyleFilePath = FLOOMPATH.'/css/floom.css';
			$DefaultStyleFileURL = FLOOMURL.'/css/floom.css';
		}
   			if (file_exists($ThemeStyleFilePath)){	
        		wp_register_style('floomStyleSheet',$ThemeStyleFileURL ,false,'1.0','screen'); 
				wp_enqueue_style( 'floomStyleSheet');
    		} elseif (file_exists($DefaultStyleFilePath)) {
    			
    			wp_register_style('floomStyleSheet', $DefaultStyleFileURL ,false,'1.0','screen'); 
				wp_enqueue_style( 'floomStyleSheet');
    		}
		
	}
	
	add_action('wp_print_styles', 'xilifloom_stylesheet');
	
	/** 
	 * subselect images according mask in filename (post_name)
	 *
	 */
	function image_name_filter($image) {
		global $xilifloom_name_selector;
		if (strpos($image->post_name, $xilifloom_name_selector) !== false ) { // fixed 0.9.2
			return true;
		} else {
			return false;
		}
	}
	/**
	 * get values in current post way
	 * @since 0.9.2
	 *
	 */
	function xilifloom_get_values() {
		global $post, $xilifloom_name_selector;
		$xili_floom_values = array();
		/* some vars can be set inside each post */
		$children = get_post_meta($post->ID, 'floom_parentID', true); /* images are kept in another post */
		if ('' != $children) $xili_floom_values['children'] = $children;
		
		$xilifloom_name_selector = get_post_meta($post->ID, 'floom_subname', true); /* ever lowercase = slug*/
		
		$title_desc = get_post_meta($post->ID, 'floom_title_desc', true);
		if ('' != $title_desc) $xili_floom_values['title_desc'] = $title_desc;
		$floom_divs = get_post_meta($post->ID, 'floom_divs', true);
		if ('' != $floom_divs) $xili_floom_values['floom_divs'] = $floom_divs;
		$amount = get_post_meta($post->ID, 'floom_amount', true);
		if ('' != $amount) $xili_floom_values['amount'] = $amount;
		$interval = get_post_meta($post->ID, 'floom_interval', true);
		if ('' != $interval) $xili_floom_values['interval'] = $interval;
		$axis = get_post_meta($post->ID, 'floom_axis', true);
		if ('' != $axis) $xili_floom_values['axis'] = $axis;
		$captions = get_post_meta($post->ID, 'floom_captions', true);
		if ('' != $captions) $xili_floom_values['captions'] = $captions;
		$progressbar = get_post_meta($post->ID, 'floom_progressbar', true);
		if ('' != $progressbar) $xili_floom_values['progressbar'] = $progressbar;
		$container = get_post_meta($post->ID, 'floom_container', true);
		if ('' != $container) $xili_floom_values['container'] = $container;
		$display = get_post_meta($post->ID, 'floom_display', true);
		if ('' != $display) $xili_floom_values['display'] = $display;
		
		return $xili_floom_values;
	}
	
	/** 
	 * insert javascript in header
	 * @updated 0.9.3 
	 *
	 */
	function xilifloom_insert_script() { 
		global $xili_settings, $xilifloom_name_selector, $post;
		
		if (has_filter('xili_floom_get_values')) {
			$xili_floom_values = apply_filters('xili_floom_get_values','');
		} else {
			$xili_floom_values = xilifloom_get_values();
		}
		
		$defaults =& $xili_settings;
		$defaults['title_desc']	= 1;
		$defaults['children'] = $post->ID;
		$the_xili_floom_values = array_merge( $defaults, $xili_floom_values );
		extract($the_xili_floom_values, EXTR_SKIP);
		
		/* if filter images came from other sources */
		if (has_filter('xili_floom_get_images')) {
			$images = apply_filters('xili_floom_get_images',$children, $xilifloom_name_selector);
		} else {
			
			$images = xilifloom_get_images($children, $xilifloom_name_selector);
		}
		
				?>
		<!-- added by xili-floom-slideshow plugin <?php echo XILIFLOOM_VER; ?> -->
		<script type="text/javascript" charset="utf-8">
		window.addEvent('domready', function(e) {	
			if ($chk($('<?php echo $floom_divs; ?>'))) {
			<?php /* images src absolute url so slidesBase = '' */ 
			if (false !== $images) {
				
				echo "var slides = [";
				$i=0;
				foreach ($images as $attach_ID => $image)	 {
					 if ($i==0)	{
					 	echo "{image: '";	
					 } else {
					 	echo ", {image: '";
					 }
					 switch ($title_desc) {
					 	case 1:
					 		$caption = addslashes($image->post_title);
					 		break;
					 	case 2:
					 		$caption = addslashes($image->post_content);
					 		break;
					 	case 3:
					 		$caption = addslashes($image->post_title).'<br/><span class="'.$xili_settings['prefix'].'desc">'.addslashes($image->post_content).'</span>';	
					 		break;
					 } 	
					 echo $image->guid."', caption: '".$caption."' }";
					 $i++;
			}	
				echo "];";
				//}
			}
			
			if ((false === $images && $display == 'none') || (count($images) < 1 && $display == 'none')) {
				if (''== $nonefunction) { /* container is not displayed */?>
				$('<?php echo $container; ?>').setStyle('display','none');
				<?php } else { 
					echo $nonefunction.'();'; /* js function called when no images to transform container */
					}
				} else { ?>
				// Floom or xiliFloom
				theFloom = new <?php echo $xili_settings['goldparam']; ?>Floom('<?php echo $floom_divs; ?>',slides, {
				prefix: '<?php echo $xili_settings['prefix']; /* only global settings */?>',
				amount: <?php echo $amount; ?>,
				animation: <?php echo $xili_settings['animation']; /* only global settings */ ?>,
				interval: <?php echo $interval; ?>,
				axis: '<?php echo $axis; ?>',
				slidesBase: '<?php echo $xili_settings['slidesBase']; /* only global settings */ ?>',
				sliceFxIn: { top: <?php echo $xili_settings['sliceFxIn']['top']; /* only global settings */ ?> },
				captions: <?php echo $captions; ?>,
				progressbar: <?php echo $progressbar; 
				if (isset($onSlideChange) && 'empty' != $onSlideChange) { ?>,
				onSlideChange: function(curslide){
					<?php echo $onSlideChange ;?>(curslide);}
				<?php }
				if (isset($onPreload) && 'empty' != $onPreload) { ?>,
				onPreload: function(curslide){
					<?php echo $onPreload ;?>(curslide);}
				<?php } /* other documented events (onFirst, onLast - not yet in floom js */
				/* extended functions */ 
				if ($xili_settings['goldparam'] != '') {
				if (isset($onLast) && 'empty' != $onLast ) { ?>, 
				onLast: function(curslide,s){
					<?php echo $onLast; ?>(curslide,s);}
				<?php } 
				if (isset($onFirst) && 'empty' != $onFirst) { ?>, 
				onFirst: function(curslide){
					<?php echo $onFirst; ?>(curslide);}
				<?php }
				do_action('xili_floom_events',$the_xili_floom_values); /* to add other events 0.9.3*/
				}
				?> 
				
				});
				
				<?php } ?>	
			};
		});
		
		</script>
	<?php 		
	}
	
	add_action('wp_head', 'xilifloom_insert_script',20);
}

/**
 * Create list of images (and caption) from post_id
 * 
 * @updated 0.9.2 - add orderby - 0.9.3 - sub-selection as LIKE in sql with one or two %
 */
function xilifloom_get_images($post_ID, $post_name_selector) {
 	if (''!= $post_name_selector) {
	 	add_filter('posts_where_request', 'where_post_name_subselect');
	 	$images =& get_children( 'post_parent='.$post_ID.'&post_type=attachment&post_mime_type=image&orderby=menu_order&order=asc&suppress_filters=0' );
	 	remove_filter('posts_where_request', 'where_post_name_subselect');
 	} else { 		
	 	$images =& get_children( 'post_parent='.$post_ID.'&post_type=attachment&post_mime_type=image&orderby=menu_order&order=asc' );
	}	
 	return $images;
 }
 
/**
 * Filter list of images with post_name
 * 
 * @since 0.9.3 - sub-selection
 */ 
function where_post_name_subselect($where){
	global $wpdb, $xilifloom_name_selector ;
	$where .= " AND $wpdb->posts.post_name LIKE '".$xilifloom_name_selector."' ";
	return $where;
}
 
/**
 * fill shortcode with datas
 * shortcode params : frame_id = id of the frame's div, blinds_id = id of the images and blinds divs
 *
 */
function insert_a_floom( $atts, $content = null ) {
	$arr_result = shortcode_atts(array('frame_id'=>'blinds-cont','blinds_id'=>'blinds'), $atts);
   	return '<div id="'.$arr_result['frame_id'].'"><div id="'.$arr_result['blinds_id'].'"></div></div>';
}
add_shortcode('xilifloom', 'insert_a_floom');

function xilifloom_default_settings() {
	$submitted_settings = array(
		'floom_divs' => 'blinds',
		'prefix' =>'floom_',
		'amount' => '24',
		'animation' => '80',
		'interval' => '8000',
		'axis' => 'vertical',
		'progressbar' => 'false',
		'captions' => 'true',
		'slidesBase' => '',
		'sliceFxIn' => array('top'=>'20'),
		'onSlideChange' => 'empty',
		'onPreload' => 'empty',
		'container' => 'blinds-cont',
		'display' => 'block',
		'nonefunction' => '',
		'onFirst' => 'empty', 
	 	'onLast' => 'empty',
	 	'goldparam' => '',
		'version' => '1.3'
	);
	update_option('xilifloomslideshow_settings', $submitted_settings);	
}


$xili_settings = get_option('xilifloomslideshow_settings');
if(empty($xili_settings)) {
 	xilifloom_default_settings();
	$xili_settings = get_option('xilifloomslideshow_settings');			
}
if ($xili_settings['version'] < '1.1') {
	$xili_settings['version'] = '1.1';
	$xili_settings['onSlideChange'] = "empty"; /* possible to add firedEvent function name */
	$xili_settings['onPreload'] = "empty";
	update_option('xilifloomslideshow_settings', $xili_settings);
}
if ($xili_settings['version'] < '1.2') {
	$xili_settings['version'] = '1.2';
	$xili_settings['container'] = 'blinds-cont'; /* to hidden if necessary */
	$xili_settings['display'] = 'block';
	update_option('xilifloomslideshow_settings', $xili_settings);
} 
if ($xili_settings['version'] < '1.3') {
	$xili_settings['version'] = '1.3';
	$xili_settings['nonefunction'] = '';
	$xili_settings['onFirst'] = 'empty'; 
	$xili_settings['onLast'] = 'empty';
	$xili_settings['goldparam'] = '';
	update_option('xilifloomslideshow_settings', $xili_settings);
}

/** 
 * add admin menu and associated page
 */
add_action('admin_menu', 'xili_addfloom_pages');
add_filter('plugin_action_links', 'filter_plugin_actions', 10, 2);
function xili_addfloom_pages() {
	global $thehook;
	$thehook = add_options_page(__('xili floom slideshow','xilifloomslideshow'), __('xili floom slideshow','xilifloomslideshow'), 'import', 'floom-slideshow_page', 'xili_floom_active_menu');
	add_action('load-'.$thehook, 'on_load_page');
}
function on_load_page() {
	global $thehook;
			wp_enqueue_script('common');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');
	add_meta_box('xilifloom-sidebox-1', __('Message','xilifloomslideshow'), 'on_sidebox_1_content', $thehook , 'side', 'core');		
}
/**
 * Add action link(s) to plugins page
 * 
 * @since 0.9.3
 * @author MS
 * @copyright Dion Hulse, http://dd32.id.au/wordpress-plugins/?configure-link and scripts@schloebe.de
 */
function filter_plugin_actions($links, $file){
	static $this_plugin;
	if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);
	if( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=floom-slideshow_page">' . __('Settings') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;
}
function  on_sidebox_1_content($data) { 
		extract($data);
		?>
	 	<h4><?php _e('Note:','xilifloomslideshow') ?></h4>
		<p><?php echo $message;?></p>
		<p><?php echo $action;?></p>
		<?php
	}
	
function  on_normal_1_content($data) { 
		extract($data);
		?>
	 	
		<p><cite><a href='http://dev.xiligroup.com/xili-floom-slideshow/' target='_blank'>xili-floom-slideshow</a></cite>&nbsp;&nbsp;<?php _e("plugin integrates the modules of Floom slideshow in your current theme and detect the specific configurations for Floom in your theme.","xilifloomslideshow"); ?></p>
		<p>-&nbsp;<?php _e("The current theme is here","xilifloomslideshow"); echo ": <em>".get_bloginfo('template_directory') ?>. </em><br /><br />-&nbsp;
		<?php _e("The active floom.css is here","xilifloomslideshow"); 
		$themejsfolder = XILIFLOOM_INCURTHEMEPATH.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/';
		if (file_exists($themejsfolder.'/css/floom.css')) {
			echo ": <em>".XILIFLOOM_INCURTHEMEURL.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/' ;}
		else {
			echo ": <em>".FLOOMURL.'/css/';
			}
			?>. </em><br /><br />-&nbsp;
			<?php if (class_exists('xilithemeselector')) { _e("xilitheme-select plugin active","xilifloomslideshow"); }?>.<br />&nbsp;
		
		<?php
	}	
	
function  on_normal_2_content($data) { 
		$xili_settingsaved = get_option('xilifloomslideshow_settings');
		extract($data);
		$update_nonce = wp_create_nonce('xilifloomoptions');
		?>
	<p><?php _e("The Floom slideshow javascript contains a lot of parameters. Instead modifying source, here, you can change some parameters for your whole site. Some settings are possible for one post (see docs).","xilifloomslideshow");?></p><br/>
	<fieldset style="margin:2px; padding:12px 6px; border:1px solid #ccc;"><legend><?php _e("Parameters as currently saved","xilifloomslideshow");?></legend>
	<?php print_r($xili_settingsaved); ?>
	</fieldset>
	<br/>
	<fieldset style="margin:2px; padding:12px 6px; border:1px solid #ccc;"><legend><?php _e("Parameters list","xilifloomslideshow");?></legend>
	<label for="paramname"><?php _e("Parameter:","xilifloomslideshow");?>&nbsp;
	<select id="paramname" name="paramname">
	<option>???</option>
	<?php
	$paramnames = array_keys($xili_settingsaved);
	foreach ($paramnames as $paramname) {
		if ($paramname != 'version' && $paramname != 'slidesBase' && $paramname != 'goldparam') {
			if ($xili_settingsaved['goldparam'] == '' && $paramname != 'onFirst' && $paramname != 'onLast' ) 
				{ echo '<option>'.$paramname.'</option>'; }
				elseif ($xili_settingsaved['goldparam'] != '')
				{ echo '<option>'.$paramname.'</option>'; }
		}
	}
	?>
	</select></label>
	<label for="paramval"><input id="paramval" name="paramval" /></label>
	<div class='submit'>
		<input id='updateparams' name='updateparams' type='submit' tabindex='6' value="<?php _e('Update','xilifloomslideshow') ?>" /></div>
	</fieldset>
	<br/>
	<fieldset style="margin:2px; padding:12px 6px; border:1px solid #ccc;"><legend><?php _e("Special gold parameters","xilifloomslideshow");?></legend>
	<p><?php _e("These parameters are reserved if extended js class of the original Floom class is used","xilifloomslideshow");?></p>
	<label for="goldparam"><?php _e("Parameter:","xilifloomslideshow");?>&nbsp;
	<select id="goldparam" name="goldparam">
	<?php  $checked = ($xili_settingsaved['goldparam'] == 'xili') ? 'selected = "selected"' : '' ; ?>
	<option value=""><?php _e("Gold inactive","xilifloomslideshow");?></option>
	<option value="xili" <?php echo $checked; ?> ><?php _e("Gold active","xilifloomslideshow");?></option>
	</select></label>
	<div class='submit'>
		<input id='setgoldparams' name='setgoldparams' type='submit' tabindex='6' value="<?php _e('Update','xilifloomslideshow') ?>" /></div>
	</fieldset>			
	<p><?php _e("For more info about javascript see Floom documentation. Floom slideshow javascript was designed by Oskar Krawczyk (<a href='http://nouincolor.com/' target='_blank' >http://nouincolor.com/</a>) under MIT license. This plugin for Wordpress don't modify the original js source.","xilifloomslideshow");?></p>
	<?php
	echo wp_nonce_field( 'xilifloomoptions', '_ajax_nonce', true, false );/**/
	}
	
function xili_floom_active_menu(){
	global $thehook, $xili_settings;
	
	if (isset($_POST['updateparams'])) {
			$action='updateparams';
	}
	if (isset($_POST['setgoldparams'])) {
			$action='setgoldparams';
	}
	$message = $action ;
		switch($action) {
			case 'updateparams';
				$paramname = $_POST['paramname'];
				$paramval = $_POST['paramval'];
				if ($paramname !='???' && $paramval !="" ) {
					if ($paramname =='sliceFxIn') {
						$paramvalarr = explode('=>',$paramval);
						$key = $paramvalarr[0];
						$xili_settings[$paramname][$key] = $paramvalarr[1];
					} else {
						$xili_settings[$paramname] = $paramval;
					}
					update_option('xilifloomslideshow_settings', $xili_settings);
					$message .= ' ok ('.$paramname.' = '.$paramval.') ';
				} else {
					$message .= ' incorrect value';
				}	
				break;
			case 'setgoldparams'; /* since 0.9.3 */
				$paramval = $_POST['goldparam'];
				$xili_settings['goldparam'] = $paramval;
				update_option('xilifloomslideshow_settings', $xili_settings);
				$displaypar = ($paramval != '') ? 'active' :'inactive' ;
				$message .= ' ok (gold param changed to '.$displaypar.') ';
				break;
			default:
			$message = ' ';
		}
	
	$data = array('message'=>$message, 'action'=>$action);
	/* register the main boxes always available */
		add_meta_box('xilifloom-normal-1', __('Style Settings','xilifloomslideshow'),'on_normal_1_content', $thehook , 'normal', 'core');
		add_meta_box('xilifloom-normal-2', __('SlideShow Settings','xilifloomslideshow'),'on_normal_2_content', $thehook , 'normal', 'core');
	?>
	<div id="xilifloom-settings" class="wrap">
		<?php screen_icon('options-general'); ?>
		<h2><?php _e("xili-floom-slideshow settings","xilifloomslideshow"); ?></h2>
		<form name="add" id="add" method="post" action="options-general.php?page=floom-slideshow_page">
				<input type="hidden" name="action" value="<?php echo $actiontype ?>" />
				<?php wp_nonce_field('xilifloom-settings'); ?>
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes($thehook, 'side', $data); ?>
					</div>
				
					<div id="post-body" class="has-sidebar has-right-sidebar">
						<div id="post-body-content" class="has-sidebar-content" style="min-width:360px">
					
	   					<?php do_meta_boxes($thehook, 'normal', $data); ?>
						</div>
		
		
		
		<h4><a href="http://dev.xiligroup.com/xili-floom-slideshow" title="Plugin page and docs" target="_blank" style="text-decoration:none" ><img style="vertical-align:middle" src="<?php echo WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/xilifloom-logo-32.gif'; ?>" alt="xili-floom logo"/>  xili-floom-slideshow</a> - © <a href="http://dev.xiligroup.com" target="_blank" title="<?php _e('Author'); ?>" >xiligroup.com</a>™ - msc 2009 - v. <?php echo XILIFLOOM_VER; ?></h4>
		</div>
				</div>
		</form>
	</div>
	<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $thehook; ?>');
			});
			//]]>
		</script>
		<?php 
		}
?>