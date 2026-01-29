<?php

class BaseModel
{
    protected static function db(): Database
    {
        return new Database();
    }
}
