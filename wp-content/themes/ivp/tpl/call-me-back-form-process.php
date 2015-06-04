<?php
    $ipRestriction       = get_option('ivp_ip_restriction');
    $weekdayEmailAddress = get_option('ivp_email_address_weekdays');
    $weekendEmailAddress = get_option('ivp_email_address_weekend');

    // ipv6 address
    $_SERVER['REMOTE_ADDR'] = ($_SERVER['REMOTE_ADDR'] == '::1')? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])
        && $_POST['action'] == 'call-me-back-form')
    {

        if (empty($_POST['name']) || empty($_POST['date']) || empty($_POST['telefon'])) {
            $result = array(
                'success' => false,
                'message' => 'Please fill in all the required fields'
            );
            echo json_encode($result);
            exit;
        } else {
            $weekday = date('w');
            $to = ($weekday == 6 || $weekday == 0) ? $weekendEmailAddress: $weekdayEmailAddress;
            $subject = 'Bel me terug bericht via www.ivp.nl';

            $message = 'Naam: '.$_POST['name']."\r\n";
            $message .= 'Organisatie: '.$_POST['organisation']."\r\n";
            $message .= 'Datum and tijd: '.$_POST['date'].' '.$_POST['time']."\r\n";
            $message .= 'Telefoon: '.$_POST['telefon']."\r\n";
            $message .= 'Bericht: '.$_POST['message']."\r\n";
            mail($to, $subject, $message);
            $result = array(
                'success' => true,
                'message' => sprintf('Message was sent to email address %s', $to),
            );
            echo json_encode($result);
            exit;
        }
    }
?>
