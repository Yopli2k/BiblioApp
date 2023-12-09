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

use BiblioApp\Core\ExtendedController\BaseView;
use BiblioApp\Core\ExtendedController\EditController;

/**
 * Controller to edit a loan record data.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class EditLoan extends EditController
{
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Préstamo';
        $data['icon'] = 'fa-solid fa-book-open-reader';
        return $data;
    }

    /**
     * Load views
     */
    protected function createViews(): void
    {
        $this->addEditView('EditLoan', 'Loan', 'Préstamo', 'fa-solid fa-book-open-reader');
    }

    /**
     * Load view data procedure
     *
     * @param string $viewName
     * @param BaseView $view
     */
    protected function loadData(string $viewName, mixed $view): void
    {
        if ($viewName == 'EditLoan') {
            $primaryKey = $this->request->request->get('id', '');
            $code = $this->request->query->get('code', $primaryKey);
            $view->loadData($code);
            $this->title .= ' ' . $view->model->primaryDescription();
        }
    }
}
