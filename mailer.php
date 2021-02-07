<?php
    // My modifications to mailer script from:
    // http://blog.teamtreehouse.com/create-ajax-contact-form
    // Added input sanitizing to prevent injection

    // Only process POST requests.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($_POST["name"]));
        $name = str_replace(array("\r","\n"),array(" "," "),$name);
        $phone = strip_tags(trim($_POST["phone"]));
        $acs = strip_tags(trim($_POST["acs"]));
        $items = strip_tags(trim($_POST["items"]));
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        // $cont_subject = trim($_POST["subject"]);
        $message = trim($_POST["message"]);

        // Check that data was sent to the mailer.
        if ( empty($name)  OR empty($phone) OR empty($acs) OR ( !empty($email) AND !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Υπάρχει κάποιο λάθος στη φόρμα. Παρακαλώ συμπληρώστε σωστά τα απαιτούμενα στοιχεία.";
            exit;
        }

        // Log the entry to a file
        $list="/home/ei233/mail-list";
        $number = count(file($list)); // Get number of previous entries
        $curr_number = $number+1;

        $listfile = fopen($list, "a");// or die("Unable to open file ".$list);
        $new_entry = $curr_number ." ". date("d/m/Y") ." ". date("H:i:s") ." ". $name ." ". $phone ." ". $acs ." ". $items ." ". $email ." ". $message."\n";
        fwrite($listfile,$new_entry);
        fclose($listfile);           

        // Set the recipient email address.
        $recipient = "Lefteris <sendtolefteris@gmail.com>";//,Michalis<mixalisef@gmail.com";

        // Set the email subject.
        $subject = "Νέα βιβλιοπαραγγελία από $name";

        // Build the email content.
        $email_content = "Όνομα: $name\n";
        $email_content .= "Τηλέφωνο: $phone\n";
        $email_content .= "Σημείο ACS : $acs\n";
        $email_content .= "Τεμάχια: $items\n";
        if (!empty($email)) $email_content .= "Email: $email\n\n";
        if (!empty($message)) $email_content .= "Message:\n$message\n";

        // Build the email headers.
        $email_headers = "From: Biology book website<orders@biologybookcy.com>";

        // Send the email.
        if (mail($recipient, '=?UTF-8?B?'.base64_encode($subject).'?=', $email_content, $email_headers)) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Η παραγγελία σας έχει καταχωρηθεί επιτυχώς. Ευχαριστούμε!";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Δυστυχώς η παραγγελία σας δεν έχει καταχωρηθεί (Error 500). Παρακαλώ δοκιμάστε αργότερα.";
        }

        // Send sms
        $keyfile = fopen("/home/ei233/key", "r") or die("Unable to open file with sms key");
        $key=fgets($keyfile);
        fclose($keyfile);

        $url = "https://www.cyta.com.cy/cytamobilevodafone/dev/websmsapi/sendsms.aspx";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'HTTP/1.1');

        $headers = array("Host: www.cyta.com.cy", "Content-Type: application/xml; charset='utf-8'", "Connection: close",);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $data = "<?xml version='1.0' encoding='UTF-8' ?>  <websmsapi> <version>1.0</version> <username>explor3r</username> <secretkey>".$key."</secretkey> <recipients> <count>1</count> <mobiles><m>".$phone."</m> </mobiles> </recipients> <message>Η ΠΑΡΑΓΓΕΛΙΑ ΣΑΣ ΕΧΕΙ ΚΑΤΑΧΩΡΗΘΕΙ. ΘΑ ΕΙΔΟΠΟΙΗΘΕΙΤΕ ΑΠΟ ΤΟ ACS " . $acs." ΓΙΑ ΠΑΡΑΛΑΒΗ. ΣΑΣ ΕΥΧΑΡΙΣΤΟΥΜΕ!</message><language>el</language> </websmsapi>";

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);


    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "Δυστυχώς η παραγγελία σας δεν έχει καταχωρηθεί (Error 403). Παρακαλώ δοκιμάστε αργότερα.";
    }

?>
