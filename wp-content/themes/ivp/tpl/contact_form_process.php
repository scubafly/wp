<?php

$errors = array(
    'naam' => empty($_POST['naam'])? true: false,
    'organisatie' => empty($_POST['organisatie'])? true: false,
    'email' => empty($_POST['email'])? true: false,
    'opmerking' => empty($_POST['organisatie'])? true: false,
);


if (!$errors['naam'] && !$errors['organisatie'] && !$errors['email'] && !$errors['opmerking']) {
    $to = 'info@ivp.nl';
    $subject = 'Reactie via www.ivp.nl';
    
    $message = 'Naam: '.$_POST['naam']."\r\n";
    $message .= 'Organisatie: '.$_POST['organisatie']."\r\n";
    $message .= 'Email: '.$_POST['email']."\r\n";
    $message .= 'Opmerking: '.$_POST['opmerking']."\r\n";
    mail($to, $subject, $message);
    $errors = array();
} 
?>
<?php if (empty($errors)): ?>
<h2>Bedankt voor uw bericht</h2>
<?php else: ?>
<form method="post" action="http://www.ivp.nl/contact">
	<p><input name="form_page_id" type="hidden" value="57" /></p>
	<fieldset class="first">
		<ul class="clearfix">
			<li>
				<ul class="clearfix<?php echo $errors['naam']? ' error':''; ?>">
					<li class="label">
						<label for="naam">Uw naam:<span class="required">*</span></label>
					</li>
					<li><input type="text" value="" name="naam" id="naam" /></li>
				</ul>
			</li>

			<li>
				<ul class="clearfix<?php echo $errors['organisatie']? ' error':''; ?>">
					<li class="label">
						<label for="organisatie">Uw organisatie:<span class="required">*</span></label>
					</li>
					<li><input type="text" value="" name="organisatie" id="organisatie" /></li>
				</ul>
			</li>							
			<li>

				<ul class="clearfix<?php echo $errors['email']? ' error':''; ?>">
					<li class="label">
						<label for="email">Uw email adres:<span class="required">*</span></label>
					</li>
					<li><input type="text" value="" name="email" id="email" /></li>
				</ul>
			</li>
																										
			<li>

				<ul class="clearfix<?php echo $errors['opmerking']? ' error':''; ?>">
					<li class="label">
						<label for="opmerking">Uw vraag:<span class="required">*</span></label>
					</li>
					<li><textarea name="opmerking" id="opmerking" rows="5" cols="40"></textarea></li>
				</ul>
			</li>																			
			<li class="last-submit">

				<div class="button-wrapper">
					<button type="submit" name="submit" id="submit" class="submit" value=""></button>
				</div>
			</li>							
		</ul>
	</fieldset>							
</form>		


<?php endif; ?>