<?php

namespace App\Utilities\SCIO;

class CoordsIDGenerator
{
    protected $coordinates;

    public function __construct($coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public function getId(): string
    {
        return hash('sha256', (serialize($this->coordinates)));
    }
}
