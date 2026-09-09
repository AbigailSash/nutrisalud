<?php
require 'config/Conexion.php';
$c = Conexion::conectar();
$s = $c->query('DESCRIBE Alimento');
print_r($s->fetchAll());
?>
