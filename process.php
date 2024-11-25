<?php
header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $to = "patakigor2000@gmail.com"; // Replace with recipient email
    $subject = "New Order from Memories Shop";
    $from = "danieldanko78@gmail.com"; // Replace with your email

    // Gather form data
    $name = $_POST['ime'];
    $surname = $_POST['prezime'];
    $address = $_POST['adresa'];
    $city = $_POST['grad'];
    $postal = $_POST['postanski'];
    $phone = $_POST['broj'];
    $email = $_POST['email'];
    $kolicina = $_POST['kolicina'];
    $cena = $kolicina * 2999;

    // MIME boundary
    $separator = md5(time());
    
    // Headers
    $headers = "From: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$separator\"\r\n";
    
    // Message body
    $message = "--$separator\r\n";
    $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    
    $message .= "Nova porudzbina:\n";
    $message .= "Ime: $name $surname\n";
    $message .= "Adresa: $address, $city\n";
    $message .= "Postanski broj: $postal\n";
    $message .= "Broj telefona: $phone\n";
    $message .= "Email: $email\n";
    $message .= "Broj proizvoda: $kolicina\n";
    $message .= "Total Cena: $cena\n\n";

    // Process each uploaded file
    if (isset($_FILES['file']) && is_array($_FILES['file']['tmp_name'])) {
        foreach ($_FILES['file']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['file']['error'][$index] == UPLOAD_ERR_OK) {
                $file_tmp = $tmp_name;
                $file_name = $_FILES['file']['name'][$index];
                $file_type = $_FILES['file']['type'][$index];

                // Encode file content
                $file_content = chunk_split(base64_encode(file_get_contents($file_tmp)));

                // Add each file as a separate part
                $message .= "--$separator\r\n";
                $message .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n";
                $message .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
                $message .= $file_content . "\r\n\r\n";
            }
        }
    }

    // End MIME boundary
    $message .= "--$separator--";

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(["status" => "success", "message" => "Vaša narudžbina je uspešno poslata! Hvala što ste poručili kod nas."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Nažalost, narudžbina nije poslata zbog greške. Pokušajte ponovo ili nas kontaktirajte za pomoć."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Nažalost, nije moguće obraditi vašu narudžbinu. Molimo Vas da pokušate ponovo ili nas kontaktirajte."]);
}
?>
