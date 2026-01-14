<?php
declare(strict_types=1);

class ProductosApiController extends Controller
{
    public function buscarPorScan(): void
    {
        // Entrega JSON; este archivo hará echo + exit
        require __DIR__ . "/api/buscar_por_scan.php";
        exit;
    }
}
