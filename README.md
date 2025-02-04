# php_telnet_ssh_client
php 8.3 telnet ssh client

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
