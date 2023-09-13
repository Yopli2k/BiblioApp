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
namespace BiblioApp\Model;

use BiblioApp\Core\App\AppModel;
use BiblioApp\Core\Tools\Tools;

/**
 * @author Carlos García Gómez <carlos@facturascripts.com>
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class User extends AppModel
{

    /** @var string */
    public string $email;

    /** @var bool */
    public bool $enabled;

    /** @var string */
    public string $name;

    /**
     * Primary Key.
     *
     * @var string
     */
    public string $username;

    /**
     * Password hashed with password_hash()
     *
     * @var string
     */
    public string $password;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->email = '';
        $this->enabled = true;
        $this->name = '';
        $this->password = '';
        $this->username = '';
    }

    /**
     * Remove the model data from the database.
     * Can not delete the admin user.
     *
     * @return bool
     */
    public function delete(): bool
    {
        if ($this->username === 'admin') {
            return false;
        }
        return parent::delete();
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->email = $data['email'] ?? null;
        $this->enabled = (bool)$data['enabled'] ?? false;
        $this->name = $data['name'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->username = $data['username'] ?? null;
    }

    public static function primaryColumn(): string
    {
        return 'username';
    }

    public static function tableName(): string
    {
        return 'users';
    }

    public function test(): bool
    {
        $this->username = trim($this->username);
        if (1 !== preg_match("/^[A-Z0-9_@\+\.\-]{3,50}$/i", $this->username)) {
            return false;
        }

        $this->email = Tools::noHtml(mb_strtolower($this->email ?? '', 'UTF8'));
        if (false === empty($this->email) && false === filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return parent::test();
    }

    /**
     * Insert the model data in the database.
     *
     * @return bool
     */
    protected function insert(): bool
    {
        $sql = 'INSERT INTO ' . static::tableName()
            . ' (email, enabled, name, password, username)'
            . ' VALUES ('
            . self::$dataBase->var2str($this->email) . ','
            . self::$dataBase->var2str($this->enabled) . ','
            . self::$dataBase->var2str($this->name) . ','
            . self::$dataBase->var2str($this->password) . ','
            . self::$dataBase->var2str($this->username)
            . ')';
        return self::$dataBase->exec($sql);
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . 'SET '
                . 'email = ' . self::$dataBase->var2str($this->email) . ','
                . 'enabled = ' . self::$dataBase->var2str($this->enabled) . ','
                . 'name = ' . self::$dataBase->var2str($this->name) . ','
                . 'password = ' . self::$dataBase->var2str($this->password)
            . ' WHERE username = ' . self::$dataBase->var2str($this->username);
        return self::$dataBase->exec($sql);
    }
}
