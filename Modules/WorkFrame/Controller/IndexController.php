<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Base\Controller\Controller;
use Alxarafe\Lib\Auth;
use Alxarafe\Lib\Functions;

/**
 * Index controller — entry point that redirects to dashboard or login.
 */
class IndexController extends Controller
{
    protected function shouldEnforceAuth(): bool
    {
        return false;
    }

    public function doIndex(): bool
    {
        if (Auth::isLogged()) {
            Functions::httpRedirect(DashboardController::url('index'));
        } else {
            Functions::httpRedirect(\CoreModules\Admin\Controller\AuthController::url(true, false));
        }
        return true;
    }
}
