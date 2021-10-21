<?php
namespace Espo\Modules\Wischat\Controllers;

use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;
use \Espo\Core\Exceptions\NotFound;

class WischatMatchingConfiguration extends \Espo\Core\Controllers\Base
{
    protected function checkControllerAccess()
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function putActionUpdate($params, $data, $request)
    {
        $this->getServiceFactory()->create('WischatMatchingConfiguration')->setMatchingParameters($data);

        $this->getContainer()->get('dataManager')->rebuild();

        return true;
    }
}
