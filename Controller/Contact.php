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
use BiblioApp\Model\WebContact;
use Symfony\Component\HttpFoundation\Response;

class Contact extends FrontPageController
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

    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Contactar con Nosotros';
        $data['breadcrumb'] = 'Contactar';
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
        if ($action == 'contact') {
            if ($this->contactAction()) {
                $this->message->info('Contacto realizado correctamente');
            } else {
                $this->message->error('¡Error! Revise los datos introducidos');
            }
        }

        return true;
    }

    private function contactAction(): bool
    {
        $data = $this->request->request->all();
        $contact = new WebContact();
        $contact->email = $data['email'] ?? '';
        $contact->name = $data['name'] ?? '';
        $contact->notes = $data['notes'] ?? '';
        $contact->phone = $data['phone'] ?? '';
        if (false === $contact->save()) {
            $this->formData = [
                'email' => $data['email'] ?? '',
                'name' => $data['name'] ?? '',
                'notes' => $data['notes'] ?? '',
                'phone' => $data['phone'] ?? '',
            ];
            return false;
        }
        return true;
    }
}