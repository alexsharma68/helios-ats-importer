<div class="wrap">
<?php
if (!empty($_POST['submit']) && wp_verify_nonce ( $_POST ['ps_nonce'], plugin_basename ( __FILE__ ) )) {
		update_option('helios_feed_url',$_POST['helios_feed_url']);
        update_option('helios_url',$_POST['helios_url']);
?>
	<div class="updated settings-error" id="setting-error-settings_updated"> 
		<p><strong>Settings saved.</strong></p>
    </div>
<?php } ?>
    <?php echo "<h2>" . __('Helios Configuration.', 'oscimp_trdom') . "</h2>"; ?>
    <table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="Sync">Manual Sync</label></th>
				<td><button name="Sync" class="button action" id="sync_job">Click Sync</button><span class="helios-status"></span></td>
			</tr>
		</tbody>
    </table>
    <form action="" method="post">
        <table class="form-table">
            <tbody>
            	<tr>
                    <th scope="row"><label for="helios_feed_url">Helios Feed Address (URL)</label></th>
                    <td><input type="text" class="regular-text" value="<?php echo get_option('helios_feed_url');?>" id="helios_feed_url" name="helios_feed_url"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="helios_url">Jobs List Page ID </label></th>
                    <td><input type="text" class="regular-text" value="<?php echo get_option('helios_url');?>" id="helios_url" name="helios_url"></td>
                </tr>
               
            </tbody>
        </table>
		
        <p class="submit">
        	<input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit">
        </p>
        <?php wp_nonce_field ( plugin_basename ( __FILE__ ), 'ps_nonce' ); ?>
    </form>
</div>
<script>
    jQuery(function($) {
        $('#sync_job').click(function() {
            $('.helios-status').html('Please wait...<img src="<?php echo HELIOS__PLUGIN_URL.'/images/small_loader.gif';?>">');
            $.ajax({
                url: '<?php echo HELIOS__PLUGIN_URL . 'helios-ajax.php'; ?>',
                type: 'POST',
                dataType: 'json',
                success: function(result) {
                    $('.helios-status').text(result[0].msg);
                }
            });
            return false;
        });
    });
</script>
