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
use BiblioApp\Core\Tools\Tools;

/**
 * Class to manage the book data.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 */
class Book extends AppModel
{

    /** @var string */
    public string $author;

    /** @var string */
    public string $editorial;

    /**
     * Primary Key.
     *
     * @var ?int
     */
    public ?int $id;

    /** @var string */
    public string $isbn;

    /**
     * Title of the book.
     *
     * @var string
     */
    public string $name;

    /** @var int */
    public int $pages;

    /** @var int */
    public int $publication;

    /**
     * The average rating of the book.
     *   - it is calculated in the getRating method.
     *   - if the book has no rating, it is set to 3.
     *
     * @var int
     */
    public int $rating = 0;

    /** @var bool */
    public bool $recommended;

    /** @var string */
    public string $synopsis;

    /**
     * Reset the values of all model properties.
     */
    public function clear(): void
    {
        $this->author = '';
        $this->editorial = '';
        $this->id = null;
        $this->isbn = '';
        $this->name = '';
        $this->pages = 0;
        $this->publication = date('Y');
        $this->recommended = false;
        $this->synopsis = '';
    }

    /**
     * Get the image of the book.
     *
     * @return BookImage
     */
    public function getImage(): BookImage
    {
        $bookImage = new BookImage();
        $where = [ new DataBaseWhere('book_id', $this->id) ];
        $bookImage->loadFromCode('', $where, []);
        return $bookImage;
    }

    /**
     * Get the average rating of the book.
     * For performance reasons, it is only calculated once, when not has value.
     *
     * @return int
     */
    public function getRating(): int
    {
        if (empty($this->rating)) {
            $sql = 'SELECT ROUND(COALESCE(AVG(rating), 3), 0) AS rating FROM ratings WHERE book_id = ' . $this->id;
            $data = self::$dataBase->select($sql);
            $this->rating = (int)$data[0]['rating'] ?? 3;
        }
        return $this->rating;
    }

    /**
     * Assign the values of the $data array to the model properties.
     *
     * @param array $data
     */
    public function loadFromData(array $data = []): void
    {
        $this->author = $data['author'] ?? '';
        $this->editorial = $data['editorial'] ?? '';
        $this->id = (int)$data['id'] ?? null;
        $this->isbn = $data['isbn'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->pages = (int)$data['pages'] ?? 0;
        $this->publication = (int)$data['publication'] ?? date('Y');
        $this->recommended = (bool)$data['recommended'] ?? false;
        $this->synopsis = $data['synopsis'] ?? '';
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
        return 'books';
    }

    /**
     * Returns true if there are no errors in the values of the model properties.
     * It runs inside the save method.
     *
     * @return bool
     */
    public function test(): bool
    {
        $this->author = Tools::noHtml(mb_strtolower($this->author ?? ''));
        $this->isbn = Tools::noHtml(mb_strtolower($this->isbn ?? ''));
        $this->name = Tools::noHtml(mb_strtolower($this->name ?? ''));
        $this->synopsis = Tools::noHtml(mb_strtolower($this->synopsis ?? ''));

        if (false ===  is_numeric($this->isbn)) {
            $this->message->error('El ISBN debe ser un número.');
            return false;
        }

        if ($this->publication > date('Y')) {
            $this->message->error('El año de publicación no puede ser mayor que el año actual.');
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
            . ' (author, editorial, isbn, name, publication, pages, recommended, synopsis)'
            . ' VALUES ('
            . self::$dataBase->var2str($this->author) . ','
            . self::$dataBase->var2str($this->editorial) . ','
            . self::$dataBase->var2str($this->isbn) . ','
            . self::$dataBase->var2str($this->name) . ','
            . $this->publication . ','
            . $this->pages . ','
            . self::$dataBase->var2str($this->recommended) . ','
            . self::$dataBase->var2str($this->synopsis)
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
        return ['author', 'editorial', 'isbn', 'name', 'publication', 'pages', 'synopsis'];
    }

    /**
     * Update the model data in the database.
     *
     * @return bool
     */
    protected function update(): bool
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET '
                . 'author = ' . self::$dataBase->var2str($this->author) . ','
                . 'editorial = ' . self::$dataBase->var2str($this->editorial) . ','
                . 'isbn = ' . self::$dataBase->var2str($this->isbn) . ','
                . 'name = ' . self::$dataBase->var2str($this->name). ','
                . 'pages = ' . $this->pages . ','
                . 'publication = ' . $this->publication . ','
                . 'recommended = ' . self::$dataBase->var2str($this->recommended) . ','
                . 'synopsis = ' . self::$dataBase->var2str($this->synopsis)
            . ' WHERE id = ' . self::$dataBase->var2str($this->id);
        return self::$dataBase->exec($sql);
    }
}
