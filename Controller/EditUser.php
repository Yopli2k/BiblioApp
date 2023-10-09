<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace BiblioApp\Controller;

use BiblioApp\Core\ExtendedController\EditController;

/*
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Lib\ExtendedController\BaseView;
use FacturaScripts\Dinamic\Model\Almacen;
use FacturaScripts\Dinamic\Model\Page;
use FacturaScripts\Dinamic\Model\RoleUser;
use FacturaScripts\Dinamic\Model\User;
use Symfony\Component\HttpFoundation\Cookie;
*/

/**
 * Controller to edit a User record data.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class EditUser extends EditController
{
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Usuarios';
        $data['icon'] = 'fas fa-users';
        return $data;
    }

    /**
     * Load views
     */
    protected function createViews(): void
    {
        $this->addEditView('EditUser', 'User', 'Usuarios', 'fas fa-user-edit');
    }

    /**
     * Load view data procedure
     *
     * @param string $viewName
     * @param BaseView $view
     */
    protected function loadData($viewName, $view): void
    {
        switch ($viewName) {
            case 'EditUser':
                $primaryKey = $this->request->request->get('username');
                $code = $this->request->query->get('code', $primaryKey);
                $view->loadData($code);
                $this->title .= ' ' . $view->model->primaryDescription();
                break;
        }
    }
}
