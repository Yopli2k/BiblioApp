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

use BiblioApp\Core\DataBase\DataBaseWhere;
use BiblioApp\Core\ExtendedController\BaseView;
use BiblioApp\Core\ExtendedController\EditController;
use BiblioApp\Model\BookImage;

/**
 * Controller to edit a book record data.
 *
 * @author Jose Antonio Cuello Principal <yopli2000@gmail.com>
 */
class EditBook extends EditController
{
    public function getPageData(): array
    {
        $data = parent::getPageData();
        $data['title'] = 'Libro';
        $data['icon'] = 'fa-solid fa-book-bookmark';
        return $data;
    }

    /**
     * Load views
     */
    protected function createViews(): void
    {
        $this->addEditView('EditBook', 'Book', 'Libro', 'fa-solid fa-book-bookmark');
        $this->addEditListView('EditBookCategory', 'BookCategory', 'Categorías', 'fa-solid fa-object-group');
        $this->createViewsLoans();
        $this->createViewsRatings();
        $this->createViewsImages();
    }

    /**
     * Run the actions that alter data before reading it.
     *
     * @param ?string $action
     * @return bool
     */
    protected function execPreviousAction(?string $action): bool
    {
        switch ($action) {
            case 'add-image':
                $this->addImageAction();
                return true;

            case 'delete-image':
                $this->deleteImageAction();
                return true;
        }

        return parent::execPreviousAction($action);
    }

    /**
     * Load view data procedure
     *
     * @param string $viewName
     * @param BaseView $view
     */
    protected function loadData(string $viewName, mixed $view): void
    {
        switch ($viewName) {
            case 'EditBook':
                $primaryKey = $this->request->request->get('id', '');
                $code = $this->request->query->get('code', $primaryKey);
                $view->loadData($code);
                $this->title .= ' ' . $view->model->primaryDescription();
                break;

            case 'ListRating':
            case 'EditLoan':
            case 'EditBookCategory':
            case 'EditBookImage':
                $mvn = $this->getMainViewName();
                $idBook = $this->views[$mvn]->model->id;
                $where = [ new DataBaseWhere('book_id', $idBook) ];
                $view->loadData(false, $where, ['id' => 'DESC']);
                break;
        }
    }

    /**
     * Add a list of images.
     *
     * @return bool
     */
    private function addImageAction(): bool
    {
        if (false === $this->validateFormToken()) {
            return false;
        }

        $idbook = $this->request->request->get('book_id', 0);
        if (empty($idbook)) {
            $this->message->error('No se ha indicado el libro');
            return false;
        }

        $count = 0;
        $uploadFiles = $this->request->files->get('newfiles', []);
        foreach ($uploadFiles as $uploadFile) {
            if (false === $this->validateFile($uploadFile)) {
                continue;
            }

            try {
                $bookImage = new BookImage();
                $bookImage->book_id = $idbook;
                $bookImage->filename = $uploadFile->getClientOriginalName();
                $bookImage->filepath = BookImage::IMAGES_PATH;
                $bookImage->filetype = $uploadFile->getMimeType();
                $bookImage->filesize = $uploadFile->getSize();

                $uploadFile->move($bookImage->filepath, $bookImage->filename);
                if (false === $bookImage->save()) {
                    $this->message->error('Error al guardar imagen ' . $bookImage->filename);
                    continue;
                }
                ++$count;
            } catch (Exception $exc) {
                $this->message->error($exc->getMessage());
                return true;
            }
        }

        $this->message->info('Se han añadido ' . $count . ' imágenes');
        return true;
    }

    private function createViewsImages(string $viewName = 'EditBookImage'): void
    {
        $this->addHtmlView($viewName, 'BookImage', 'BookImage', 'images', 'fas fa-images');
    }

    private function createViewsLoans(string $viewName = 'EditLoan'): void
    {
        $this->addEditListView($viewName, 'Loan', 'Préstamos', 'fa-solid fa-book-open-reader');
        $this->views[$viewName]->disableColumn('libro');
    }

    private function createViewsRatings(string $viewName = 'ListRating'): void
    {
        $this->addListView($viewName, 'Rating', 'Opiniones', 'fa-regular fa-comments');
        $this->views[$viewName]->addSearchFields(['valoration']);
        $this->views[$viewName]->addOrderBy(['rating_date', 'rating_time', 'id'], 'Fecha', 2);
        $this->views[$viewName]->addOrderBy(['rating', 'id'], 'Valoración');

        $this->views[$viewName]->disableColumn('libro');

        // TODO: fix filter error
        // $this->views[$viewName]->addFilterAutocomplete('member_id', 'Asociado', 'member_id', 'members', 'id', 'name');

        // TODO: add approved button and process.
    }

    /**
     * Delete an image.
     *
     * @return bool
     */
    private function deleteImageAction(): bool
    {
        if (false === $this->validateFormToken()) {
            return false;
        }

        $id = $this->request->request->get('image_id', 0);
        if (empty($id)) {
            return false;
        }

        $bookImage = new BookImage();
        if (false === $bookImage->loadFromCode($id)) {
            return false;
        }

        if (false === $bookImage->delete()) {
            $this->message->warning('Error al eliminar la imagen');
            return false;
        }

        $this->message->info('Imagen eliminada correctamente');
        return true;
    }

    private function validateFile($uploadFile): bool
    {
        if (false === $uploadFile->isValid()) {
            $this->message->error($uploadFile->getErrorMessage());
            return false;
        }

        if (false === strpos($uploadFile->getMimeType(), 'image/')) {
            $this->message->error('El formato del archivo ' . $uploadFile->getClientOriginalName() . ' no está soportado');
            return false;
        }
        return true;
    }
}
