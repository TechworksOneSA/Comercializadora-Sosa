<?php 
try { 
    \ = new PDO('mysql:host=localhost;dbname=comercializadora_sosa', 'root', ''); 
    echo 'Estructura de tabla categorias:' . PHP_EOL; 
    \ = \->query('DESCRIBE categorias'); 
    while(\ = \->fetch(PDO::FETCH_ASSOC)) { 
        echo \['Field'] . ' - ' . \['Type'] . ' - ' . \['Null'] . PHP_EOL; 
    } 
    echo PHP_EOL . 'Contenido actual:' . PHP_EOL; 
    \ = \->query('SELECT * FROM categorias LIMIT 5'); 
    while(\ = \->fetch(PDO::FETCH_ASSOC)) { 
        print_r(\); 
    } 
} catch(Exception \) { 
    echo 'Error: ' . \->getMessage(); 
}
