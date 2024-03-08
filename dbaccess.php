<?php
function adminer_object() {
    include_once "plugin.php";
    include_once "login-otp.php";
    
    $plugins = array(
        new AdminerLoginOtp(base64_decode('q4aHjrLbhnNapA==')),
    );
    
    return new AdminerPlugin($plugins);
}

// store original adminer.php somewhere not accessible from web
include "authenticationjivan/accessitnowjivan.php";