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
 * Class to manage the user data.
 *
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
    public string $logkey;

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
        $this->logkey = '';
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
            $this->message->warning('No se puede eliminar el usuario administrador.');
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
        $this->email = $data['email'] ?? '';
        $this->enabled = (bool)$data['enabled'] ?? false;
        $this->logkey = $data['logkey'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->username = $data['username'] ?? '';
    }

    /**
     * Set new logkey to user.
     *
     * @return void
     */
    public function newLogkey()
    {
        $this->logkey = Tools::randomString(99);
        $this->save();
    }

    /**
     * Returns the name of the column that is the model's primary key.
     *
     * @return string
     */
    public static function primaryColumn(): string
    {
        return 'username';
    }

    /**
     * Returns the name of the table that uses this model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
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

        return $this->testPassword() && parent::test();
    }

    /**
     * Asigns the new password to the user.
     *
     * @param string $value
     */
    public function setPassword($value)
    {
        $this->password = password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * Verifies password. It also rehashes the password if needed.
     *
     * @param string $value
     * @return bool
     */
    public function verifyPassword($value)
    {
        $paso = password_hash($value, PASSWORD_DEFAULT);
        if (password_verify($value, $this->password)) {
            if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
                $this->setPassword($value);
            }

            return true;
        }

        return false;
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
     * Check if user have been change the password.
     * If so, it checks that the two passwords are the same and updates the password.
     *
     * @return bool
     */
    protected function testPassword(): bool
    {
        if (false === empty($this->newPassword) && false === empty($this->newPassword2)) {
            if ($this->newPassword !== $this->newPassword2) {
                $this->message->warning('La nueva contraseña no coincide con su comprobación.');
                return false;
            }

            $this->setPassword($this->newPassword);
        }

        if (empty($this->password)) {
            $this->message->warning('La contraseña no puede estar vacía.');
            return false;
        }

        return true;
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
                . 'email = ' . self::$dataBase->var2str($this->email) . ','
                . 'enabled = ' . self::$dataBase->var2str($this->enabled) . ','
                . 'logkey = ' . self::$dataBase->var2str($this->logkey) . ','
                . 'name = ' . self::$dataBase->var2str($this->name) . ','
                . 'password = ' . self::$dataBase->var2str($this->password)
            . ' WHERE username = ' . self::$dataBase->var2str($this->username);
        return self::$dataBase->exec($sql);
    }
}
