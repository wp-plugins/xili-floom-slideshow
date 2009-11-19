<?php
/*
Plugin Name: xili-floom-slideshow
Plugin URI: http://dev.xiligroup.com/xili-floom-slideshow/
Description: xili-floom-slideshow integrate the floom slideshow from Oskar in wordpress theme -
Author: MS dev.xiligroup team
Version: 0.9.0
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

# Version date 091119 (beta) - MS

/** * multilingue for admin pages and menu */

load_plugin_textdomain('xilifloomslideshow',PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
/** * version */
define('XILIFLOOM_VER','0.9.0');

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
	/* option if mootools is elsewhere and managed in functions.php by a xilifloom_theme_header function */
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
	
	function xilifloom_stylesheet () {
			$ThemeStyleFilePath = XILIFLOOM_INCURTHEMEPATH.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/floom.css';
    		$DefaultStyleFilePath = FLOOMPATH.'/css/floom.css'; 
   	
   			if (file_exists($ThemeStyleFilePath)){
    			
        		wp_register_style('floomStyleSheet', XILIFLOOM_INCURTHEMEURL.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/css/floom.css',false,'1.0','screen'); 
				wp_enqueue_style( 'floomStyleSheet');
    		} elseif (file_exists($DefaultStyleFilePath)) {
    			
    			wp_register_style('floomStyleSheet', FLOOMURL.'/css/floom.css',false,'1.0','screen'); 
				wp_enqueue_style( 'floomStyleSheet');
    		}
		
	}
	
	add_action('wp_print_styles', 'xilifloom_stylesheet');
	
	function xilifloom_insert_script() { 
		global $post, $xili_settings;
		/* some vars can be set inside each post */
		$images = xilifloom_getimages($post->ID);
		$blinds = get_post_meta($post->ID, 'floom_divs', true);
		$blinds = ('' != $blinds) ? $blinds : $xili_settings['floom_divs'] ;
		$captions = get_post_meta($post->ID, 'floom_captions', true);
		$captions = ('' != $captions) ? $captions : $xili_settings['captions'] ;
		$progressbar = get_post_meta($post->ID, 'floom_progressbar', true);
		$progressbar = ('' != $progressbar) ? $progressbar : $xili_settings['progressbar'] ;
		?>
		<script type="text/javascript" charset="utf-8">
			window.addEvent('domready', function(e) {	
			if ($chk($('blinds'))) {
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
			 echo $image->guid."', caption: '".$image->post_title."' }";
			 $i++;
			}	
				echo "];";
			}
			?>
			
			$('<?php echo $xili_settings['floom_divs']; ?>').floom(slides, {
				prefix: '<?php echo $xili_settings['prefix']; ?>',
				amount: <?php echo $xili_settings['amount']; ?>,
				animation: <?php echo $xili_settings['animation']; ?>,
				interval: <?php echo $xili_settings['interval']; ?>,
				axis: '<?php echo $xili_settings['axis']; ?>',
				slidesBase: '<?php echo $xili_settings['slidesBase']; ?>',
				sliceFxIn: { top: <?php echo $xili_settings['sliceFxIn']['top']; ?> },
				captions: <?php echo $captions; ?>,
				progressbar: <?php echo $progressbar; ?>
			});
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
 *
 */
 function xilifloom_getimages($post_ID) {
 	$images =& get_children( 'post_parent='.$post_ID.'&post_type=attachment&post_mime_type=image&order=asc' );
 	
 	//print_r($images);
 	return $images;
 }
 
/**
 * fill shortcode with datas
 *
 *
 */
function insert_a_floom( $atts, $content = null ) {
	$arr_result = shortcode_atts(array('floomcontainer'=>'blinds-cont','floomslides'=>'blinds'), $atts);
   	return '<div id="'.$arr_result['floomcontainer'].'"><div id="'.$arr_result['floomslides'].'"></div></div>';
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
		'version' => '1.0'
	);
	update_option('xilifloomslideshow_settings', $submitted_settings);	
}


$xili_settings = get_option('xilifloomslideshow_settings');
if(empty($xili_settings)) {
 	xilifloom_default_settings();
	$xili_settings = get_option('xilifloomslideshow_settings');			
}


/** 
 * add admin menu and associated page
 */
add_action('admin_menu', 'xili_addfloom_pages');
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
	 	
		<p><cite><a href='http://dev.xiligroup.com/xili-floom-slideshow/' target='_blank'>xili-floom-slideshow</a></cite> <?php _e("plugin integrates the modules of Floom slideshow in your current theme and detect the specific configurations for Floom in your theme.","xilifloomslideshow"); ?></p>
		<p>-&nbsp;<?php _e("The current theme is here","xilifloomslideshow"); echo ": <em>".get_bloginfo('template_directory') ?>. </em><br /><br />-&nbsp;
		<?php _e("The active floom.css is here","xilifloomslideshow"); 
		$themejsfolder = XILIFLOOM_INCURTHEMEPATH.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/';
		if (file_exists($themejsfolder.'/css/floom.css')) {
			echo ": <em>".XILIFLOOM_INCURTHEMEURL.'/'.XILIFLOOM_SUBFLOOMFOLDER.'/' ;}
		else {
			echo ": <em>".FLOOMURL.'/css/';
			}
			?>. </em><br /><br />&nbsp;
		
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
		if ($paramname != 'version' && $paramname != 'slidesBase') 
				{ echo '<option>'.$paramname.'</option>'; }
	}
	?>
	</select></label>
	<label for="paramval"><input id="paramval" name="paramval" /></label>
	<div class='submit'>
		<input id='updateparams' name='updateparams' type='submit' tabindex='6' value="<?php _e('Update','xilifloomslideshow') ?>" /></div>
	</fieldset>
	<br/>			
	<p><?php _e("For more info about javascript see Floom documentation. Floom slideshow javascript was designed by Oskar Krawczyk (<a href='http://nouincolor.com/' target='_blank' >http://nouincolor.com/</a>) under MIT license. This plugin for Wordpress don't modify the original js source.","xilifloomslideshow");?></p>
	<?php
	echo wp_nonce_field( 'xilifloomoptions', '_ajax_nonce', true, false );/**/
	}
	
function xili_floom_active_menu(){
	global $thehook, $xili_settings;
	
	if (isset($_POST['updateparams'])) {
			$action='updateparams';
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