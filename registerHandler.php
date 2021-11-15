<p><a href="register.php">Return to registration.</a></p>
<p><a href="index.html">Return to main menu.</a></p>

<?php
/* ---User Authentification---
 * Username and password <= 50 characters
 * Unlimited attempts allowed
 * Certain characters are not allowed: ' " / \ [ ] ( ) { }
 * Password is hidden when typed
 * Username is case-insensitive
 * Password is case-sensitive !--This is done within phpMyAdmin by settings the PASSWORD column collation to latin1_general_cs--!
 * Password must be bigger than 5 characters
 * Username must be bigger than 5 characters
 */
require_once('myfuncs.php');


$link = dbConnect();

// Input
$firstname = $_POST['FirstName'];
$lastname = $_POST['LastName'];
$email = $_POST['Email'];
$email2 = $_POST['ReenterEmail'];
$username = $_POST['Username'];
$password = $_POST['Password'];

// Check for empty input, otherwise exit out with an error
if ($firstname == NULL) { $message = "The First Name or is a required field and cannot be blank.\n"; gotoResultPage($message); }
if ($lastname == NULL)  { $message = "The Last Name is a required field and cannot be blank.\n"; gotoResultPage($message); }
if ($email == NULL)     { $message = "The email is a required field and cannot be blank.\n"; gotoResultPage($message); }
if ($email2 == NULL)    { $message = "The email re-entry is a required field and cannot be blank.\n"; gotoResultPage($message); }
if ($username == NULL)  { $message = "The username is a required field and cannot be blank.\n"; gotoResultPage($message); }
if ($password == NULL)  { $message = "The password is a required field and cannot be blank.\n"; gotoResultPage($message); }

// Check length
if (strlen($username) <= 5) {
    gotoResultPage("Username must have at least 6 characters.");
}

if (strlen($password) <= 5) {
    gotoResultPage("Password must have at least 6 characters");
}

// Check for illegal characters
$illegal = array("'", "\"", "/", "\\", "[", "]", "{", "}", "(", ")");
foreach ($illegal as $i) {
    if (strpos($username, $i) !== false) {
        gotoResultPage("ERROR: The username has one of the illegal characters: ' \" / \ [ ] ( ) { }\n");
    }
}

foreach ($illegal as $i) {
    if (strpos($password, $i) !== false) {
        gotoResultPage("ERROR: The password has one of the illegal characters: ' \" / \ [ ] ( ) { }\n");
    }
}

// Check for duplicate users
$sql = "SELECT * FROM users";
$result = mysqli_query($link, $sql);
foreach ($result as $user)
{
    if (strtolower($user["USERNAME"]) === strtolower($username))
    {
        $message = "Username already exists. Please pick a new Username.";
        gotoResultPage($message);
    }

    // Check duplicate email
    if (strtolower($user["EMAIL"]) === strtolower($email))
    {
        $message = "Email is already registered to an account. Please use a different Email.";
        gotoResultPage($message);
    }
}

// Sanitizing input to deal with SQL injection
$firstname= str_replace("'", "", $firstname);
$lastname= str_replace("'", "", $lastname);
$email= str_replace("'", "", $email);
$email2= str_replace("'", "", $email2);
$username = str_replace("'", "", $username);
$password= str_replace("'", "", $password);


if (strtolower($email) != strtolower($email2)) {
    $message = "Email does not match! Please try again.";
    gotoResultPage($message);
}

// Attempt insert
$sql = "INSERT INTO users (FIRST_NAME, LAST_NAME, EMAIL, USERNAME, PASSWORD) VALUES ('$firstname', '$lastname', '$email', '$username', '$password')";
if(mysqli_query($link, $sql)){
    $message = "Successfully registered.";
    gotoResultPage($message);
} else{
    $message = "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    gotoResultPage($message);
}

// Close connection
mysqli_close($link);
?>