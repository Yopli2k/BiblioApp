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

use BiblioApp\Core\ExtendedController\ListController;

class ListMember extends ListController
{

    /**
     * Return the basic data for this page.
     *
     * @return array
     */
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Asociados';
        $data['icon'] = 'fa fa-address-book';
        return $data;
    }

    protected function createViews(): void
    {
        $this->createViewsMembers();
        $this->createViewsLoans();
    }

    private function createViewsLoans(string $viewName = 'ListLoan'): void
    {
        $this->addView($viewName, 'Loan', 'Préstamos', 'fa-solid fa-book-open-reader');
        $this->addOrderBy($viewName, ['loan_date'], 'Préstamo');
        $this->addOrderBy($viewName, ['return_date'], 'Devolución');
        $this->addFilterPeriod($viewName, 'loan_date', 'Préstamo', 'loan_date');
        $this->addFilterAutocomplete($viewName, 'book', 'Libro', 'book_id', 'books', 'id', 'name');
        $this->addFilterAutocomplete($viewName, 'member', 'Asociado', 'member_id', 'members', 'id', 'name');
    }

    /**
     * @param string $viewName
     */
    private function createViewsMembers(string $viewName = 'ListMember'): void
    {
        $this->addView($viewName, 'Member', 'Asociados', 'fa fa-address-book');
        $this->addSearchFields($viewName, ['name', 'phone']);
        $this->addOrderBy($viewName, ['name'], 'Nombre');
        $this->addOrderBy($viewName, ['phone'], 'Teléfono');
    }
}