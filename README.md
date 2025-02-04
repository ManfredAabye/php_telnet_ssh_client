<img src="https://github.com/ManfredAabye/OpenSim.RemoteAdmin.2025/blob/main/flags/En.png?raw=true" alt="Project Badge"> <img src="https://github.com/ManfredAabye/OpenSim.RemoteAdmin.2025/blob/main/flags/De.png?raw=true" alt="Project Badge">
# php_telnet_ssh_client 2025
php 8.3 telnet ssh client

## Guide to Telnet and SSH Client

This script allows you to send commands over Telnet or SSH to a remote server and display the response. It offers security features such as rate limiting, CSRF protection, and command blacklist and whitelist checks.

## Features

- **Telnet and SSH Connections:** The script can establish a connection to a remote server via Telnet or SSH.
- **Rate Limiting:** Limits the number of login attempts to prevent brute-force attacks.
- **CSRF Protection:** Protects against cross-site request forgery (CSRF) using CSRF tokens.
- **Command Blacklist and Whitelist Checks:** Verifies entered commands against a blacklist and/or whitelist to block unwanted or dangerous commands.

## Configuration

The configuration is done in the PHP code. There are two control variables to manage the blacklist and whitelist checks:

```php
$useBlacklist = true;  // Enables or disables the blacklist check
$useWhitelist = true;  // Enables or disables the whitelist check
```

### Blacklist

The blacklist contains dangerous or unwanted commands that should be blocked:

```php
$blacklistedCommands = [
    'rm', 'shutdown', 'reboot', 'mkfs', 'dd', 'passwd', 'su', 'sudo', 'chown', 'chmod', 'kill',
];
```

### Whitelist

The whitelist contains allowed commands that can be safely executed:

```php
$allowedCommands = [
    'ls', 'pwd', 'whoami', 'date', 'uptime', 'uname -a', 'df -h', 'free -m', 'top -b -n 1', 'ps aux', 'id', 'netstat -tuln', 'ifconfig', 'ping -c 4 example.com', 'traceroute example.com',
];
```

## Usage

1. **Session Management:** The session is started and managed at the beginning of the script. Secure session options are used.
2. **Establish Connection:** Users can enter the connection details and establish a connection to the remote server.
3. **Send Command:** After the connection is established, a command can be entered and sent. The script checks the command against the blacklist and/or whitelist.

### Example

```php
// Configuration variables
$useBlacklist = true;
$useWhitelist = true;

// Define blacklist and whitelist
$blacklistedCommands = [
    'rm', 'shutdown', 'reboot', 'mkfs', 'dd', 'passwd', 'su', 'sudo', 'chown', 'chmod', 'kill',
];

$allowedCommands = [
    'ls', 'pwd', 'whoami', 'date', 'uptime', 'uname -a', 'df -h', 'free -m', 'top -b -n 1', 'ps aux', 'id', 'netstat -tuln', 'ifconfig', 'ping -c 4 example.com', 'traceroute example.com',
];
```

With this guide, you should be able to use and customize the script to safely send commands over Telnet or SSH to remote servers.

---

## Anleitung zum Telnet und SSH Client

Dieses Skript ermöglicht es, Befehle über Telnet oder SSH an einen entfernten Server zu senden und die Antwort anzuzeigen. 
Es bietet Sicherheitsfunktionen wie Rate-Limiting, CSRF-Schutz sowie Blacklist- und Whitelist-Prüfung für Befehle.

## Funktionen

- **Telnet- und SSH-Verbindungen:** Das Skript kann eine Verbindung zu einem entfernten Server über Telnet oder SSH herstellen.
- **Rate-Limiting:** Begrenzung der Anzahl von Anmeldeversuchen, um Brute-Force-Angriffe zu verhindern.
- **CSRF-Schutz:** Schutz gegen Cross-Site-Request-Forgery (CSRF) durch Verwendung von CSRF-Tokens.
- **Blacklist- und Whitelist-Prüfung:** Überprüfung der eingegebenen Befehle anhand einer Blacklist und/oder Whitelist, um unerwünschte oder gefährliche Befehle zu blockieren.

## Konfiguration

Die Konfiguration erfolgt im PHP-Code. Es gibt zwei Kontrollvariablen, um die Blacklist- und Whitelist-Prüfung zu steuern:

```php
$useBlacklist = true;  // Schaltet die Blacklist-Prüfung ein oder aus
$useWhitelist = true;  // Schaltet die Whitelist-Prüfung ein oder aus
```

### Blacklist

Die Blacklist enthält gefährliche oder unerwünschte Befehle, die blockiert werden sollen:

```php
$blacklistedCommands = [
    'rm', 'shutdown', 'reboot', 'mkfs', 'dd', 'passwd', 'su', 'sudo', 'chown', 'chmod', 'kill',
];
```

### Whitelist

Die Whitelist enthält erlaubte Befehle, die sicher ausgeführt werden können:

```php
$allowedCommands = [
    'ls', 'pwd', 'whoami', 'date', 'uptime', 'uname -a', 'df -h', 'free -m', 'top -b -n 1', 'ps aux', 'id', 'netstat -tuln', 'ifconfig', 'ping -c 4 example.com', 'traceroute example.com',
];
```

## Nutzung

1. **Session-Management:** Die Sitzung wird zu Beginn des Skripts gestartet und verwaltet. Es werden sichere Sitzungsoptionen verwendet.

2. **Verbindung herstellen:** Benutzer können die Verbindungsdetails eingeben und die Verbindung zum entfernten Server herstellen.

3. **Befehl senden:** Nach Herstellung der Verbindung kann ein Befehl eingegeben und gesendet werden. Das Skript überprüft den Befehl anhand der Blacklist und/oder Whitelist.

### Beispiel

```php
// Konfigurationsvariablen
$useBlacklist = true;
$useWhitelist = true;

// Blacklist und Whitelist definieren
$blacklistedCommands = [
    'rm', 'shutdown', 'reboot', 'mkfs', 'dd', 'passwd', 'su', 'sudo', 'chown', 'chmod', 'kill',
];

$allowedCommands = [
    'ls', 'pwd', 'whoami', 'date', 'uptime', 'uname -a', 'df -h', 'free -m', 'top -b -n 1', 'ps aux', 'id', 'netstat -tuln', 'ifconfig', 'ping -c 4 example.com', 'traceroute example.com',
];
```

Mit dieser Anleitung sollten Sie in der Lage sein, das Skript zu verwenden und anzupassen, um Befehle sicher über Telnet oder SSH an entfernte Server zu senden.
