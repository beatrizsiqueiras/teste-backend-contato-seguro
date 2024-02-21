<?php
date_default_timezone_set('America/Sao_Paulo');

function now(): string | false
{
    return date('Y-m-d H:i:s');
}
