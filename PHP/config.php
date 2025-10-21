<?php
// This is the configuration file for the database connection
$server_name = "localhost";
$database_id = "your db id here";
$database_password = "your db password here";
$database_name = "your db name here";

// Configuration for the email used in the register and password reset process
$mail_domain = "your mail domain here";
$mail_user = "your mail user here";
$mail_password = "your mail password here";

$folder = "/Goralys"; // Just use for developpement,should be "" for production

function connect_to_database()
{
    global $server_name, $database_id, $database_password, $database_name;
    $conn = new mysqli($server_name, $database_id, $database_password, $database_name);

    if ($conn->connect_error) {
        http_response_code(500); // Internal Server Error
        exit("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Utility function to easily show toast from php
function show_toast(string $toast_type, string $toast_title, string $toast_message, string $to_page = "index.html"): void
{
    global $folder;
    echo "<script type='text/javascript'>
        window.location.href = window.location.origin + '$folder/$to_page?toast=" . urlencode('true') . "&toast-type=" . urlencode($toast_type) . "&toast-title=" . urlencode($toast_title) . "&toast-message=" . urlencode($toast_message) . "';
    </script>";
}
