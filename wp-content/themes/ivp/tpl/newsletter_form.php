<?php
if ('POST' == $_SERVER['REQUEST_METHOD']){
  require_once('newsletter_form_process.php');
} else {
  require_once('newsletter_form_new.php');
}
?>