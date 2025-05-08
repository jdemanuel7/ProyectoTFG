<?php
// pages/logout.php

session_start();      // Recupera la sesión actual
session_unset();      // Elimina todas las variables de sesión
session_destroy();    // Destruye la sesión por completo

// Redirige al inicio
header('Location: /index.php');
exit;
