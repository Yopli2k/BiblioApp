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

use BiblioApp\Controller\Base\BookTrait;
use BiblioApp\Core\Controller\FrontPageController;
use BiblioApp\Core\DataBase\DataBaseWhere;
use BiblioApp\Model\Loan;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for list the loans of the current member.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class MemberLoan extends FrontPageController
{
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

        if (false === isset($this->member) || empty($this->member->id)) {
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

    /**
     * Return a list of loans and his book data for the current member.
     * For each return item into array:
     *   - loan: Loan model object
     *   - book: Book model object
     *
     * @return array
     */
    public function getLoans(): array
    {
        $result = [];
        $where = [ new DataBaseWhere('member_id', $this->member->id) ];
        $order = [ 'loan_date' => 'DESC' ];
        $loanList = new Loan();
        foreach ($loanList->select($where, $order) as $loan) {
            $result[] = [
                'loan' => $loan,
                'book' => $loan->getBook(),
            ];
        }
        return $result;
    }

    /**
     * Return the basic data for this page.
     *
     * @return array
     */
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Mis Reservas';
        $data['breadcrumb'] = 'Reservas';
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
        if ($action == 'delete-loan') {
            if ($this->deleteLoanAction()) {
                $this->message->info('Reserva eliminada correctamente.');
                return true;
            }
            $this->message->error('No se ha podido actualizar los datos.');
        }
        return true;
    }

    /**
     * Delete a loan for login member.
     *
     * @return bool
     */
    private function deleteLoanAction(): bool
    {
        $data = $this->request->request->all();
        $member_id = (int)$data['member_id'] ?? 0;
        $book_id = (int)$data['book_id'] ?? 0;
        $loan_id = (int)$data['loan_id'] ?? 0;
        if (empty($loan_id)
            || empty($book_id)
            || empty($member_id)
            || $member_id !== $this->member->id
        ) {
            return false;
        }

        // for security, check for all fields (id + member + book ).
        $where = [
            new DataBaseWhere('id', $loan_id),
            new DataBaseWhere('book_id', $book_id),
            new DataBaseWhere('member_id', $member_id),
            new DataBaseWhere('return_date', null, 'IS'),
        ];

        $loan = new Loan();
        if ($loan->loadFromCode('', $where)) {
            return $loan->delete();
        }
        return false;
    }
}
