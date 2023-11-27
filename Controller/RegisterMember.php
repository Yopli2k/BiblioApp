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
use BiblioApp\Model\Member;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller to manage a new member form.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class RegisterMember extends FrontPageController
{

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
     *
     * @param Response $response
     * @return bool
     */
    public function exec(Response &$response): bool
    {
        if (false === parent::exec($response)) {
            return false;
        }

        $action = $this->request->request->get('action', $this->request->query->get('action', ''));
        if (false === $this->execPreviousAction($action)) {
            $this->setTemplate(false);
            return false;
        }
        return true;
    }

    /**
     * Return the basic data for this page.
     *
     * @return array
     */
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Registrar Nuevo Miembro';
        $data['breadcrumb'] = 'Registrar';
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
        if ($action == 'new-member') {
            $data = $this->request->request->all();
            if ($this->newMemberAction($data)) {
                $this->message->info('Contacto realizado correctamente');
            } else {
                $this->formData = [
                    'address' => $data['address'] ?? '',
                    'document' => $data['document'] ?? '',
                    'email' => $data['email'] ?? '',
                    'name' => $data['name'] ?? '',
                    'phone' => $data['phone'] ?? '',
                ];
            }
        }

        return true;
    }

    /**
     * Check if the password is correct.
     *
     * @param Member $member
     * @param array $data
     * @return bool
     */
    private function checkPassword(Member $member, array $data): bool
    {
        $member->newPassword = $data['password'] ?? '';
        $member->newPassword2 = $data['confirm'] ?? '';
        return $member->testPassword();
    }

    /**
     * Check if exists a member with the value of the indicate field.
     *
     * @param string $field
     * @param string $value
     * @return bool
     */
    private function checkMemberExists(string $field, string $value): bool
    {
        $where = [ new DataBaseWhere($field, $value) ];
        $member = new Member();
        return $member->loadFromCode('', $where);
    }

    /**
     * Create a new member.
     *   - check if the password is correct.
     *   - check if the email is already in use.
     *   - check if the document is already in use.
     *
     * @param array $data
     * @return bool
     */
    private function newMemberAction(array $data): bool
    {
        $member = new Member();
        if (false === $this->checkPassword($member, $data)) {
            return false;
        }

        if ($this->checkMemberExists('email', $data['email'] ?? '')) {
            $this->message->warning('¡Error! Ya existe un miembro con ese email');
            return false;
        }

        if ($this->checkMemberExists('document', $data['document'] ?? '')) {
            $this->message->warning('¡Error! Ya existe un miembro con ese documento');
            return false;
        }

        $member->address = $data['address'] ?? '';
        $member->document = $data['document'] ?? '';
        $member->email = $data['email'] ?? '';
        $member->name = $data['name'] ?? '';
        $member->phone = $data['phone'] ?? '';
        return $member->save();
    }
}