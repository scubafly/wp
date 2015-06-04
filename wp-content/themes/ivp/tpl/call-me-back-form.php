<?php
    $ipRestriction       = get_option('ivp_ip_restriction');
    $weekdayEmailAddress = get_option('ivp_email_address_weekdays');
    $weekendEmailAddress = get_option('ivp_email_address_weekend');

    // ipv6 address
    $_SERVER['REMOTE_ADDR'] = ($_SERVER['REMOTE_ADDR'] == '::1')? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
?>
<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' && (empty($ipRestriction) || $_SERVER['REMOTE_ADDR'] == $ipRestriction)): ?>
<a href="#TB_inline?height=450&width=400&inlineId=call-me-back&modal=true" class="thickbox" title="Bel mij terug">Bel mij terug</a>
<div id="call-me-back" style="display:none">
    <div id="TB_closeWindow"><a href="#" id="TB_closeWindowButton" title="Close">close</a></div>
    <h2>Bel mij terug</h2>
    <form method="post" action="<?php echo home_url( '/' ); ?>" class="call-me-back" id="call-me-back-form">
        <ul class="errors">
            <li><strong>Vul a.u.b. alle verplichte velden</strong></li>
        </ul>
        <ul class="success">
            <li><strong>Bedankt voor uw bericht</strong></li>
        </ul>
        <fieldset class="first">
            <ul class="clearfix">
                <li>
                    <ul class="clearfix">
                        <li class="label">
                            <label for="naam">Uw naam: <span class="required">*</span></label>
                        </li>
                        <li><input type="text" value="" name="name" id="naam" /></li>
                    </ul>
                </li>
		<li>
			<ul class="clearfix">
			<li class="label">
				<label for="organisatie">Organisatie: <span class="required">*</spn></label>
			</li>
			<li><input type="text" value="" name="organisation" id="organisatie" /></li>
			</ul>
		</li>
                <li>
                    <ul class="clearfix">
                        <li class="label">
                            <label for="organisatie">Datum en tijd: <span class="required">*</span></label>
                        </li>
                        <li><input type="text" value="" name="date" class="inline-input"/> <span>om</span> <input type="text" value="" name="time" class="inline-input-small" /> uur</li>
                    </ul>
                </li>
                <li>
                    <ul class="clearfix">
                        <li class="label">
                            <label>Telefoon: <span class="required">*</span></label>
                        </li>
                        <li><input type="text" value="" name="telefon" /></li>
                    </ul>
                </li>
                <li>
                    <ul class="clearfix">
                        <li class="label">
                            <label for="bericht">Bericht:</label>
                        </li>
                        <li><textarea name="message" rows="5" cols="40"></textarea></li>
                    </ul>
                </li>
                <li class="last-submit">
                    <div class="button-wrapper">
                        <input type="hidden" name="action" value="call-me-back-form" />
                        <button type="submit" name="submit-call-me-back" class="submit" value="call-me-back" id="submit-call-me-back-btn"></button>
                    </div>
                </li>
            </ul>
        </fieldset>
    </form>
</div><!-- call me back -->
<script type="text/javascript">
    jQuery('#submit-call-me-back-btn').bind('click', function(){
        jQuery.ajax({
            type: 'POST',
            url: jQuery("#call-me-back-form").attr('action'),
            data: jQuery("#call-me-back-form").serialize(),
            dataType: 'json',
            success: function(data){
                if (!data.success) {
                    jQuery("#call-me-back-form").find('ul.errors').css('display', 'block');
                    jQuery("#call-me-back-form").find('ul.success').css('display', 'none');
                } else {
                    jQuery("#call-me-back-form").find('ul.success').css('display', 'block');
                    jQuery("#call-me-back-form").find('ul.errors').css('display', 'none');
                    setTimeout(function(){tb_remove();}, 2000);
                }
                //console.log(data.success);
            }
        });
        return false;
    });

</script>
<?php endif; ?>
