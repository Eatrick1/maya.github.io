<?php
/**
 * Maya Farm — Visit Booking & Contact Form Handler
 * Uses PHP mail() — configured for Hostinger shared hosting
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// ── CONFIG ──────────────────────────────────────────────────────────────────
// IMPORTANT: The "From" address MUST match your Hostinger hosting domain
// Replace noreply@eatricksolutions.com with a real email on your hosting domain
$from_email    = 'noreply@eatricksolutions.com';   // ← must match your hosting domain
$farm_email    = 'ssendegeyapatrick93@gmail.com';  // ← where you receive emails
$farm_name     = 'Maya Farm';
$form_type     = isset($_POST['form_type']) ? $_POST['form_type'] : 'contact';
// ────────────────────────────────────────────────────────────────────────────

// ── SANITIZE ────────────────────────────────────────────────────────────────
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)));
}

// ── SEND HELPER ─────────────────────────────────────────────────────────────
function sendEmail($to, $subject, $body, $from_email, $reply_to) {
    $boundary = md5(time());
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: Maya Farm <{$from_email}>\r\n";
    $headers .= "Reply-To: {$reply_to}\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 1\r\n";

    // Use -f flag so Hostinger accepts the From address
    return mail($to, $subject, $body, $headers, "-f{$from_email}");
}


// ════════════════════════════════════════════════════════════════════════════
// VISIT BOOKING FORM
// ════════════════════════════════════════════════════════════════════════════
if ($form_type === 'visit') {

    $fname  = clean($_POST['vfname']  ?? '');
    $lname  = clean($_POST['vlname']  ?? '');
    $email  = filter_var(trim($_POST['vemail'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone  = clean($_POST['vphone']  ?? '');
    $type   = clean($_POST['vtype']   ?? '');
    $date   = clean($_POST['vdate']   ?? '');
    $guests = clean($_POST['vguests'] ?? '');
    $notes  = clean($_POST['vnotes']  ?? 'None');

    if (!$fname || !$lname || !$email || !$phone || !$type || !$date || !$guests) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    $full_name = "$fname $lname";

    // Email to farm owner
    $subject_farm = "New Farm Visit Booking — $full_name | $date";
    $body_farm = "New Farm Visit Request
======================

Name:           $full_name
Email:          $email
Phone/WA:       $phone
Visit Type:     $type
Date:           $date
No. of Guests:  $guests
Notes:          $notes

---
Submitted via Maya Farm website.
https://eatricksolutions.com/maya/";

    $sent = sendEmail($farm_email, $subject_farm, $body_farm, $from_email, $email);

    // Confirmation to visitor
    $subject_visitor = "Your Farm Visit Request — Maya Farm";
    $body_visitor = "Dear $fname,

Thank you for requesting a farm visit at Maya Farm!

We have received your booking request and our team will contact you within 24 hours to confirm.

Your Booking Summary
====================
Name:             $full_name
Visit Type:       $type
Preferred Date:   $date
Number of Guests: $guests
Notes:            $notes

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
info@mayafarm.ug";

    sendEmail($email, $subject_visitor, $body_visitor, $from_email, $farm_email);

    if ($sent) {
        echo json_encode(['success' => true, 'message' => 'Your visit request has been received! Check your email for confirmation. We will contact you within 24 hours.']);
    } else {
        // Log error for debugging
        error_log("Maya Farm mail() failed for visit booking from: $email");
        echo json_encode(['success' => false, 'message' => 'Sorry, there was a sending error. Please WhatsApp us directly on +1 (248) 533-6685.']);
    }


// ════════════════════════════════════════════════════════════════════════════
// GENERAL CONTACT / INVESTOR INQUIRY
// ════════════════════════════════════════════════════════════════════════════
} else {

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

    // Email to farm owner
    $subject_farm = "New Inquiry from $full_name — Maya Farm Website";
    $body_farm = "New Contact / Investor Inquiry
===============================

Name:          $full_name
Email:         $email
Phone/WA:      $phone
Interest:      $interest
Organization:  $org

Message:
$message

---
Submitted via Maya Farm website.
https://eatricksolutions.com/maya/";

    $sent = sendEmail($farm_email, $subject_farm, $body_farm, $from_email, $email);

    // Confirmation to user
    $subject_visitor = "Thank you for contacting Maya Farm";
    $body_visitor = "Dear $fname,

Thank you for reaching out to Maya Farm!

We have received your message and our team will respond within 24 hours.

Your Message Summary
====================
Name:     $full_name
Interest: $interest
Message:  $message

In the meantime, feel free to WhatsApp us for a faster response:
+1 (248) 533-6685

Warm regards,
The Maya Farm Team
Mpigi District, Uganda
+1 (248) 533-6685
info@mayafarm.ug";

    sendEmail($email, $subject_visitor, $body_visitor, $from_email, $farm_email);

    if ($sent) {
        echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been received. We will respond within 24 hours.']);
    } else {
        error_log("Maya Farm mail() failed for contact form from: $email");
        echo json_encode(['success' => false, 'message' => 'Sorry, there was a sending error. Please WhatsApp us directly on +1 (248) 533-6685.']);
    }
}
?>
