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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class to manage cookies.
 * Require the response object for manage (update/clear) the cookies.
 * Require the request object for get the cookies.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class AppCookies
{

    /**
     * Clear all cookies.
     *
     * @param Response $response
     * @return void
     */
    public static function clear(Response $response): void
    {
        $response->headers->clearCookie('biblioUserName', APP_ROUTE);
        $response->headers->clearCookie('biblioLogkey', APP_ROUTE);
    }

    /**
     * Get the logkey from cookies.
     *
     * @param Request $request
     * @return string
     */
    public static function getLogkey(Request $request): string
    {
        return $request->cookies->get('biblioLogkey', '');
    }

    /**
     * Get the username from cookies.
     *
     * @param Request $request
     * @return string
     */
    public static function getUser(Request $request): string
    {
        return $request->cookies->get('biblioUserName', '');
    }

    /**
     * Set the cookies from the user data.
     *
     * @param Response $response
     * @param User $user
     * @return void
     */
    public static function update(Response $response, User $user): void
    {
        $expire = time() + APP_COOKIES_EXPIRE;
        $response->headers->setCookie(new Cookie('biblioUserName', $user->username, $expire, APP_ROUTE, null, false, false, false, null));
        $response->headers->setCookie(new Cookie('biblioLogkey', $user->logkey, $expire, APP_ROUTE, null, false, false, false, null));
    }
}