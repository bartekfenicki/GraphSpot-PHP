<?php
// Prevent Clickjacking
header("X-Frame-Options: SAMEORIGIN");

// Prevent XSS
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; img-src 'self' data: https://example.com; font-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com; frame-src https://www.google.com; connect-src 'self';");

// Prevent MIME Sniffing
header("X-Content-Type-Options: nosniff");

// Enable XSS Protection 
header("X-XSS-Protection: 1; mode=block");

// Additional security headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Referrer-Policy: no-referrer-when-downgrade"); 
header("Permissions-Policy: geolocation=(), microphone=(), camera=()"); 

session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

?>