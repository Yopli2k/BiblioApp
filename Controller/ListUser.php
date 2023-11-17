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

use BiblioApp\Core\DataBase\DataBaseWhere;
use BiblioApp\Core\ExtendedController\ListController;

class ListUser extends ListController
{

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Usuarios';
        $data['icon'] = 'fas fa-users';
        return $data;
    }

    protected function createViews(): void
    {
        $this->createViewsUsers();
        $this->createViewsWebContacts();
    }

    /**
     * @param string $viewName
     */
    private function createViewsUsers(string $viewName = 'ListUser'): void
    {
        $this->addView($viewName, 'User', 'Usuarios', 'fas fa-users');
        $this->addSearchFields($viewName, ['name', 'username', 'email']);
        $this->addOrderBy($viewName, ['name'], 'Nombre');
        $this->addOrderBy($viewName, ['username'], 'Usuario');
        $this->addOrderBy($viewName, ['email'], 'Correo');
        $this->setSettings($viewName, 'btnDelete', false);
        $this->setSettings($viewName, 'checkBoxes', false);
    }

    private function createViewsWebContacts(string $viewName = 'ListWebContact'): void
    {
        $this->addView($viewName, 'WebContact', 'Contactos Web', 'fas fa-envelope');
        $this->addSearchFields($viewName, ['name', 'email', 'phone']);
        $this->addOrderBy($viewName, ['creationdate', 'creationtime'], 'Fecha');
        $this->addOrderBy($viewName, ['name'], 'Nombre');
        $this->addOrderBy($viewName, ['email'], 'Correo');

        $this->addFilterPeriod($viewName, 'creationdate', 'Fecha', 'creationdate');
        $this->addFilterAutocomplete($viewName, 'member', 'Asociado', 'member_id', 'members', 'id', 'name');
        $this->addFilterSelectWhere($viewName, 'status', [
            ['label' => 'Pendiente', 'where' => [ new DataBaseWhere('resolved', false) ] ],
            ['label' => 'Resuelto', 'where' => [ new DataBaseWhere('resolved', true) ] ],
            ['label' => 'Todos', 'where' => []]
        ], 'Estado');
    }
}