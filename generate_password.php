<?php
// Replace 'qwerty' with any password you want to hash
$password = 'qwerty';

// Hash the password using BCRYPT
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Display the hashed result
echo "Hashed Password: " . $hashed;
?>
