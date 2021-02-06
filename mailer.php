<?php
    // My modifications to mailer script from:
    // http://blog.teamtreehouse.com/create-ajax-contact-form
    // Added input sanitizing to prevent injection

    // Only process POST reqeusts.
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

        // Set the recipient email address.
        $recipient = "sendtolefteris@gmail.com,mixalisef@gmail.com";

        // Set the email subject.
        $subject = "New book order from $name";

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
            echo "Ευχαριστούμε! Η παραγγελία σας έχει σταλεί.";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Δυστυχώς η παραγγελία σας δεν έχει σταλεί (Error 500). Παρακαλώ δοκιμάστε αργότερα.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "Δυστυχώς η παραγγελία σας δεν έχει σταλεί (Error 403). Παρακαλώ δοκιμάστε αργότερα.";
    }

?>
