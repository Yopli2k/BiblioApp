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
use BiblioApp\Core\Tools\DateTools;

/**
 * Class to manage the loan of the book data.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class Loan extends AppModel
{

    /**
     * Link to the book model.
     *
     * @var int|null
     */
    public ?int $book_id;

    /**
     * Primary Key.
     *
     * @var int|null
     */
    public ?int $id;

    /**
     * Date of the loan.
     *
     * @var string
     */
    public string $loan_date;

    /**
     * Link to member model.
     *
     * @var int|null
     */
    public ?int $member_id;

    /**
     * Date of the return of the loan book.
     *
     * @var string|null
     */
    public ?string $return_date;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->book_id = null;
        $this->id = null;
        $this->member_id = null;
        $this->loan_date = date('Y-m-d');
        $this->return_date = null;
    }

    /**
     * Indicates if the book is loaned.
     * If the idbook parameter is empty, it checks if the current book is loaned.
     *
     * @param int $idbook
     * @return bool
     */
    public function isLoan(int $idbook = 0): bool
    {
        if (empty($idbook)) {
            return empty($this->return_date);
        }

        $where = [
            new DataBaseWhere('book_id', $idbook),
            new DataBaseWhere('return_date', null, 'IS')
        ];
        $loan = new Loan();
        return $loan->loadFromCode('', $where);
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->book_id = (int)$data['book_id'] ?? null;
        $this->id = (int)$data['id'] ?? null;
        $this->member_id = (int)$data['member_id'] ?? null;
        $this->loan_date = $data['loan_date'] ?? date('Y-m-d');
        $this->return_date = $data['return_date'] ?? null;
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
        return 'loans';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
    public function test(): bool
    {
        if (DateTools::dateGreaterThan($this->loan_date, false, $this->return_date)) {
            $this->message->warning('La fecha de retorno debe ser mayor o igual que la fecha de préstamo.');
            return false;
        }

        if (empty($this->primaryColumnValue()) &&
            empty($this->return_date) &&
            $this->isLoan($this->book_id)
        ) {
            $this->message->warning('El libro ya está prestado. No es posible volverlo a prestar');
            return false;
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
        return parent::url($type, 'ListMember?activetab=' . $list);
    }

    /**
     * Insert the model data in the database.
     *
     * @return bool
     */
    protected function insert(): bool
    {
        $sql = 'INSERT INTO ' . static::tableName()
            . ' (book_id, member_id, loan_date, return_date)'
            . ' VALUES ('
            . $this->book_id . ','
            . $this->member_id . ','
            . self::$dataBase->var2str($this->loan_date) . ','
            . self::$dataBase->var2str($this->return_date)
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
        return ['book_id', 'member_id', 'loan_date'];
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
            . 'book_id = ' . $this->book_id . ','
            . 'member_id = ' . $this->member_id . ','
            . 'loan_date = ' . self::$dataBase->var2str($this->loan_date) . ','
            . 'return_date = ' . self::$dataBase->var2str($this->return_date)
            . ' WHERE id = ' . self::$dataBase->var2str($this->id);
        return self::$dataBase->exec($sql);
    }
}
