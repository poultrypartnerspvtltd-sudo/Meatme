<?php
/**
 * Email Helper Functions for MeatMe
 * Centralized email sending functionality
 */

// Load PHPMailer via Composer autoload
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('sendMail')) {
    /**
     * Send email using PHPMailer with Gmail SMTP
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string $altBody Plain text alternative (optional)
     * @param array $attachments File paths to attach (optional)
     * @return bool Success status
     * @throws Exception On mailer error
     */
    function sendMail($to, $subject, $body, $altBody = '', $attachments = [])
    {
        // Load email configuration
        $emailConfig = require __DIR__ . '/../../config/email.php';

        $mail = new PHPMailer(true);

        try {
            // Enable debug mode if configured
            if ($emailConfig['debug_mode']) {
                $mail->SMTPDebug = PHPMailer::DEBUG_SERVER;
                $mail->Debugoutput = function($str, $level) {
                    error_log("PHPMailer Debug: $str");
                };
            }

            // Server settings
            $mail->isSMTP();
            $mail->Host       = $emailConfig['smtp']['host'];
            $mail->SMTPAuth   = $emailConfig['smtp']['auth'];
            $mail->Username   = $emailConfig['smtp']['username'];
            $mail->Password   = $emailConfig['smtp']['password'];
            $mail->SMTPSecure = $emailConfig['smtp']['encryption'] === 'tls' ?
                               PHPMailer::ENCRYPTION_STARTTLS :
                               PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $emailConfig['smtp']['port'];

            // Timeout settings
            $mail->Timeout = 30;
            $mail->SMTPKeepAlive = false;

            // Recipients
            $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
            $mail->addAddress($to);

            // Add BCC recipients if configured
            if (!empty($emailConfig['bcc_emails'])) {
                foreach ($emailConfig['bcc_emails'] as $bccEmail => $bccName) {
                    $mail->addBCC($bccEmail, $bccName);
                }
            }

            // Attachments
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $mail->addAttachment($attachment);
                    }
                }
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            if (!empty($altBody)) {
                $mail->AltBody = $altBody;
            } else {
                // Generate basic plain text version if not provided
                $mail->AltBody = strip_tags($body);
            }

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            throw new Exception("Failed to send email. Please try again later.");
        }
    }
}

if (!function_exists('sendContactEmail')) {
    /**
     * Send contact form email with formatted template
     *
     * @param string $name Sender name
     * @param string $email Sender email
     * @param string $subject Message subject
     * @param string $message Message content
     * @return bool Success status
     * @throws Exception On mailer error
     */
    function sendContactEmail($name, $email, $subject, $message)
    {
        // Load email configuration
        $emailConfig = require __DIR__ . '/../../config/email.php';

        $mail = new PHPMailer(true);

        try {
            // Enable debug mode if configured
            if ($emailConfig['debug_mode']) {
                $mail->SMTPDebug = PHPMailer::DEBUG_SERVER;
                $mail->Debugoutput = function($str, $level) {
                    error_log("PHPMailer Debug: $str");
                };
            }

            // Server settings
            $mail->isSMTP();
            $mail->Host       = $emailConfig['smtp']['host'];
            $mail->SMTPAuth   = $emailConfig['smtp']['auth'];
            $mail->Username   = $emailConfig['smtp']['username'];
            $mail->Password   = $emailConfig['smtp']['password'];
            $mail->SMTPSecure = $emailConfig['smtp']['encryption'] === 'tls' ?
                               PHPMailer::ENCRYPTION_STARTTLS :
                               PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $emailConfig['smtp']['port'];

            // Timeout settings
            $mail->Timeout = 30;
            $mail->SMTPKeepAlive = false;

            // Recipients
            $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
            $mail->addAddress($emailConfig['admin_email'], $emailConfig['admin_name']);

            // Add BCC recipients if configured
            if (!empty($emailConfig['bcc_emails'])) {
                foreach ($emailConfig['bcc_emails'] as $bccEmail => $bccName) {
                    $mail->addBCC($bccEmail, $bccName);
                }
            }

            // Reply-To
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $emailConfig['contact_subject_prefix'] . ' ' . $name;

            // Email body
            $emailBody = "
            <html>
            <head>
                <title>New Contact Form Message</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2e7d32; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
                    .field { margin: 10px 0; }
                    .field strong { display: inline-block; width: 100px; }
                    .message-box { background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #4caf50; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>🐔 New Contact Message - MeatMe</h2>
                    </div>
                    <div class='content'>
                        <div class='field'><strong>Name:</strong> " . htmlspecialchars($name) . "</div>
                        <div class='field'><strong>Email:</strong> <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></div>
                        <div class='field'><strong>Subject:</strong> " . htmlspecialchars($subject ?: 'General Inquiry') . "</div>
                        <div class='field'><strong>Date:</strong> " . date('F j, Y g:i A') . "</div>
                        <div class='field'><strong>Message:</strong></div>
                        <div class='message-box'>" . nl2br(htmlspecialchars($message)) . "</div>
                        <hr>
                        <p style='font-size: 12px; color: #666; margin: 0;'>
                            This message was sent from the MeatMe website contact form.<br>
                            Sita Poultry Farm, Butwal, Bellbariya, Nepal
                        </p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $mail->Body    = $emailBody;
            $mail->AltBody = "New Contact Form Message:\n\nName: $name\nEmail: $email\nSubject: " . ($subject ?: 'General Inquiry') . "\nMessage:\n$message";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            throw new Exception("Failed to send email. Please try again later.");
        }
    }
}
?>
