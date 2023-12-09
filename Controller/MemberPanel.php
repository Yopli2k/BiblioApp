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
use Symfony\Component\HttpFoundation\Response;

class MemberPanel extends FrontPageController
{

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
     * Return the basic data for this page.
     *
     * @return array
     */
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Mi Información';
        $data['breadcrumb'] = 'Perfil';
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
            case 'update':
                if ($this->updateAction()) {
                    $this->message->info('Información actualizada correctamente.');
                } else {
                    $this->message->error('No se ha podido actualizar los datos.');
                }
                break;

            case 'change-password':
                if ($this->changePasswordAction()) {
                    $this->message->info('Contraseña actualizada correctamente.');
                }
                break;
        }
        return true;
    }

    /**
     * Change member password.
     *
     * @return bool
     */
    private function changePasswordAction(): bool
    {
        $data = $this->request->request->all();
        if (empty($data['password'])
            || empty($data['new_password'])
            || empty($data['confirm'])
        ) {
            $this->message->warning('Debe completar todos los campos.');
            return false;
        }

        if (false === $this->member->verifyPassword($data['password'])) {
            $this->message->warning('La contraseña actual no es correcta.');
            return false;
        }

        $this->member->newPassword = $data['new_password'];
        $this->member->newPassword2 = $data['confirm'];
        return $this->member->testPassword() && $this->member->save();
    }

    /**
     * Update member data.
     *
     * @return bool
     */
    private function updateAction(): bool
    {
        $data = $this->request->request->all();

        if (false === $this->member->checkNewEmail($data['email'])) {
            $this->message->warning('El correo electrónico ya está en uso.');
            return false;
        }

        $this->member->address = $data['address'];
        $this->member->document = $data['document'];
        $this->member->email = $data['email'];
        $this->member->name = $data['name'];
        $this->member->phone = $data['phone'];
        return $this->member->save();
    }
}