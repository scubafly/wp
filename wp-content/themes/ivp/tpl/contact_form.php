<?php
if ('POST' == $_SERVER['REQUEST_METHOD']){
  require_once('contact_form_process.php');
} else {
  require_once('contact_form_new.php');
}
?>