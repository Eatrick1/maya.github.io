<?php
/**
 * Maya Farm — Visit Booking & Contact Form Handler
 * Sends email to farm + confirmation to visitor
 * No PHPMailer — uses PHP mail() function
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// ── CONFIG ──────────────────────────────────────────────
$farm_email    = 'ssendegeyapatrick93@gmail.com';
$farm_name     = 'Maya Farm';
$form_type     = isset($_POST['form_type']) ? $_POST['form_type'] : 'contact'; // 'visit' or 'contact'
// ────────────────────────────────────────────────────────

// ── SANITIZE INPUTS ──────────────────────────────────────
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)));
}

if ($form_type === 'visit') {
    // Farm Visit Booking fields
    $fname    = clean($_POST['vfname']    ?? '');
    $lname    = clean($_POST['vlname']    ?? '');
    $email    = filter_var(trim($_POST['vemail'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone    = clean($_POST['vphone']   ?? '');
    $type     = clean($_POST['vtype']    ?? '');
    $date     = clean($_POST['vdate']    ?? '');
    $guests   = clean($_POST['vguests']  ?? '');
    $notes    = clean($_POST['vnotes']   ?? 'None');

    // Validate required fields
    if (!$fname || !$lname || !$email || !$phone || !$type || !$date || !$guests) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    $full_name = "$fname $lname";

    // ── EMAIL TO FARM ────────────────────────────────────
    $to_farm    = $farm_email;
    $subject_farm = "New Farm Visit Booking — $full_name | $date";
    $body_farm  = "
New Farm Visit Request
======================

Name:         $full_name
Email:        $email
Phone/WA:     $phone
Visit Type:   $type
Date:         $date
No. of Guests: $guests
Notes:        $notes

---
Submitted via Maya Farm website contact form.
";

    $headers_farm  = "From: Maya Farm Website <noreply@mayafarm.ug>\r\n";
    $headers_farm .= "Reply-To: $email\r\n";
    $headers_farm .= "X-Mailer: PHP/" . phpversion();

    $sent_farm = mail($to_farm, $subject_farm, $body_farm, $headers_farm);

    // ── CONFIRMATION EMAIL TO VISITOR ────────────────────
    $to_visitor    = $email;
    $subject_visitor = "Your Farm Visit Request — Maya Farm";
    $body_visitor  = "
Dear $fname,

Thank you for requesting a farm visit at Maya Farm!

We have received your booking request and our team will contact you within 24 hours to confirm the details.

Your Booking Summary
====================
Name:         $full_name
Visit Type:   $type
Preferred Date: $date
Number of Guests: $guests
Notes:        $notes

What to Expect
==============
- Our team will confirm your visit date and time via email or WhatsApp
- Visits run Monday to Saturday, 8:00 AM - 4:00 PM
- Location: Mpigi District, Uganda (~45 minutes from Kampala)
- For quick confirmation, WhatsApp us: +1 (248) 533-6685

We look forward to welcoming you to Maya Farm!

Warm regards,
The Maya Farm Team
Mpigi District, Uganda
+1 (248) 533-6685
info@mayafarm.ug
";

    $headers_visitor  = "From: Maya Farm <$farm_email>\r\n";
    $headers_visitor .= "Reply-To: $farm_email\r\n";
    $headers_visitor .= "X-Mailer: PHP/" . phpversion();

    mail($to_visitor, $subject_visitor, $body_visitor, $headers_visitor);

    if ($sent_farm) {
        echo json_encode(['success' => true, 'message' => 'Your visit request has been received! Check your email for confirmation. We will contact you within 24 hours.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sorry, there was an error sending your request. Please WhatsApp us directly on +1 (248) 533-6685.']);
    }

} else {
    // General Contact / Investor Inquiry fields
    $fname    = clean($_POST['fname']        ?? '');
    $lname    = clean($_POST['lname']        ?? '');
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone    = clean($_POST['phone']        ?? '');
    $interest = clean($_POST['interest']     ?? '');
    $org      = clean($_POST['organization'] ?? 'Not provided');
    $message  = clean($_POST['message']      ?? '');

    if (!$fname || !$lname || !$email || !$message) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    $full_name = "$fname $lname";

    // ── EMAIL TO FARM ────────────────────────────────────
    $to_farm      = $farm_email;
    $subject_farm = "New Inquiry from $full_name — Maya Farm Website";
    $body_farm    = "
New Contact / Investor Inquiry
===============================

Name:         $full_name
Email:        $email
Phone/WA:     $phone
Interest:     $interest
Organization: $org

Message:
$message

---
Submitted via Maya Farm website contact form.
";

    $headers_farm  = "From: Maya Farm Website <noreply@mayafarm.ug>\r\n";
    $headers_farm .= "Reply-To: $email\r\n";
    $headers_farm .= "X-Mailer: PHP/" . phpversion();

    $sent_farm = mail($to_farm, $subject_farm, $body_farm, $headers_farm);

    // ── CONFIRMATION EMAIL TO VISITOR ────────────────────
    $to_visitor      = $email;
    $subject_visitor = "Thank you for contacting Maya Farm";
    $body_visitor    = "
Dear $fname,

Thank you for reaching out to Maya Farm!

We have received your message and our team will respond within 24 hours.

Your Message Summary
====================
Name:     $full_name
Interest: $interest
Message:  $message

In the meantime, feel free to WhatsApp us directly for a faster response:
+1 (248) 533-6685

Warm regards,
The Maya Farm Team
Mpigi District, Uganda
+1 (248) 533-6685
info@mayafarm.ug
";

    $headers_visitor  = "From: Maya Farm <$farm_email>\r\n";
    $headers_visitor .= "Reply-To: $farm_email\r\n";
    $headers_visitor .= "X-Mailer: PHP/" . phpversion();

    mail($to_visitor, $subject_visitor, $body_visitor, $headers_visitor);

    if ($sent_farm) {
        echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been received. Check your email — we will respond within 24 hours.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sorry, there was a sending error. Please WhatsApp us directly on +1 (248) 533-6685.']);
    }
}
?>
