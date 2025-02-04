<?php
/*
 * telnet_ssh_client.php (2025) Created under PHP 8.3
 *
 * @author Manfred Zainhofer
 * @copyright Copyright (c) 2025, OpenSim Community.
 * @license MIT License
 * @version 1.0.2
 * @link https://github.com/ManfredAabye/php_telnet_ssh_client
 */

 session_start([
     'cookie_secure' => true,
     'cookie_httponly' => true,
     'use_strict_mode' => true
 ]);
 
 if (empty($_SESSION['csrf_token'])) {
     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
 }
 
 if (isset($_POST['logout'])) {
     $_SESSION = [];
     session_destroy();
     echo '<p style="color: green;">Session beendet.</p>';
     echo '<meta http-equiv="refresh" content="2; URL=./telnet_ssh_client.php">';
     exit();
 }
 
 // Kontrollvariablen für Blacklist und Whitelist
 $useBlacklist = true;  // Schalten Sie die Blacklist-Prüfung ein oder aus
 $useWhitelist = true;  // Schalten Sie die Whitelist-Prüfung ein oder aus
 
 // Validierungsfunktionen
 function validateHost($host) {
     return filter_var($host, FILTER_VALIDATE_DOMAIN) || filter_var($host, FILTER_VALIDATE_IP);
 }
 
 function validatePort($port) {
     return $port >= 1 && $port <= 65535;
 }
 
 function rateLimit() {
     if (!isset($_SESSION['login_attempts'])) {
         $_SESSION['login_attempts'] = 0;
     }
     $_SESSION['login_attempts']++;
     if ($_SESSION['login_attempts'] > 5) {
         throw new Exception('Zu viele Login-Versuche. Bitte versuchen Sie es später erneut.');
     }
 }
 
 // Blacklist von gefährlichen Befehlen
 $blacklistedCommands = [
     'rm', 'shutdown', 'reboot', 'mkfs', 'dd', 'passwd', 'su', 'sudo', 'chown', 'chmod', 'kill',
 ];
 
 // Whitelist von erlaubten Befehlen
 $allowedCommands = [
     'ls', 'pwd', 'whoami', 'date', 'uptime', 'uname -a', 'df -h', 'free -m', 'top -b -n 1', 'ps aux', 'id', 'netstat -tuln', 'ifconfig', 'ping -c 4 example.com', 'traceroute example.com',
 ];
 
 function isCommandBlacklisted($command) {
     global $blacklistedCommands;
     foreach ($blacklistedCommands as $blacklistedCommand) {
         if (strpos($command, $blacklistedCommand) !== false) {
             return true;
         }
     }
     return false;
 }
 
 function isCommandAllowed($command) {
     global $allowedCommands;
     return in_array($command, $allowedCommands);
 }
 
 function isCommandSafe($command) {
     global $useBlacklist, $useWhitelist;
     if ($useBlacklist && isCommandBlacklisted($command)) {
         return false;
     }
     if ($useWhitelist && !isCommandAllowed($command)) {
         return false;
     }
     return true;
 }
 
 // Funktion zum Öffnen einer Telnet-Verbindung
 function openTelnetConnection($host, $port, $timeout) {
     if (!validateHost($host) || !validatePort($port)) {
         throw new Exception('Ungültiger Host oder Port.');
     }
 
     $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
     if (!$fp) {
         throw new Exception("Verbindung fehlgeschlagen: $errno - $errstr");
     }
     return $fp;
 }
 
 function isConnected($host, $port, $timeout = 10) {
     if (!validateHost($host) || !validatePort($port)) {
         return false;
     }
 
     $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
     if ($fp) {
         fclose($fp);
         return true;
     } else {
         return false;
     }
 }
 
 $is_connected = isConnected($_SESSION['host'] ?? '', (int)$_SESSION['port'] ?? 0);
 
 // Funktion zum Senden eines Telnet-Befehls und zum Empfangen der Antwort
 function sendTelnetCommand($fp, $username, $password, $command) {
     if (!isCommandSafe($command)) {
         throw new Exception('Unzulässiger Befehl.');
     }
 
     // Anmeldeinformationen senden
     fwrite($fp, $username . "\n");
     usleep(500000); // Kurze Verzögerung, um die Eingabe zu simulieren
     fwrite($fp, $password . "\n");
     usleep(500000); // Kurze Verzögerung, um die Eingabe zu simulieren
 
     // Befehl senden
     fwrite($fp, $command . "\n");
     $response = '';
     while ($line = fgets($fp, 128)) {
         $response .= $line;
     }
     return $response;
 }
 
 // Funktion zum Senden eines SSH-Befehls und zum Empfangen der Antwort
 function sendSshCommand($host, $port, $username, $password, $command) {
     if (!validateHost($host) || !validatePort($port) || !isCommandSafe($command)) {
         throw new Exception('Ungültiger Host, Port oder Befehl.');
     }
 
     $connection = ssh2_connect($host, $port);
     if (!$connection) {
         throw new Exception('Verbindung fehlgeschlagen.');
     }
 
     if (!ssh2_auth_password($connection, $username, $password)) {
         throw new Exception('Authentifizierung fehlgeschlagen.');
     }
 
     $stream = ssh2_exec($connection, $command);
     if (!$stream) {
         throw new Exception('Befehlsausführung fehlgeschlagen.');
     }
 
     stream_set_blocking($stream, true);
     $response = stream_get_contents($stream);
     fclose($stream);
     ssh2_disconnect($connection);
 
     return $response;
 }
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connect'])) {
     $protocol = $_POST['protocol'];
     $_SESSION['protocol'] = $protocol;
     $_SESSION['host'] = $_POST['host'];
     $_SESSION['port'] = $_POST['port'];
     $_SESSION['username'] = $_POST['username'];
     $_SESSION['password'] = $_POST['password']; // Passwort nicht ändern
 }
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
     rateLimit();
 
     $host = $_SESSION['host'];
     $port = (int)$_SESSION['port']; // Sicherstellen, dass der Port als Integer gespeichert wird
     $username = $_SESSION['username'];
     $password = $_SESSION['password'];
     $command = $_POST['command'];
     $protocol = $_SESSION['protocol'];
 
     try {
         if (!$host || !$port || !$username || !$password) {
             throw new Exception('Ein oder mehrere Pflichtfelder sind leer.');
         }
 
         if ($protocol === 'telnet') {
             $fp = openTelnetConnection($host, $port, 10); // 10 Sekunden Timeout
             $response = sendTelnetCommand($fp, $username, $password, $command);
             fclose($fp);
         } elseif ($protocol === 'ssh') {
             $response = sendSshCommand($host, $port, $username, $password, $command);
         } else {
             throw new Exception('Unbekanntes Protokoll.');
         }
 
         $_SESSION['response'] = $response;
 
     } catch (Exception $e) {
         $_SESSION['error'] = $e->getMessage();
     }
 
     header('Location: ' . $_SERVER['PHP_SELF']);
     exit();
 }
 
 $protocol = $_SESSION['protocol'] ?? 'ssh'; // Voreinstellung auf SSH setzen
 $host = $_SESSION['host'] ?? '';
 $port = $_SESSION['port'] ?? '';
 $username = $_SESSION['username'] ?? '';
 $password = $_SESSION['password'] ?? '';
 $command = $_POST['command'] ?? '';
 $response = $_SESSION['response'] ?? '';
 $error = $_SESSION['error'] ?? '';
 
 // Sitzung aufräumen
 unset($_SESSION['response']);
 unset($_SESSION['error']);

 ?> 

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Telnet und SSH Client</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background-color: white; padding: 20px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        .status-icon { top: 10px; right: 10px; width: 20px; height: 20px; }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 10px; }
        label { font-weight: bold; }
        input, textarea, select, button { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button {  background-color: #4CAF50; border: none; color: white;  padding: 12px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;}
        button:hover { background-color: #45a049; }
        .output-container { background-color: #000; color: #fff; padding: 10px; border-radius: 5px; border: 1px solid #ddd; white-space: pre-wrap; word-wrap: break-word; height: 350px; overflow-y: auto; display: flex; flex-direction: column; }
        .output { flex: 1; overflow-y: auto; }
        .console-input { background-color: #000; color: #fff; padding: 10px; border: none; border-top: 0px solid #ddd; width: 100%; box-sizing: border-box; }
        .console-input:focus { outline: none; border-color: #000; }
        .input-group { margin: 15px 0;}
        .button.reset {    background-color: #f44336;}
        .button.reset:hover {    background-color: #da190b;}
        .button.logout {    background-color: #ca8d24;}
        .button.logout:hover {    background-color: #e2ae54;}
    </style>
</head>
<body>

<div class="container">
    <h2>Telnet und SSH Client</h2>    
    <form method="post">
    <img src="<?php echo $is_connected ? 'pics/online.png' : 'pics/offline.png'; ?>" alt="Status Icon" class="status-icon">
        <label for="protocol">Protokoll:</label>        
        <select id="protocol" name="protocol" required>
            <option value="telnet" <?php if ($protocol === 'telnet') echo 'selected'; ?>>Telnet</option>
            <option value="ssh" <?php if ($protocol === 'ssh') echo 'selected'; ?>>SSH</option>
        </select>

        <label for="host">Host:</label>
        <input type="text" id="host" name="host" value="<?php echo htmlspecialchars($host); ?>" required>

        <label for="port">Port:</label>
        <input type="text" id="port" name="port" value="<?php echo htmlspecialchars($port); ?>" required>

        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>

        <div class="input-group">
        <button class="button" type="submit" name="connect">Verbinden</button>
        <button class="button reset" type="reset" name="Reset">Data Reset</button>
        <button class="button logout" type="submit" name="logout">Session Close</button>
        </div>
    </form>

    <br> <!-- Leerzeile eingefügt -->

    <div class="output-container">
        <div class="output">
            <?php if (!empty($response)): ?>
                <pre><?php echo htmlspecialchars($response); ?></pre>
            <?php elseif (!empty($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php else: ?>
                <p>Hier werden die Ausgaben angezeigt.</p>
            <?php endif; ?>
        </div>
        
        <form method="post">
            <input type="hidden" name="protocol" value="<?php echo htmlspecialchars($protocol); ?>">
            <input type="hidden" name="host" value="<?php echo htmlspecialchars($host); ?>">
            <input type="hidden" name="port" value="<?php echo htmlspecialchars($port); ?>">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
            <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">

            <input type="text" name="command" class="console-input" placeholder="Gib hier deinen Befehl ein" required>
            <button type="submit">Befehl senden</button>
        </form>
    </div>
</div>

</body>
</html>

