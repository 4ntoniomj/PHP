<?php
// INICIA SESIÓN ANTES QUE TODO - SIEMPRE AL INICIO
session_start();

// Cargar auth.php DESPUÉS de iniciar sesión
require_once __DIR__ . '/../app/auth.php';

// Ahora sí, cerrar sesión
logout();

// Redirigir al login
header('Location: login.php');
exit;