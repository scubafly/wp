<?php

$errors = array(
    'voornaam' => false,
    'organisatie' => false,
    'email' => empty($_POST['email'])? true: false,
    'achternaam' => false,
);


if (!$errors['voornaam'] && !$errors['organisatie'] && !$errors['email'] && !$errors['achternaam']) {
    $to = 'info@ivp.nl';
    $subject = 'Newsletter subscription via www.ivp.nl';
    
    $message = 'Voornaam: '.$_POST['voornaam']."\r\n";
    $message .= 'Achternaam: '.$_POST['achternaam']."\r\n";
    $message .= 'Organisatie: '.$_POST['organisatie']."\r\n";
    $message .= 'Email: '.$_POST['email']."\r\n";
    mail($to, $subject, $message);
    $errors = array();
} 
?>
<?php if (empty($errors)): ?>
<h2>Bedankt</h2>
<?php else: ?>
<form method="post" action="http://www.ivp.nl/nieuwsbrief">
	<p><input name="newsletter_subscribe" type="hidden" value="true" /></p>
	
	<fieldset class="first">
	<legend>Schrijf u nu in voor de nieuwsbrief</legend>
		<ul class="clearfix">
			<li>
				<ul class="clearfix<?php echo $errors['voornaam']? ' error':''; ?>">
					<li class="label">

						<label for="voornaam">Uw voornaam:<span class="required"></span></label>
					</li>
					<li><input type="text" value="" name="voornaam" id="voornaam" /></li>
				</ul>
			</li>
			<li>
				<ul class="clearfix<?php echo $errors['achternaam']? ' error':''; ?>">
					<li class="label">

						<label for="achternaam">Uw achternaam:<span class="required"></span></label>
					</li>
					<li><input type="text" value="" name="achternaam" id="achternaam" /></li>
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
				<ul class="clearfix<?php echo $errors['organisatie']? ' error':''; ?>">
					<li class="label">

						<label for="organisatie">Uw organisatie:<span class="required"></span></label>
					</li>
					<li><input type="text" value="" name="organisatie" id="organisatie" /></li>
				</ul>
			</li>
			

			<li class="last-submit">
				<div class="button-wrapper">
					<button type="submit" name="submit" id="submit" class="submit" value=""><span class="corner"></span></button>

				</div>
			</li>
		</ul>

	</fieldset>		
</form>

<?php endif; ?>
