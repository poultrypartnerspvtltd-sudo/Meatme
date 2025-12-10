<?php

namespace App\Middleware;

use App\Core\CSRF as CSRFCore;

class CSRF
{
    public function handle()
    {
        return CSRFCore::middleware();
    }
}
?>
