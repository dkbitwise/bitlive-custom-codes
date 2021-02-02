<?php
/**
 * Admin student template
 */
?>
<div class="bitlive-custom-codes wrap" id="bitlive_custom_codes_form">
	<h1 class="bitlive-custom wp-heading-inline">Sync Students</h1>
    <div class="bitlive-sysnc-student-button">
        <a class="button button-primary" href="<?php echo esc_url(admin_url('?page=bitlive-sync-live&sync=yes'))?>"><?php esc_html_e('Sync Now','bitlive-custom-codes'); ?></a>
    </div>
</div>
