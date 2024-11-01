<div class="wrap">
    <h2>WP Post Notification Settings </h2><br>
	<div id="wpn-tab-menu"><a id="wpn-general" class="wpn-tab-links active" >General</a> <a  id="wpn-shortcode" class="wpn-tab-links">Shortcode</a> <a  id="wpn-support" class="wpn-tab-links">Support</a> <a  id="wpn-other" class="wpn-tab-links">Other Plugins</a></div>
    <form method="post" action="options.php"> 
        <div class="wpn-setting">
			<!-- General Setting -->	
			<div class="first wpn-tab" id="div-wpn-general">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="wpn_enable">Enable</label></th>
						<td><input type="checkbox" value="1" name="wpn_enable" id="wpn_enable" <?php checked(get_option('wpn_enable'),1); ?> /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="wpn_display_date">Display Time History</label></th>
						<td><input type="checkbox" value="1" name="wpn_display_date" id="wpn_display_date" <?php checked(get_option('wpn_display_date'),1); ?> /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="wpn_delay_time">Delay Time</label></th>
						<td><input type="text"  name="wpn_delay_time" id="wpn_delay_time" value="<?php echo get_option('wpn_delay_time'); ?>" placeholder="5000"/>(ms)</td>
					</tr>
					<!-- Addon Fileds -->
					<tr valign="top">
						<th scope="row"><label for="wpn_enable">Flash News with Floating sidebar</label></th>
						<td><input type="checkbox" value="1" name="wpn_flash_enable" id="wpn_flash_enable" <?php checked(get_option('wpn_flash_enable'),1); ?> /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="wpn_position">Flash News Sidebar Position</label></th>
						<td><select name="wpn_position" id="wpn_position"> 
						<option value="left" <?php selected(get_option('wpn_position'),'left'); ?>>Left</option> 
						<option value="right" <?php selected(get_option('wpn_position'),'right'); ?>>Right</option> 
						<select/></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="wpn_hide_on_home">Hide Flash News Sidebar on:</label></th>
						<td> 
						Home <input type="checkbox" value="1" name="wpn_hide_on_home" id="wpn_hide_on_home" <?php checked(get_option('wpn_hide_on_home'),1); ?> />
						Tags <input type="checkbox" value="1" name="wpn_hide_on_tags" id="wpn_hide_on_tags" <?php checked(get_option('wpn_hide_on_tags'),1); ?> />
						Category <input type="checkbox" value="1" name="wpn_hide_on_category" id="wpn_hide_on_category" <?php checked(get_option('wpn_hide_on_category'),1); ?> />
						Search <input type="checkbox" value="1" name="wpn_hide_on_search" id="wpn_hide_on_search" <?php checked(get_option('wpn_hide_on_search'),1); ?> />
						Author <input type="checkbox" value="1" name="wpn_hide_on_author" id="wpn_hide_on_author" <?php checked(get_option('wpn_hide_on_author'),1); ?> />
						Archive <input type="checkbox" value="1" name="wpn_hide_on_archive" id="wpn_hide_on_archive" <?php checked(get_option('wpn_hide_on_archive'),1); ?> />
						</td>
					</tr>
					<tr valign="top">
					<th scope="row" nowrap>Hide Flash News Sidebar for post type</th>
					<td>
					<?php $wpn_hide_on_post_type=get_option('wpn_hide_on_post_type');?>
					<select name="wpn_hide_on_post_type[]" id="wpn_hide_on_post_type" style="width:500px;" multiple>
						
					  <?php 
						$args = array(
						   'public'   => true,
						   '_builtin' => false
						);

						$output = 'names'; // names or objects, note names is the default
						$operator = 'and'; // 'and' or 'or'

						$post_types = get_post_types( $args, $output, $operator ); 
						array_push($post_types,'post');array_push($post_types,'page');
						foreach ( $post_types  as $post_type ) {

							echo '<option value="'.$post_type.'" '.selected(true, in_array($post_type, $wpn_hide_on_post_type), false).'>'.$post_type.'</option>';
						}

						?>
					 <option value="" <?php if($wpn_hide_on_post_type==''){ echo 'selected="selected"';}?>>None</option>
				 
					</select>
					
					</td>
					</tr>	
					<tr valign="top">
					<th scope="row" nowrap>Hide Flash News Sidebar for taxonomy type</th>
					<td>
						<?php 
						$selectedTerm = get_option('wpn_exclude_term_type');
						$args = array(
					  'public'   => true,
					  '_builtin' => false
					  
						); 
						$output = 'names'; // or objects
						$operator = 'and'; // 'and' or 'or'
						$taxonomies = get_taxonomies( $args, $output, $operator ); 
						?>
						
						<select name="wpn_exclude_term_type[]" id="wpn_exclude_term_type" multiple>
						
						<?php
						if ( $taxonomies ) {
						foreach ( $taxonomies as $taxonomy ) {
						echo '<option value="'.$taxonomy.'" '.selected(in_array($taxonomy, $selectedTerm)).'>'.$taxonomy.'</option>';
					    }
					   }

						?>    
						<option value="" <?php if($selectedTerm==''){ echo 'selected="selected"';}?>>None</option>
						</select>
					</td>
					</tr>	
					<tr valign="top">
						<th scope="row"><label for="wpn_number_of_order">Number of post</label></th>
						<td><input type="text" value="<?php echo get_option('wpn_number_of_order'); ?>" name="wpn_number_of_order" id="wpn_number_of_order" /></td>
					</tr>
				</table>
			</div>
			<div class="wpn-tab" id="div-wpn-shortcode">
				<h2>Shortcode</h2>
				<p><code>[wpn_posts]</code></p>
			</div>
			<div class="wpn-tab" id="div-wpn-support"> <h2>Support</h2> 
				<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZEMSYQUZRUK6A" target="_blank" style="font-size: 17px; font-weight: bold;"><img src="<?php echo  plugins_url( '../images/btn_donate_LG.gif' , __FILE__ );?>" title="Donate for this plugin"></a></p>
				<p><a href="mailto:info@wp-experts.in">Plugin Author</a></p>

			</div>
			<div class="last wpn-tab" id="div-wpn-other">
				<p><strong>Our Other Plugins:</strong><br>
				<h2>Other plugins</h2>
				<p>
				  <ol>
					<li><a href="https://wordpress.org/plugins/custom-share-buttons-with-floating-sidebar" target="_blank">Custom Share Buttons With Floating Sidebar</a></li>
					<li><a href="https://wordpress.org/plugins/seo-manager/" target="_blank">SEO Manager</a></li>
					<li><a href="https://wordpress.org/plugins/protect-wp-admin/" target="_blank">Protect WP-Admin</a></li>
					<li><a href="https://wordpress.org/plugins/wp-categories-widget/" target="_blank">WP Categories Widget</a></li>
					<li><a href="https://wordpress.org/plugins/wp-protect-content/" target="_blank">WP Protect Content</a></li>
					<li><a href="https://wordpress.org/plugins/wp-version-remover/" target="_blank">WP Version Remover</a></li>
					<li><a href="https://wordpress.org/plugins/wp-posts-widget/" target="_blank">WP Post Widget</a></li>
					<li><a href="https://wordpress.org/plugins/wp-importer" target="_blank">WP Importer</a></li>
					<li><a href="https://wordpress.org/plugins/wp-csv-importer/" target="_blank">WP CSV Importer</a></li>
					<li><a href="https://wordpress.org/plugins/wp-testimonial/" target="_blank">WP Testimonial</a></li>
					<li><a href="https://wordpress.org/plugins/wc-sales-count-manager/" target="_blank">WooCommerce Sales Count Manager</a></li>
					<li><a href="https://wordpress.org/plugins/wp-social-buttons/" target="_blank">WP Social Buttons</a></li>
					<li><a href="https://wordpress.org/plugins/wp-youtube-gallery/" target="_blank">WP Youtube Gallery</a></li>
					<li><a href="https://wordpress.org/plugins/tweets-slider/" target="_blank">Tweets Slider</a></li>
					<li><a href="https://wordpress.org/plugins/rg-responsive-gallery/" target="_blank">RG Responsive Slider</a></li>
					<li><a href="https://wordpress.org/plugins/cf7-advance-security" target="_blank">Contact Form 7 Advance Security WP-Admin</a></li>
					<li><a href="https://wordpress.org/plugins/wp-easy-recipe/" target="_blank">WP Easy Recipe</a></li>
                </ol>
				</p>
			</div>
		</div>
		<?php settings_fields('wpn-group'); ?>
        <?php @submit_button(); ?>
    </form>
</div>
