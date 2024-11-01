<?php

// SHOW OPTIONS
function wpts_show_option_page() { 

	// Options in variables
	$wpts_testmode = get_option('wpts_testmode'); 
	$wpts_zipcode = get_option('wpts_zipcode'); 
	$wpts_version = get_option('wpts_version');

	include_once( plugin_dir_path( __FILE__ ) . '/styles.php' ); ?>

	<div class="wrap">
		<h2>Stripe - Options</h2>

		<?php
			if (empty($_SERVER['HTTPS'])) { ?>
				<div class="alert">For Stripe to work in LIVE mode, you need to have SSL working on your site. Contact your hosting company to get one setup or feel free to <a href="support@wptemplates.store">give us a shout</a> :)</div>
			<?php }
		?>
		<form method="post" action="options.php">
			<?php settings_fields( 'wpts_options_group' ); ?>
			<p>General settings for your Stripe setup.</p>
			<table class="wpts_table wp-list-table widefat">
				<tr>
					<th>Company Name:</th>
					<td>
						<input type="text" id="wpts_company_name" name="wpts_company_name" value="<?php echo get_option('wpts_company_name'); ?>" placeholder="Same as your Stripe Account">
					</td>	
					<td>This should be the same as the company name in your Stripe account.</td>			
				</tr>
				<!-- TEST MODE -->
				<tr>
					<th>Test Mode:</th>
					<td>
						<input id="wpts_testmode" name="wpts_testmode" type="checkbox"
							<?php
								if($wpts_testmode) { echo 'checked="checked"'; }
							?>>
					</td>	
					<td>Check this setting when testing your buttons.</td>			
				</tr>
				<tr>
					<th>Live Publish Key:</th>
					<td>
						<input type="text" id="wpts_live_public" name="wpts_live_public" value="<?php echo get_option('wpts_live_public'); ?>">
					</td>	
					<td>Find your keys here: <a target="_blank" href="https://dashboard.stripe.com/account/apikeys">https://dashboard.stripe.com/account/apikeys</a></td>			
				</tr>
				<tr>
					<th>Live Secret Key:</th>
					<td>
						<input type="text" id="wpts_live_secret" name="wpts_live_secret" value="<?php echo get_option('wpts_live_secret'); ?>">
					</td>
					<td></td>				
				</tr>
				<tr>
					<th>Test Publish Key:</th>
					<td>
						<input type="text" id="wpts_test_public" name="wpts_test_public" value="<?php echo get_option('wpts_test_public'); ?>">
					</td>	
					<td></td>			
				</tr>
				<tr>
					<th>Test Secret Key:</th>
					<td>
						<input type="text" id="wpts_test_secret" name="wpts_test_secret" value="<?php echo get_option('wpts_test_secret'); ?>">
					</td>		
					<td></td>		
				</tr>
			</table>

			<h2>Additional Options</h2>
			<table class="wpts_table wp-list-table widefat">
				<!-- EXTRA OPTIONS -->
				<tr>
					<th>Check Zipcode?</th>
					<td>
						<input id="wpts_zipcode" name="wpts_zipcode" type="checkbox"
							<?php
								if($wpts_zipcode) { echo 'checked="checked"'; }
							?>>
					</td>			
					<td>Some extra validation to prevent fraud.</td>	
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

		<!-- PROMO -->
		<div class="promo">
			Stripe - Basic is a free plugin brought to you by the good folks at <a href="https://www.wptemplates.store">wptemplates.store</a>. v. <?php echo $wpts_version;?>
		</div>
	</div>

<?php }

?>