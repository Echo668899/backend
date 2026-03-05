<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Repositories\Backend\Admin\AdminUserRepository;

/**
 * Class IndexController
 * @package App\Controller\Backend
 */
class IndexController extends BaseBackendController
{
    public function indexAction()
    {
        $menus = AdminUserRepository::getMenus();
        $this->view->setVars([
            'menus' => $menus,
        ]);
    }
}
