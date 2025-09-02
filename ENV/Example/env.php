<?php
// env.php - Configuration for SMTP (SSL)
return [
    'smtp_host'     => 'smtp.example.com',
    'smtp_user'     => 'your@email.com',
    'smtp_pass'     => 'your-password',
    'smtp_port'     =>  465,
    'smtp_secure'   => 'ssl'
];

// for TLS:
//  'smtp_port'     =>  587,
//  'smtp_secure'   => 'tls',