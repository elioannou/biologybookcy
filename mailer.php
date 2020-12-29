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
        $subject = "New order from $name $phone";

        // Build the email content.
        $email_content = "Name: $name\n";
        $email_content .= "Phone: $phone\n";
        $email_content .= "ACS Store: $acs\n";
        $email_content .= "Items: $items\n";
        $email_content .= "Email: $email\n\n";
        // $email_content .= "Subject: $cont_subject\n";
        $email_content .= "Message:\n$message\n";

        // Build the email headers.
        $email_headers = "From: Book order <order@book.com>";

        // Send the email.
        if (mail($recipient, $subject, $email_content, $email_headers)) {
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
