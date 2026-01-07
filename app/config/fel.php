<?php

/**
 * Configuración para integración FEL (Facturación Electrónica en Línea)
 * SAT Guatemala - compatible con Hostinger
 */

class FelConfig
{
    // Configuraciones del certificador FEL
    const CERTIFICADOR_ENDPOINTS = [
        'infile' => [
            'test' => 'https://certificador.infile.com.gt/test/',
            'prod' => 'https://certificador.infile.com.gt/prod/'
        ],
        'digifact' => [
            'test' => 'https://felgttestv2.digifact.com.gt/',
            'prod' => 'https://felgtv2.digifact.com.gt/'
        ]
        // TODO: Agregar más certificadores según necesidad
    ];

    // Configuración actual (se configurará después)
    const ACTIVE_CERTIFICADOR = 'infile';
    const ENVIRONMENT = 'test'; // test, prod

    // Credenciales (placeholder - configurar en producción)
    const FEL_NIT = '';
    const FEL_USERNAME = '';
    const FEL_PASSWORD = '';
    const FEL_CODIGO_ESTABLECIMIENTO = '';

    // Tipos de documento FEL
    const TIPOS_DOCUMENTO = [
        'FACT' => 'Factura',
        'FCAM' => 'Factura Cambiaria',
        'FPEQ' => 'Factura Pequeño Contribuyente',
        'FCAP' => 'Factura Contribuyente Agropecuario',
        'FESP' => 'Factura Especial',
        'NABN' => 'Nota de Abono',
        'RDON' => 'Recibo por Donación',
        'RECI' => 'Recibo',
        'NDEB' => 'Nota de Débito',
        'NCRE' => 'Nota de Crédito'
    ];

    // Configuración de timeout para cURL
    const CURL_TIMEOUT = 30;
    const CURL_CONNECT_TIMEOUT = 10;

    public static function getEndpoint()
    {
        return self::CERTIFICADOR_ENDPOINTS[self::ACTIVE_CERTIFICADOR][self::ENVIRONMENT];
    }

    public static function isProduction()
    {
        return self::ENVIRONMENT === 'prod';
    }

    public static function getCurlOptions()
    {
        return [
            CURLOPT_TIMEOUT => self::CURL_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::CURL_CONNECT_TIMEOUT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => self::isProduction(),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ];
    }
}
