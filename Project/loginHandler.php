<?php
/* ---User Authentification---
 * Username and password <= 50 characters
 * Unlimited attempts allowed
 * Certain characters are not allowed: ' " / \ [ ] ( ) { }
 * Password is hidden when typed
 * Username is case-insensitive
 * Password is case-sensitive !--This is done within phpMyAdmin by settings the PASSWORD column collation to latin1_general_cs--!
 */

require_once('myfuncs.php');

$link = dbConnect();

// Input
$username = $_POST['Username'];
$password = $_POST['Password'];

// Check for empty input, otherwise exit out with an error
if ($username == NULL)  { gotoResultPage("The username is a required field and cannot be blank.\n"); }
if ($password == NULL)  { gotoResultPage("The password is a required field and cannot be blank.\n"); }

// Sanitizing input in case of sql injection
$username = str_replace("'", "", $username);
$password= str_replace("'", "", $password);

$sql = "SELECT ID, FIRST_NAME, LAST_NAME, EMAIL, USERNAME, PASSWORD FROM users WHERE USERNAME='$username' AND PASSWORD='$password'";
$result = mysqli_query($link, $sql);
$numRows = mysqli_num_rows($result);

// Check for matches in our database
// We have too many of this user
if ($numRows > 2) {
    $message = "There are multiple users registered with that information";
    gotoResultPage($message);
}
else if ($numRows == 1) {
    $row = $result->fetch_assoc();	// Read the Row from the Query
    saveUserId($row["ID"]);		// Save User ID in the Session
    // Close connection
    mysqli_close($link);
    gotoResultPage("Welcome " . $row["FIRST_NAME"] . "!");
}
else if ($numRows == 0) {
    $message = "Login Failed.";
    gotoResultPage($message);
}
else {
    $message = "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    gotoResultPage($message);
}

// Close connection
mysqli_close($link);
?>