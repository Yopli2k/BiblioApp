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
use BiblioApp\Core\DataBase\DataBaseWhere;
use BiblioApp\Core\Tools\PhoneTools;
use BiblioApp\Core\Tools\Tools;
use BiblioApp\Model\Member;

/**
 * Class to manage the contacts made through the web.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class WebContact extends AppModel
{

    /**
     * Date of the contact.
     *
     * @var string
     */
    public string $creationdate;

    /**
     * Time of the contact.
     *
     * @var string
     */
    public string $creationtime;

    /**
     * The email address of the contact.
     *
     * @var string
     */
    public string $email;

    /**
     * Primary Key.
     *
     * @var int|null
     */
    public ?int $id;

    /**
     * Link to member model.
     *
     * @var int|null
     */
    public ?int $member_id;

    /**
     * The name of the person who made the contact.
     *
     * @var string
     */
    public string $name;

    /**
     * Contact message from the web.
     *
     * @var string
     */
    public string $notes;

    /**
     * Phone number for contact.
     *
     * @var string
     */
    public string $phone;

    /**
     * Indicates if the contact is resolved.
     *
     * @var bool
     */
    public bool $resolved;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->id = null;
        $this->member_id = null;
        $this->creationdate = date('Y-m-d');
        $this->creationtime = date('H:i:s');
        $this->email = '';
        $this->name = '';
        $this->notes = '';
        $this->phone = '';
        $this->resolved = false;
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->id = (int)$data['id'] ?? null;
        $this->member_id = (int)$data['member_id'] ?? null;
        $this->creationdate = $data['creationdate'] ?? date('Y-m-d');
        $this->creationtime = $data['creationtime'] ?? date('H:i:s');
        $this->email = $data['email'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->notes = $data['notes'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->resolved = (bool)$data['resolved'] ?? false;
    }

    /**
     * Returns the name of the column that is the model's primary key.
     *
     * @return string
     */
    public static function primaryColumn(): string
    {
        return 'id';
    }

    /**
     * Returns the name of the table that uses this model.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'webcontacts';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
    public function test(): bool
    {
        $this->name = Tools::noHtml($this->name);
        $this->notes = Tools::noHtml($this->notes);

        $this->email = Tools::noHtml(mb_strtolower($this->email ?? '', 'UTF8'));
        if (false === empty($this->email) && false === filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->message->warning('El email no es válido.');
            return false;
        }

        $this->phone = Tools::noHtml($this->phone ?? '');
        if (false === empty($this->phone) && false === PhoneTools::isPhone($this->phone)) {
            $this->message->warning('El teléfono no es válido.');
            return false;
        }

        if (empty($this->member_id)) {
            $this->setMemberId();
        }

        return parent::test();
    }

    /**
     * Returns the url where to see / modify the data.
     *
     * @param string $type
     * @param string $list
     * @return string
     */
    public function url(string $type = 'auto', string $list = 'List'): string
    {
        return parent::url($type, 'ListUser?activetab=' . $list);
    }

    /**
     * Insert the model data in the database.
     *
     * @return bool
     */
    protected function insert(): bool
    {
        $member_id = empty($this->member_id) ? 'NULL' : $this->member_id;
        $sql = 'INSERT INTO ' . static::tableName()
            . ' (member_id, creationdate, creationtime, email, name, notes, phone, resolved)'
            . ' VALUES ('
            . $member_id . ','
            . self::$dataBase->var2str($this->creationdate) . ','
            . self::$dataBase->var2str($this->creationtime) . ','
            . self::$dataBase->var2str($this->email) . ','
            . self::$dataBase->var2str($this->name) . ','
            . self::$dataBase->var2str($this->notes) . ','
            . self::$dataBase->var2str($this->phone) . ','
            . self::$dataBase->var2str($this->resolved)
            . ')';
        return self::$dataBase->exec($sql);
    }

    /**
     * Returns the list of fields that are required.
     *
     * @return string[]
     */
    protected function requiredFields(): array
    {
        return ['creationdate', 'creationtime', 'email', 'name', 'notes', 'phone'];
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $member_id = empty($this->member_id) ? 'NULL' : $this->member_id;
        $sql = 'UPDATE ' . static::tableName() . ' SET '
            . 'member_id = ' . $member_id . ','
            . 'email = ' . self::$dataBase->var2str($this->email) . ','
            . 'name = ' . self::$dataBase->var2str($this->name) . ','
            . 'notes = ' . self::$dataBase->var2str($this->notes) . ','
            . 'phone = ' . self::$dataBase->var2str($this->phone) . ','
            . 'resolved = ' . self::$dataBase->var2str($this->resolved)
            . ' WHERE id = ' . self::$dataBase->var2str($this->id);
        return self::$dataBase->exec($sql);
    }

    /**
     * Set the member id from the email.
     */
    private function setMemberId(): void
    {
        if (empty($this->email)) {
            return;
        }

        $where = [ new DataBaseWhere('email', $this->email) ];
        $member = new Member();
        if ($member->loadFromCode('', $where)) {
            $this->member_id = $member->id;
        }
    }
}
