<?php
namespace App\Controllers;

use App\Core\Controller;

class ContactController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Contact Us - MeatMe',
            'meta_description' => 'Get in touch with MeatMe at Sita Poultry Farm. Contact us for fresh chicken delivery, orders, and customer support in Butwal, Bellbariya, Nepal.',
            'contact_info' => [
                'phone' => '+977 9821908585',
                'email' => 'meatme9898@gmail.com',
                'address' => 'Sita Poultry Farm, Butwal, Bellbariya',
                'business_hours' => [
                    'monday_friday' => '9:00 AM - 8:00 PM',
                    'saturday' => '9:00 AM - 6:00 PM',
                    'sunday' => '10:00 AM - 5:00 PM'
                ]
            ]
        ];
        
        $this->render('contact.index', $data);
    }
    
    public function faq()
    {
        $data = [
            'title' => 'Frequently Asked Questions - MeatMe',
            'faqs' => [
                [
                    'question' => 'What are your delivery hours?',
                    'answer' => 'We deliver fresh chicken from 9:00 AM to 8:00 PM on weekdays, 9:00 AM to 6:00 PM on Saturday, and 10:00 AM to 5:00 PM on Sunday.'
                ],
                [
                    'question' => 'How fresh is your chicken?',
                    'answer' => 'Our chicken is sourced daily from local farms and delivered within 24 hours of processing to ensure maximum freshness.'
                ],
                [
                    'question' => 'Do you deliver to my area?',
                    'answer' => 'We currently deliver within Butwal and surrounding areas. Contact us to check if we deliver to your specific location.'
                ],
                [
                    'question' => 'What payment methods do you accept?',
                    'answer' => 'We accept cash on delivery, eSewa, Khalti, and bank transfers for your convenience.'
                ],
                [
                    'question' => 'Can I cancel my order?',
                    'answer' => 'Orders can be cancelled within 30 minutes of placement. Please call us immediately if you need to cancel.'
                ]
            ]
        ];
        
        $this->render('contact.faq', $data);
    }
    
    public function submit()
    {
        // Get form data
        $name = $this->input('name', '');
        $email = $this->input('email', '');
        $phone = $this->input('phone', '');
        $subject = $this->input('subject', 'General Inquiry');
        $message = $this->input('message', '');
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($message)) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'Please fill in all required fields.']);
            } else {
                \App\Core\Session::flash('error', 'Please fill in all required fields.');
                \App\Core\Helpers::redirect('contact');
            }
            return;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'Please provide a valid email address.']);
            } else {
                \App\Core\Session::flash('error', 'Please provide a valid email address.');
                \App\Core\Helpers::redirect('contact');
            }
            return;
        }
        
        // Save contact message to file (simple approach for normal PHP project)
        $contactFile = __DIR__ . '/../../storage/contact_messages.txt';
        $contactDir = dirname($contactFile);
        
        if (!is_dir($contactDir)) {
            mkdir($contactDir, 0755, true);
        }
        
        $contactData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'date' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        $logEntry = date('Y-m-d H:i:s') . " | {$name} ({$email}) | {$subject}\n";
        $logEntry .= "Message: {$message}\n";
        $logEntry .= "Phone: {$phone}\n";
        $logEntry .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";
        $logEntry .= str_repeat('-', 80) . "\n\n";
        
        file_put_contents($contactFile, $logEntry, FILE_APPEND);
        
        // Send email notification (if email helper exists)
        try {
            if (file_exists(__DIR__ . '/../helpers/email.php')) {
                require_once __DIR__ . '/../helpers/email.php';
                $adminEmail = 'meatme9898@gmail.com';
                $emailSubject = "New Contact Form Submission: {$subject}";
                $emailBody = "Name: {$name}\n";
                $emailBody .= "Email: {$email}\n";
                $emailBody .= "Phone: {$phone}\n";
                $emailBody .= "Subject: {$subject}\n\n";
                $emailBody .= "Message:\n{$message}";
                
                // Try to send email (non-blocking)
                @send_email($adminEmail, $emailSubject, $emailBody);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the request
            error_log("Contact form email error: " . $e->getMessage());
        }
        
        // Return success response
        if ($this->isAjaxRequest()) {
            $this->json([
                'success' => true,
                'message' => 'Thank you for contacting us! We will get back to you soon.'
            ]);
        } else {
            \App\Core\Session::flash('success', 'Thank you for contacting us! We will get back to you soon.');
            \App\Core\Helpers::redirect('contact');
        }
    }
}
?>
