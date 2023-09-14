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
namespace BiblioApp\Core\App;

use BiblioApp\Model\User;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class to manage selected controller.
 *
 * @author Carlos García Gómez <carlos@facturascripts.com>
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class AppController extends AppBase
{
    /**
     * Controller loaded
     *
     * @var PageController
     */
    private PageController $controller;

    /**
     * Contains the page name.
     *
     * @var string
     */
     private string $pageName;

    /**
     * @var ?User
     */
    private ?User $user;

    /**
     * Initializes the app.
     *
     * @param string $uri
     * @param string $pageName
     */
    public function __construct(string $uri = '/', string $pageName = '')
    {
        parent::__construct($uri);
        $this->pageName = $pageName;
    }

    /**
     * Select and run the corresponding controller.
     *
     * @return bool
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function run(): bool
    {
        if (false === parent::run()) {
            return false;
        }

        if ($this->request->query->get('logout')) {
            AppCookies::clear($this->response);
            $this->response->headers->set('Refresh', '0; ' . 'index.php/Login');
            return false;
        }

        $userName = $this->request->request->get('biblioUserName', '');
        $this->user = empty($userName)
            ? $this->cookieAuth()
            : $this->userAuth($userName);

        $pageName = $this->getPageName();
        $this->loadController($pageName);
        return true;
    }

    /**
     * Finalize the app.
     *
     * @param int $status
     * @param string $message
     */
    protected function die(int $status, string $message = ''): void
    {
        $this->response->setContent(nl2br($message));
        $this->response->setStatusCode($status);
    }

    /**
     * Load and process the $pageName controller.
     *
     * @param string $pageName
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function loadController(string $pageName): void
    {
        $controllerName = $this->getControllerFullName($pageName);
        if (false === class_exists($controllerName)) {
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
            $this->renderHtml('Error/ControllerNotFound.html.twig', $controllerName);
            return;
        }

        $this->controller = new $controllerName($pageName, $this->uri);
        $this->controller->exec($this->response);
        $template = $this->controller->getTemplate();
        $this->renderHtml($template, $controllerName);
    }

    /**
     * Creates HTML with the selected template. The data will not be inserted in it
     * until render() is executed
     *
     * @param string $template
     * @param string $controllerName
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function renderHtml(string $template, string $controllerName = ''): void
    {
        // HTML template variables
        $templateVars = [
            'controllerName' => $controllerName,
            'controller' => $this->controller ?? null,
            'template' => $template
        ];

        try {
            $this->response->setContent(Html::render($template, $templateVars));
        } catch (Exception $exc) {
            // ToolBox::log()->critical($exc->getMessage());
            $this->response->setContent(Html::render('Error/TemplateError.html.twig', $templateVars));
            $this->response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Authenticate the user using the cookie.
     *
     * @param User $user
     * @return ?User
     */
    private function cookieAuth(): ?User
    {
        $userName = AppCookies::getUser($this->request);
        if (empty($userName)) {
            return null;
        }

        $user = new User();
        if ($user->loadFromCode($userName)
            && $user->enabled
            && $user->logkey === AppCookies::getLogkey($this->request)
        ) {
            AppCookies::update($this->response, $user);
            return $user;
        }

        AppCookies::clear($this->response);
        return null;
    }

    /**
     * Returns the controllers full name
     *
     * @param string $pageName
     * @return string
     */
    private function getControllerFullName(string $pageName): string
    {
        return '\\' . APP_NAME . '\\Controller\\' . $pageName;
    }

    /**
     * Returns the name of the default controller for the current user or for all users.
     *
     * @return string
     */
    private function getPageName(): string
    {
        if (false === empty($this->pageName)) {
            return $this->pageName;
        }

        if (false === empty($this->getUriParam(0)) && $this->getUriParam(0) !== 'index.php') {
            return $this->getUriParam(0);
        }

        return isset($this->user) ? 'Main' : 'Home';
    }

    /**
     * User authentication from login form.
     * Returns the user when successful auth, or null when not.
     *
     * @param string $userName
     * @return ?User
     */
    private function userAuth(string $userName): ?User
    {
        $user = new User();
        if ($user->loadFromCode($userName)
            && $user->enabled
            && $user->verifyPassword($this->request->request->get('biblioPassword'))
        ) {
            $user->newLogkey();
            AppCookies::update($this->response, $user);
            return $user;
        }

        AppCookies::clear($this->response);
        return null;
    }
}
