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

use BiblioApp\Core\App\PageController;
use BiblioApp\Core\DataBase\DataBaseWhere;
use BiblioApp\Model\Book;
use BiblioApp\Model\Category;

class Home extends PageController
{

    public function getCategories(): array
    {
        $categories = new Category();
        return $categories->select([]);
    }

    /**
     * Return the basic data for this page.
     *
     * @return array
     */
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Home Page';
        return $data;
    }

    /**
     * Returns a list of books marked as recommended
     *
     * @return Book[]
     */
    public function getRecommendBooks(): array
    {
        $books = new Book();
        $where = [ new DataBaseWhere('recommended', true) ];
        return $books->select($where, [], 0, 4);
    }

    /**
     * Get the average rating of the book.
     * For performance reasons, it is only calculated once, when not has value.
     *
     * @return int
     */
    public function getRating(Book $book): int
    {
        return $book->getRating();
    }

    public function getUrlBookImage(Book $book): string
    {
        $bookImage = $book->getImage();
        $url = $bookImage->getFullPath();
        return (file_exists($url)) ? $url : '';
    }
}