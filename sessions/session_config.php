<?php
// Ustawienia zwiększające bezpieczeństwo
ini_set('session.cookie_httponly', 1); // Blokuje dostęp JS do ID sesji
ini_set('session.use_only_cookies', 1); // Zapobiega przekazywaniu ID w URL
ini_set('session.cookie_secure', 1);   // Tylko przez HTTPS (jeśli masz certyfikat)

session_start();
?>