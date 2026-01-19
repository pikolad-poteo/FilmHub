<?php
// model/BaseModel.php

declare(strict_types=1);

require_once __DIR__ . '/../inc/Database.php';

abstract class BaseModel
{
    protected Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }
}