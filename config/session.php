<?php
if (isset($_COOKIE['session-name'])) {
    $encodedSession = $_COOKIE['session-name'];
    $sessionJSON = base64_decode($encodedSession);
    $sessionData = json_decode($sessionJSON, true);
    
    if ($sessionData) {
        $_SESSION['username'] = $sessionData['username'];
        $_SESSION['level'] = $sessionData['level'];
    } else {
        // If the session is invalid, just unset the session
        unset($_SESSION['username']);
        unset($_SESSION['level']);
    }
} else {
    // If the cookie is not found, just unset the session
    unset($_SESSION['username']);
    unset($_SESSION['level']);
}

?>
