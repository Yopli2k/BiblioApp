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
namespace BiblioApp\Controller\Base;

use BiblioApp\Model\Book;

/**
 * Trait BookTrait
 * This trait contains methods for use in the controllers that need to work with books.
 *
 * @author José Antonio Cuello Principal
 */
trait BookTrait
{
    /**
     * Return the categories of the book.
     *
     * @param Book $book
     * @return array
     */
    public function getCategories(Book $book): array
    {
        return $book->getCategories();
    }

    /**
     * Return the valorations of the book.
     *
     * @param Book $book
     * @return array
     */
    public function getValorations(Book $book): array
    {
        $result = [];
        foreach ($book->getValorations() as $rating) {
            $result[] = [
                'rating' => $rating,
                'member' => $rating->getMember(),
            ];
        }
        return $result;
    }

    /**
     * Get the average rating of the book.
     * For performance reasons, it is only calculated once, when not has value.
     *
     * @param Book $book
     * @return int
     */
    public function getRating(Book $book): int
    {
        return $book->getRating();
    }

    /**
     * Get the url for display the book image.
     *
     * @param Book $book
     * @return string
     */
    public function getUrlBookImage(Book $book): string
    {
        $bookImage = $book->getImage();
        $url = $bookImage->getFullPath();
        return (file_exists($url)) ? $url : '';
    }
}