<?php
/*
 * Plugin Name: MP3 Scraper
 * Plugin URI: http://jordanandree.com
 * Description: A plugin that will scrape any html links to mp3 files in a post or page content area and convert them to secure flash embeds, utilizing the <a href="http://1bit.markwheeler.net/" target="_blank">1 Bit Audio Player</a>.
 * Author: Jordan Andree &bull; Skorinc
 * Version: 1.1
 * Author URI: http://jordanandree.com
*/
add_filter('the_content' , 'mp3Scrape', 0);

register_activation_hook(__FILE__, 'mp3_scraper_activate');

add_action( 'admin_init', 'mp3_scraper_admin_init' );
add_action( 'admin_menu', 'mp3_scraper_menu');

function mp3_scraper_activate(){
	add_option('mp3_scraper_color', '#ffffff');
	add_option('mp3_scraper_size', '10');
}

function mp3_scraper_admin_init(){
	
	register_setting('mp3_scraper_settings', 'mp3_scraper_color');
	register_setting('mp3_scraper_settings', 'mp3_scraper_size');
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery.colorpicker', WP_PLUGIN_URL.'/mp3-scraper/colorpicker/js/colorpicker.js');
	wp_enqueue_style('colorpicker', WP_PLUGIN_URL.'/mp3-scraper/colorpicker/css/colorpicker.css');
	
}

function mp3Scrape( $content ){
	$regex = "#(<a[^>]*?)\.mp3(.*?)<\/a>#s"; 
	
	return preg_replace_callback($regex, 'customEncode', $content);		
}

function customEncode( $matches ){
	$file = explode('"', $matches[0]);
	$file = $file[1];
	
	$array_salt = 'skorincrulezdude';
	$between = ':';
	$base64 = str_shuffle($array_salt).$between.base64_encode($file);
	$movie = get_option('siteurl').'/wp-content/plugins/mp3-scraper/flash/1bit.swf';
	
	return '<span class="mp3player"><embed wmode="transparent" quality="high" bgcolor="transparent" type="application/x-shockwave-flash" width="'.get_option('mp3_scraper_size').'px" height="'.get_option('mp3_scraper_size').'px" src="'.$movie.'" flashvars="skor='.$base64.'&foreColor='.get_option('mp3_scraper_color').'" /></span>';
	
}

function mp3_scraper_menu(){ 	
    $page = add_options_page('MP3 Scraper', 'MP3 Scraper', 'administrator', 'mp3_scraper', 'mp3_scraper_config');	
}

function mp3_scraper_config(){
	?>
	<div class="wrap">
	<h2>MP3 Scraper Settings</h2>
	<form method="post" action="options.php">
	    <table class="form-table">
			<?php settings_fields( 'mp3_scraper_settings' ); ?>
			<!-- visual settings -->
			<tr><td colspan="5"><h3>Visual Settings</h3></td></tr>
			<tr><td>
				<label for="mp3_scraper_color">Color</label>
				<input type="text" value="<?php echo get_option('mp3_scraper_color') ?>" size="40" name="mp3_scraper_color" id="mp3_scraper_color" />
			</td></tr>	
			<tr><td>
				<label for="mp3_scraper_size">Size</label>
				<input type="text" value="<?php echo get_option('mp3_scraper_size') ?>" size="40" name="mp3_scraper_size" id="mp3_scraper_size" />
			</td></tr>
		</table>
		
		
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$('#mp3_scraper_color').ColorPicker({ 
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				},
				onSubmit: function(hsb, hex, rgb, el) {
						$(el).val('#'+hex);
						$(el).ColorPickerHide();
				},

				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				}

			})
			.bind('keyup', function(){
				$(this).ColorPickerSetColor(this.value);
			});

		});
		</script>
		
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
		
	</form>
	
	</div>
	<?php
}

?>