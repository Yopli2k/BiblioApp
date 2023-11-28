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
use BiblioApp\Core\DataBase\DataBaseWhere;
use BiblioApp\Core\Tools\Tools;
use BiblioApp\Model\Book;
use BiblioApp\Model\BookCategory;
use BiblioApp\Model\Category;
use BiblioApp\Controller\Base\BookTrait;
use Symfony\Component\HttpFoundation\Response;

class Home extends FrontPageController
{

    private const ORDERBY_NEW = 'Novedad';
    private const ORDERBY_AUTHOR = 'Autor';

    /**
     * the total number of books (filtered or not)
     *
     * @var int
     */
    public int $bookCount = 0;

    /**
     * Set the limit x page for the book list
     *
     * @var int
     */
    public int $limit = 12;

    /**
     * Set the filter category for the book list
     *
     * @var array
     */
    public array $filterCategory = [];

    /**
     * Set the order by for the book list
     *
     * @var string
     */
    public string $filterOrderBy = self::ORDERBY_NEW;

    /**
     * Set the offset for the book list (pagination)
     *
     * @var int
     */
    public int $offset = 0;

    use BookTrait;

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

        // Get the filter category and offset, if exists
        $this->filterCategory = $this->request->request->get('category', []);
        $this->filterOrderBy = $this->request->request->get('orderby', self::ORDERBY_NEW);
        $this->offset = $this->request->request->get('offset', $this->request->query->get('offset', 0));

        // Get action and execute if not empty
        $action = $this->request->request->get('action', $this->request->query->get('action', ''));
        if (false === $this->execPreviousAction($action)) {
            $this->setTemplate(false);
            return false;
        }
        return true;
    }

    /**
     * Return a list of all or filter books.
     *
     * @return Book[]
     */
    public function getBookList(): array
    {
        $where = [];
        if (false === empty($this->filterCategory)){
            $book_ids = $this->getBookIds();
            $where = [ new DataBaseWhere('id', implode(',', $book_ids), 'IN') ];
        }

        $orderBy = $this->filterOrderBy === self::ORDERBY_AUTHOR
            ? ['author' => 'ASC']
            : ['id' => 'DESC'];

        $books = new Book();
        $this->bookCount = $books->count($where);
        return $this->bookCount > 0
            ? $books->select($where, $orderBy, $this->offset, $this->limit)
            : [];
    }

    /**
     * Return a list of all categories.
     *
     * @return array
     */
    public function getCategories(): array
    {
        $categories = new Category();
        return $categories->select([]);
    }

    /**
     * Return a list of all order by.
     *
     * @return array
     */
    public function getOrderByList(): array
    {
        return [
            self::ORDERBY_NEW => 'Novedad',
            self::ORDERBY_AUTHOR => 'Autor'
        ];
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

    public function getPagination(): array
    {
        return Tools::getPagination($this->bookCount, $this->offset, $this->limit);
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
     * Run the actions that alter data before reading it.
     *
     * @param ?string $action
     * @return bool
     */
    protected function execPreviousAction(?string $action): bool
    {
        return true;
    }

    /**
     * Return a list of book ids filtered by category
     *
     * @return array
     */
    private function getBookIds(): array
    {
        $category_ids = implode(',', $this->filterCategory);
        $sql = 'SELECT book_id, GROUP_CONCAT(category_id) as categories FROM books_categories'
            . ' WHERE category_id IN (' . $category_ids . ')'
            . ' GROUP BY book_id';

        $bookIds = [ 0 ];
        foreach ($this->dataBase->select($sql) as $row) {
            $categories = explode(',', $row['categories']);
            if (count($categories) === count($this->filterCategory)) {
                $bookIds[] = $row['book_id'];
            }
        }

        return $bookIds;
    }
}