<?php

// Encrypt password with sha256 and salt
function encrypt($password)  {
    return hash("sha256", $password . "salt");
}