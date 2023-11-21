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

use BiblioApp\Controller\Base\User;
use BiblioApp\Core\App\AppCookies;
use BiblioApp\Model\Member;

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
     * @var Member $member
     */
    public Member $member;

    /**
     * Initialize all objects and properties.
     *
     * @param string $className
     * @param string $uri
     */
    public function __construct(string $className, string $uri = '')
    {
        parent::__construct($className, $uri);
        $this->member = new Member();
    }
}