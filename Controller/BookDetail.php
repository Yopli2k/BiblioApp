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

use BiblioApp\Core\Controller\FrontPageController;
use BiblioApp\Controller\Base\BookTrait;
use BiblioApp\Model\Book;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller to show a book record data.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class BookDetail extends FrontPageController
{
    use BookTrait;

    /**
     * The book to show.
     *
     * @var Book
     */
    public Book $book;

    /**
     * Runs the controller's logic.
     * if return false, the controller break the execution.
     * if member is not logged in, redirect to home page.
     *
     * @param Response $response
     * @return bool
     */
    public function exec(Response &$response): bool
    {
        if (false === parent::exec($response)) {
            return false;
        }

        $this->book = new Book();
        $book_id = $this->request->query->get('code', '');
        if (empty($book_id) || false === $this->book->loadFromCode($book_id)) {
            $this->redirect('');
            $this->setTemplate(false);
            return false;
        }

        // Get action and execute if not empty
        $action = $this->request->request->get('action', $this->request->query->get('action', ''));
        if (false === $this->execPreviousAction($action)) {
            $this->setTemplate(false);
            return false;
        }
        return true;
    }

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Detalle del libro';
        $data['breadcrumb'] = 'Libro';
        return $data;
    }

    /**
     * Run the actions that alter data before reading it.
     *
     * @param ?string $action
     * @return bool
     */
    protected function execPreviousAction(?string $action): bool
    {
        return true;
    }
}