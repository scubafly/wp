<div class="wrap">
<h2>IVP Options</h2>

<form method="post" action="options.php">
    <?php settings_fields('ivp_settings'); ?>
    <h3 class="title">Call Me Back Settings</h3>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Email Address Weekdays</th>
        <td>
            <input type="text" name="ivp_email_address_weekdays" value="<?php echo get_option('ivp_email_address_weekdays'); ?>" /><br />
            <span class="description">Monday-Friday.</span>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Email Address - Weekend</th>
        <td>
            <input type="text" name="ivp_email_address_weekend" value="<?php echo get_option('ivp_email_address_weekend'); ?>" /><br />
            <span class="description">Saturday-Sunday</span>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Ip Address Restriction</th>
        <td>
            <input type="text" name="ivp_ip_restriction" value="<?php echo get_option('ivp_ip_restriction'); ?>" /><br />
            <span class="description">Form is visible only for ip address; leave empty if it's visible to all users</span>
        </td>
        </tr>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
