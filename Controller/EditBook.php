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
 * Controller to edit a book record data.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class EditBook extends EditController
{
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Libro';
        $data['icon'] = 'fa-solid fa-book-bookmark';
        return $data;
    }

    /**
     * Load views
     */
    protected function createViews(): void
    {
        $this->addEditView('EditBook', 'Book', 'Libro', 'fa-solid fa-book-bookmark');
        $this->addEditListView('EditBookCategory', 'BookCategory', 'Categorías', 'fa-solid fa-object-group');
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
            case 'EditBook':
                $primaryKey = $this->request->request->get('id', '');
                $code = $this->request->query->get('code', $primaryKey);
                $view->loadData($code);
                $this->title .= ' ' . $view->model->primaryDescription();
                break;

            case 'ListRating':
            case 'EditLoan':
            case 'EditBookCategory':
                $mvn = $this->getMainViewName();
                $idBook = $this->views[$mvn]->model->id;
                $where = [ new DataBaseWhere('book_id', $idBook) ];
                $view->loadData(false, $where, ['id' => 'DESC']);
                break;
        }
    }

    private function createViewsLoans(string $viewName = 'EditLoan'): void
    {
        $this->addEditListView($viewName, 'Loan', 'Préstamos', 'fa-solid fa-book-open-reader');
        $this->views[$viewName]->disableColumn('libro');
    }

    private function createViewsRatings(string $viewName = 'ListRating'): void
    {
        $this->addListView($viewName, 'Rating', 'Opiniones', 'fa-regular fa-comments');
        $this->views[$viewName]->addSearchFields(['valoration']);
        $this->views[$viewName]->addOrderBy(['rating_date', 'rating_time', 'id'], 'Fecha', 2);
        $this->views[$viewName]->addOrderBy(['rating', 'id'], 'Valoración');

        $this->views[$viewName]->disableColumn('libro');

        // TODO: fix filter error
        // $this->views[$viewName]->addFilterAutocomplete('member_id', 'Asociado', 'member_id', 'members', 'id', 'name');

        // TODO: add approved button and process.
    }
}
