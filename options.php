<?php defined( 'ABSPATH' ) or die; ?>
<style type="text/css">
	.sitewatcher-switch {
		position: relative;
		display: inline-block;
		width: 60px;
		height: 34px;
		zoom: 0.7;
	}
	.sitewatcher-switch input {
		opacity: 0;
		width: 0;
		height: 0;
	}
	.sitewatcher-slider {
		position: absolute;
		cursor: pointer;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #aa0000;
		-webkit-transition: .4s;
		transition: .4s;
	}
	.sitewatcher-slider:before {
		position: absolute;
		content: "";
		height: 26px;
		width: 26px;
		left: 4px;
		bottom: 4px;
		background-color: white;
		-webkit-transition: .4s;
		transition: .4s;
	}
	input:checked + .sitewatcher-slider {
		background-color: #7ad03a;
	}

	input:focus + .sitewatcher-slider {
		box-shadow: 0 0 1px #7ad03a;
	}

	input:checked + .sitewatcher-slider:before {
		-webkit-transform: translateX(26px);
		-ms-transform: translateX(26px);
		transform: translateX(26px);
	}
	.sitewatcher-logo {
		max-height: 60px;
	}
	.sitewatcher-slider.sitewatcher-round {
		border-radius: 34px;
	}
	.sitewatcher-slider.sitewatcher-round:before {
		border-radius: 50%;
	}
</style>
<div class="wrap">
<?php settings_errors(); ?>
<img class="sitewatcher-logo" src="<?php echo esc_attr( SITEWATCHER_LOGO_URL ); ?>">
	<h1><?php esc_html_e( 'SiteWatcher', 'sitewatcher' ); ?></h1>
	<h2><?php esc_html_e( 'Options', 'sitewatcher' ); ?></h2>
	<?php // translators: %1$ breakline tag, %2$ opening link tag, %3$ closing link tag ?>
	<p><?php echo sprintf( esc_html__( 'Enable SiteWatcher to automatically scan your website after each update.%1$sIf you don&#39;t have a SiteWatcher account yet, click %2$shere%3$s to create a free account now.', 'sitewatcher' ), '<br>', '<a target="blank" href="https://sitewatcher.ai/app/register">', '</a>' ); ?></p> 
	<form method="POST" action="options.php">
		<?php settings_fields( SITEWATCHER_OPTSGROUP_NAME ); ?>
		<?php do_settings_sections( SITEWATCHER_OPTSGROUP_NAME ); ?>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Enable SiteWatcher', 'sitewatcher' ); ?></th>
				<td>
					<label class="sitewatcher-switch">
						<input type="checkbox" name="<?php echo esc_attr( SITEWATCHER_OPTIONS_NAME . '[enable_pi]' ); ?>" value="1" <?php echo esc_attr( $this->get_option( 'enable_pi' ) == '1' ? 'checked' : '' ); ?> />
						<span class="sitewatcher-slider sitewatcher-round"></span>
					</label>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Disable WordPress emails about updates. You can configure notifications in your SiteWatcher account.', 'sitewatcher' ); ?></th>
				<td>
					<label class="sitewatcher-switch">
						<input type="checkbox" name="<?php echo esc_attr( SITEWATCHER_OPTIONS_NAME . '[disable_en]' ); ?>" value="1" <?php echo esc_attr( $this->get_option( 'disable_en' ) == '1' ? 'checked' : '' ); ?> />
						<span class="sitewatcher-slider sitewatcher-round"></span>
					</label>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Endpoint URL.', 'sitewatcher' ); ?></th>
				<td>
					<strong><?php echo esc_html( get_rest_url( null, 'sitewatcher/v1/info' ) ); ?></strong>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>