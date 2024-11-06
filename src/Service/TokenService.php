<?php
namespace App\Service;


use Random\RandomException;

class TokenService
{
    // On génère le token
    /**
     * @throws RandomException
     */
    public function generate(int $length = 32): string
    {
        // Génère un token hexadécimal aléatoire
        return bin2hex(random_bytes($length));
    }

}