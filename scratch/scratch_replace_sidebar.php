<?php
$files = [
    'views/dashboard.php',
    'views/informes/index.php',
    'views/pacientes/crear.php',
    'views/pacientes/editar.php',
    'views/pacientes/listar.php',
    'views/perfil/index.php',
    'views/planes/index.php',
    'views/turnos/form.php',
    'views/turnos/index.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Expresión regular para encontrar el div de sidebar completo
        // Busca <div class="sidebar"> hasta su cierre correspondiente.
        $pattern = '/<div class="sidebar">.*?<\/div>\s*<\/div>/s';
        
        // Como el sidebar tiene varios <div> adentro, Regex simple puede fallar.
        // Mejor buscamos por string desde <div class="sidebar"> hasta </ul>\s*</div>
        
        $pattern2 = '/<div class="sidebar">.*?<\/ul>\s*<\/div>/s';
        
        $newContent = preg_replace($pattern2, "<?php include 'views/layout/sidebar.php'; ?>", $content);
        
        if ($newContent !== $content && $newContent !== null) {
            file_put_contents($file, $newContent);
            echo "Actualizado: $file\n";
        } else {
            echo "No se encontró el patrón en: $file\n";
        }
    } else {
        echo "No existe: $file\n";
    }
}
?>
