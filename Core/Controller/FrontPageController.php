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
namespace BiblioApp\Core\Controller;

use BiblioApp\Core\App\AppCookies;
use BiblioApp\Model\Member;
use Symfony\Component\HttpFoundation\Response;

/**
 * Page controller base for all frontend pages.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
abstract class FrontPageController extends PageController
{
    /**
     * The member logged in into frontend.
     *
     * @var ?Member $member
     */
    public ?Member $member;

    /**
     * Initialize all objects and properties.
     *
     * @param string $className
     * @param string $uri
     */
    public function __construct(string $className, string $uri = '')
    {
        parent::__construct($className, $uri);
        $this->member = null;
    }

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

        $this->member = $this->cookieAuth();
        if (false === empty($this->member)) {
            $this->multiRequestProtection->addSeed($this->member->primaryColumnValue());
        }
        return true;
    }

    /**
     * Authenticate the member using the cookie.
     */
    private function cookieAuth(): ?Member
    {
        // Check for logout action before loading member
        $action = $this->request->request->get('action', $this->request->query->get('action', ''));
        if ($action === 'logout') {
            AppCookies::clearCookie($this->response, 'biblioMemberID');
            AppCookies::clearCookie($this->response, 'biblioMemberLogKey');
            return null;
        }

        $memberId = AppCookies::getCookie($this->request, 'biblioMemberID');
        $logKey = AppCookies::getCookie($this->request, 'biblioMemberLogKey');
        if (empty($memberId) || empty($logKey)) {
            return null;
        }

        $member = new Member();
        if ($member->loadFromCode($memberId)
            && $member->enabled
            && $member->logkey === $logKey
        ) {
            AppCookies::setCookie($this->response, 'biblioMemberID', $member->primaryColumnValue());
            AppCookies::setCookie($this->response, 'biblioMemberLogKey', $member->logkey);
            return $member;
        }

        AppCookies::clearCookie($this->response, 'biblioMemberID');
        AppCookies::clearCookie($this->response, 'biblioMemberLogKey');
        return null;
    }
}