<?php
// Evitar múltiples inclusiones
if (!isset($conexion)) {
    $conectionInstance = ConnectionBD::getInstance();
    $conexion = $conectionInstance->getConnection();
}

