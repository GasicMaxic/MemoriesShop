<?php
header('Content-Type: application/json'); // Ensure JSON response
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer's class files
require 'vendor/PHPMailer-6.9.3/src/Exception.php';
require 'vendor/PHPMailer-6.9.3/src/PHPMailer.php';
require 'vendor/PHPMailer-6.9.3/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = ["status" => "error", "message" => "An unknown error occurred."];

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception("Invalid request method.");
    }

    // Gather form data
    $requiredFields = ['ime', 'prezime', 'adresa', 'grad', 'postanski', 'broj'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field {$field} is required.");
        }
    }

    $name = $_POST['ime'];
    $surname = $_POST['prezime'];
    $address = $_POST['adresa'];
    $city = $_POST['grad'];
    $postal = $_POST['postanski'];
    $phone = $_POST['broj'];
    $kolicina = isset($_FILES['file']['name']) ? count($_FILES['file']['name']) : 0;
    $cena = $kolicina * 1980;
    $dostava = 450;

    // PHPMailer setup
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = ''; // Replace with your email
    $mail->Password = ''; // Replace with your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('danieldanko78@gmail.com', 'Memories Shop');
    $mail->addAddress('patakigor2000@gmail.com');

    // Email content
    $mail->isHTML(false);
    $mail->Subject = 'New Order from Memories Shop';
    $mail->Body    = "Nova porudzbina:\n" .
                     "Ime: $name $surname\n" .
                     "Adresa: $address, $city\n" .
                     "Postanski broj: $postal\n" .
                     "Broj telefona: $phone\n" .
                     "Broj proizvoda: $kolicina\n" .
                     "Total Cena: $cena rsd\n";

    // Attachments
    if (isset($_FILES['file']) && is_array($_FILES['file']['tmp_name'])) {
        foreach ($_FILES['file']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['file']['error'][$index] == UPLOAD_ERR_OK) {
                $file_tmp = $tmp_name;
                $file_name = $_FILES['file']['name'][$index];
                $mail->addAttachment($file_tmp, $file_name);
            }
        }
    }

    // Send email
    $mail->send();
    $response = ["status" => "success", "message" => "Va分a narud弔bina je uspe分no poslata!"];
} catch (Exception $e) {
    error_log($e->getMessage()); // Log error for debugging
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
