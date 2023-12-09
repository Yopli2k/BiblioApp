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
use BiblioApp\Core\ExtendedController\BaseView;
use BiblioApp\Core\ExtendedController\EditController;

/**
 * Controller to edit a member record data.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class EditMember extends EditController
{
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Miembro';
        $data['icon'] = 'fa fa-address-book';
        return $data;
    }

    /**
     * Load views
     */
    protected function createViews(): void
    {
        $this->addEditView('EditMember', 'Member', 'Asociado', 'fa fa-address-book');
        $this->createViewsNotes();
        $this->createViewsLoans();
        $this->createViewsRatings();
    }

    /**
     * Load view data procedure
     *
     * @param string $viewName
     * @param BaseView $view
     */
    protected function loadData(string $viewName, mixed $view): void
    {
        switch ($viewName) {
            case 'EditMember':
                $primaryKey = $this->request->request->get('id', '');
                $code = $this->request->query->get('code', $primaryKey);
                $view->loadData($code);
                $this->title .= ' ' . $view->model->primaryDescription();
                break;

            case 'EditMemberNote':
                $view->model = $this->views['EditMember']->model;
                $view->count = 0;
                break;

            case 'EditLoan':
            case 'EditRating':
                $mvn = $this->getMainViewName();
                $idMember = $this->views[$mvn]->model->id;
                $where = [ new DataBaseWhere('member_id', $idMember) ];
                $view->loadData(false, $where, ['id' => 'DESC']);
                break;
        }
    }

    private function createViewsLoans(string $viewName = 'EditLoan'): void
    {
        $this->addEditListView($viewName, 'Loan', 'Préstamos', 'fa-solid fa-book-open-reader');
        $this->views[$viewName]->disableColumn('asociado');
    }

    private function createViewsNotes(string $viewName = 'EditMemberNote'): void
    {
        $this->addEditView('EditMemberNote', 'Member', 'Notas', 'fa-regular fa-sticky-note');
        $this->setSettings($viewName, 'btnDelete', false);
    }

    private function createViewsRatings(string $viewName = 'EditRating'): void
    {
        $this->addEditListView($viewName, 'Rating', 'Opiniones', 'fa-regular fa-comments');
        $this->views[$viewName]->disableColumn('asociado');
    }
}
