<?php

namespace App\Http\Controllers;

use App\Http\Utils\ErrorUtil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class SetupController extends Controller
{
    use ErrorUtil;


    public function setup()
    {
        Artisan::call('passport:install');
        Artisan::call('migrate', ['--path' => 'vendor/laravel/passport/database/migrations']);

        Artisan::call('check:migrate');
     
        Artisan::call('l5-swagger:generate');

    
        return "ok";
    }

    public function migrate(Request $request)
    {
        Artisan::call('check:migrate');
        return "migrated";
    }

}
