<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    // Đã kế thừa BaseController để sử dụng được $this->middleware()
}