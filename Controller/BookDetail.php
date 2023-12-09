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
use BiblioApp\Model\Loan;
use BiblioApp\Model\Rating;
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
     * The received data from the form.
     * if the form is saved, the array is empty.
     *
     * @var array
     */
    public array $formData = [];

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
            $this->redirect('/index.php');
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

    /**
     * Indicates if the book has a loan.
     *
     * @param Book $book
     * @return bool
     */
    public function hasLoan(Book $book): bool
    {
        return $book->hasLoan() || $this->member->hasLoan();
    }

    /**
     * Return the basic data for this page.
     *
     * @return array
     */
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
        switch ($action) {
            case 'comment':
                if ($this->newCommentAction()) {
                    $this->message->info('El comentario se ha guardado correctamente.');
                }
                break;

            case 'new-loan':
                if ($this->newLoanAction()) {
                    $this->message->info('El libro se ha reservado correctamente.');
                }
                break;
        }
        return true;
    }

    private function newCommentAction(): bool
    {
        $data = $this->request->request->all();
        if (false === $this->checkDataForm($data)) {
            return false;
        }

        $email = $data['email'] ?? '';
        if ($email !== $this->member->email) {
            $this->message->warning('El email no coincide con el del socio.');
            return false;
        }

        $rating = new Rating();
        $rating->member_id = $this->member->id;
        $rating->book_id = $this->book->id;
        $rating->email = $this->member->email;
        $rating->rating = (int)$data['rating'] ?? 5;
        $rating->valoration = $data['notes'] ?? '';
        if ($rating->save()) {
            return true;
        }

        $this->formData = [
            'email' => $data['email'] ?? '',
            'notes' => $data['notes'] ?? '',
        ];
        return false;
    }

    /**
     * Check if the data post is correct.
     *
     * @param array $data
     * @return bool
     */
    private function checkDataForm(array $data): bool
    {
        $member_id = (int)$data['member_id'] ?? 0;
        $book_id = (int)$data['book_id'] ?? 0;
        return false === empty($member_id)
            && false === empty($book_id)
            && $book_id === $this->book->id
            && $member_id === $this->member->id;
    }

    /**
     * Create a new loan for the book and member.
     * Can't loan a book if
     *   -- the book is already loaned
     *   -- the member has a loan without return
     *
     * @return bool
     */
    private function newLoanAction(): bool
    {
        $data = $this->request->request->all();
        if (false === $this->checkDataForm($data)) {
            return false;
        }

        if ($this->book->hasLoan()) {
            $this->message->warning('El libro ya está reservado. No se puede volver a reservar.');
            return false;
        }

        if ($this->member->hasLoan()) {
            $this->message->warning('El socio ya tiene un libro reservado. No se puede volver a reservar.');
            return false;
        }

        $loan = new Loan();
        $loan->member_id = $this->member->id;
        $loan->book_id = $this->book->id;
        return $loan->save();
    }
}