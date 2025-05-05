<?php
/**
 * Mailcoach connection setup view
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

/** @var $this Thrive_Dash_List_Connection_Mailcoach */
?>
<div class="tve-sp"></div>
<h2><?php echo esc_html__( 'Mailcoach Connection', 'thrive-dash' ); ?></h2>
<div class="tve-sp"></div>
<p><?php echo esc_html__( 'Enter your Mailcoach API Key and URL below to connect', 'thrive-dash' ); ?></p>
<div class="tve-sp"></div>
<div class="tvd-row">
	<div class="tvd-col tvd-s12">
		<div class="tvd-input-field">
			<input id="tvd-mm-api-key" type="text" name="connection[api_key]"
				value="<?php echo esc_attr( $this->param( 'api_key' ) ); ?>"/>
			<label for="tvd-mm-api-key"><?php echo esc_html__( 'API Key', 'thrive-dash' ); ?></label>
		</div>
	</div>
</div>
<div class="tvd-row">
	<div class="tvd-col tvd-s12">
		<div class="tvd-input-field">
			<input id="tvd-mm-api-url" type="text" name="connection[api_url]"
				value="<?php echo esc_attr( $this->param( 'api_url' ) ); ?>"/>
			<label for="tvd-mm-api-url"><?php echo esc_html__( 'API URL', 'thrive-dash' ); ?></label>
			<p><?php echo esc_html__( 'E.g., https://your-mailcoach-instance.com', 'thrive-dash' ); ?></p>
		</div>
	</div>
</div>
<div class="tve-sp"></div>
<div class="tvd-row">
	<div class="tvd-col tvd-s12">
		<p class="tve-mailcoach-help">
			<?php 
			// First part of the API token instructions
			echo esc_html__( 'To get your API Key, go to your Mailcoach account,', 'thrive-dash' ); 
			?> 
			<?php
			// Second part of the API token instructions
			echo esc_html__( 'navigate to "API tokens" in the settings', 'thrive-dash' ); 
			?>
			<br>
			<?php 
			// Third part of the API token instructions
			echo esc_html__( 'and create a new token with required permissions:', 'thrive-dash' ); 
			?>
			<br>
			- <?php echo esc_html__( 'view email lists', 'thrive-dash' ); ?>
			<br>
			- <?php echo esc_html__( 'manage subscribers', 'thrive-dash' ); ?>
		</p>
	</div>
</div>
<div class="tve-sp"></div>
<?php $this->display_video_link(); ?>
