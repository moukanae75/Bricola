<?php

// Démarresessionr 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger config DB directement
require_once __DIR__ . '/../config/database.php';

// Charger les routes
require_once __DIR__ . '/../routes/web.php';