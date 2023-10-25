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

class ListBook extends ListController
{

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Libros';
        $data['icon'] = 'fa-solid fa-book-bookmark';
        return $data;
    }

    protected function createViews(): void
    {
        $this->createViewsBooks();
        $this->createViewsCategories();
        $this->createViewsRatings();
    }

    /**
     * @param string $viewName
     */
    private function createViewsBooks(string $viewName = 'ListBook'): void
    {
        $this->addView($viewName, 'Book', 'Libros', 'fa-solid fa-book-bookmark');
        $this->addSearchFields($viewName, ['title', 'synopsis', 'author', 'isbn']);
        $this->addOrderBy($viewName, ['name'], 'Título');
        $this->addOrderBy($viewName, ['isbn'], 'ISBN');
    }

    /**
     * @param string $viewName
     */
    private function createViewsCategories(string $viewName = 'ListCategory'): void
    {
        $this->addView($viewName, 'Category', 'Categorías', 'fa-solid fa-object-group');
        $this->addSearchFields($viewName, ['name']);
        $this->addOrderBy($viewName, ['name'], 'Nombre');
    }

    private function createViewsRatings(string $viewName = 'ListRating'): void
    {
        $this->addView($viewName, 'Rating', 'Opiniones', 'fa-regular fa-comments');
        $this->addSearchFields($viewName, ['valoration']);
        $this->addOrderBy($viewName, ['rating_date', 'rating_time', 'id'], 'Fecha', 2);
        $this->addOrderBy($viewName, ['rating', 'id'], 'Valoración');
    }
}