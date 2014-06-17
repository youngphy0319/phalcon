<?php

namespace WscnApiVer2\Controllers;

use Swagger\Annotations as SWG;
use Eva\EvaFileSystem\Models;
use Eva\EvaFileSystem\Forms;
use Eva\EvaEngine\Exception;

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.2",
 *  resourcePath="/media",
 *  basePath="/v2"
 * )
 */
class MediaController extends ControllerBase
{
    /**
     *
     * @SWG\Api(
     *   path="/media",
     *   description="Media related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="GET",
     *       summary="Get media list",
     *       notes="Returns a media based on ID",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="q",
     *           description="Keyword",
     *           paramType="query",
     *           required=false,
     *           type="string"
     *         ),
     *         @SWG\Parameter(
     *           name="status",
     *           description="Status, allow value : pending | published | deleted | draft",
     *           paramType="query",
     *           required=false,
     *           type="string"
     *         ),
     *         @SWG\Parameter(
     *           name="uid",
     *           description="User ID",
     *           paramType="query",
     *           required=false,
     *           type="integer"
     *         ),
     *         @SWG\Parameter(
     *           name="cid",
     *           description="Category ID",
     *           paramType="query",
     *           required=false,
     *           type="integer"
     *         ),
     *         @SWG\Parameter(
     *           name="order",
     *           description="Order, allow value : +-id, +-created_at, +-sortOrder default is -created_at",
     *           paramType="query",
     *           required=false,
     *           type="string"
     *         ),
     *         @SWG\Parameter(
     *           name="limit",
     *           description="Limit max:100 | min:3; default is 25",
     *           paramType="query",
     *           required=false,
     *           type="integer"
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function indexAction()
    {
        $limit = $this->request->getQuery('limit', 'int', 25);
        $limit = $limit > 100 ? 100 : $limit;
        $limit = $limit < 3 ? 3 : $limit;
        $order = $this->request->getQuery('order', 'string', 'id');
        $query = array(
            'q' => $this->request->getQuery('q', 'string'),
            'status' => $this->request->getQuery('status', 'string'),
            'uid' => $this->request->getQuery('uid', 'int'),
            'extension' => $this->request->getQuery('extension', 'string'),
            'image' => $this->request->getQuery('image', 'int'),
            'order' => $order,
            'limit' => $limit,
            'page' => $this->request->getQuery('page', 'int', 1),
        );

        $form = new Forms\FilterForm();
        $form->setValues($this->request->getQuery());

        $fileManager = new Models\FileManager();
        $medias = $fileManager->findFiles($query);
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "builder" => $medias,
            "limit"=> $limit,
            "page" => $query['page']
        ));
        $paginator->setQuery($query);
        $pager = $paginator->getPaginate();

        $mediaArray = array();
        if ($pager->items) {
            foreach ($pager->items as $key => $media) {
                $mediaArray[] = $media->dump(Models\FileManager::$defaultDump);
            }
        }

        $data = array(
            'paginator' => $this->getApiPaginator($paginator),
            'results' => $mediaArray,
        );
        return $this->response->setJsonContent($data);
    }

    /**
    *
    * @SWG\Api(
        *   path="/media/{mediaId}",
        *   description="Media related api",
        *   produces="['application/json']",
        *   @SWG\Operations(
            *     @SWG\Operation(
                *       method="GET",
                *       summary="Find media by ID",
     *       notes="Returns a media based on ID",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="mediaId",
     *           description="ID of media",
     *           paramType="path",
     *           required=true,
     *           type="integer"
     *         )
     *       ),
     *       @SWG\ResponseMessages(
     *          @SWG\ResponseMessage(
     *            code=400,
     *            message="Invalid ID supplied"
     *          ),
     *          @SWG\ResponseMessage(
     *            code=404,
     *            message="media not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function getAction()
    {
        $id = $this->dispatcher->getParam('id');
        $mediaModel = new Models\FileManager();
        $media = $mediaModel->findFirst($id);
        if (!$media) {
            throw new Exception\ResourceNotFoundException('Request media not exist');
        }
        $media = $media->dump(Models\FileManager::$defaultDump);
        return $this->response->setJsonContent($media);
    }

    /**
     *
     * @SWG\Api(
     *   path="/media/{mediaId}",
     *   description="Media related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="PUT",
     *       summary="Update media by ID",
     *       notes="Returns updated media",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="mediaId",
     *           description="ID of media",
     *           paramType="path",
     *           required=true,
     *           type="integer"
     *         )
     *       ),
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="mediaData",
     *           description="Media info",
     *           paramType="body",
     *           required=true,
     *           type="string"
     *         )
     *       ),
     *       @SWG\ResponseMessages(
     *          @SWG\ResponseMessage(
     *            code=400,
     *            message="Invalid ID supplied"
     *          ),
     *          @SWG\ResponseMessage(
     *            code=404,
     *            message="media not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
     public function putAction()
     {
         $id = $this->dispatcher->getParam('id');
         $data = $this->request->getRawBody();
         if (!$data) {
             throw new Exception\InvalidArgumentException('No data input');
         }
         if (!$data = json_decode($data, true)) {
             throw new Exception\InvalidArgumentException('Data not able to decode as JSON');
         }

         $media = Models\Media::findFirst($id);
         if (!$media) {
             throw new Exception\ResourceNotFoundException('Request media not exist');
         }

        $form = new Forms\MediaForm();
        $form->setModel($media);
        $form->addForm('text', 'Eva\EvaFileSystem\Forms\TextForm');


        if (!$form->isFullValid($data)) {
            return $this->displayJsonInvalidMessages($form);
        }

        try {
            $form->save('updateMedia');
            $data = $media->dump(Models\Media::$defaultDump);
            return $this->response->setJsonContent($data);
        } catch (\Exception $e) {
            return $this->displayExceptionForJson($e, $form->getModel()->getMessages());
        }
     }

     /**
     *
     * @SWG\Api(
     *   path="/media",
     *   description="Media related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="POST",
     *       summary="Create new media",
     *       notes="Returns a media based on ID",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="media json",
     *           description="Media info",
     *           paramType="body",
     *           required=true,
     *           type="string"
     *         )
     *       ),
     *       @SWG\ResponseMessages(
     *          @SWG\ResponseMessage(
     *            code=400,
     *            message="Invalid ID supplied"
     *          ),
     *          @SWG\ResponseMessage(
     *            code=404,
     *            message="media not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function mediaAction()
    {
        $data = $this->request->getRawBody();
        if (!$data) {
            throw new Exception\InvalidArgumentException('No data input');
        }
        if (!$data = json_decode($data, true)) {
            throw new Exception\InvalidArgumentException('Data not able to decode as JSON');
        }

        $form = new Forms\MediaForm();
        $media = new Models\Media();
        $form->setModel($media);
        $form->addForm('text', 'Eva\EvaFileSystem\Forms\TextForm');

        if (!$form->isFullValid($data)) {
            return $this->displayJsonInvalidMessages($form);
        }

        try {
            $form->save('createMedia');
            $data = $media->dump(Models\Media::$defaultDump);
            return $this->response->setJsonContent($data);
        } catch (\Exception $e) {
            return $this->displayExceptionForJson($e, $form->getModel()->getMessages());
        }
    }

    /**
    *
     * @SWG\Api(
     *   path="/media/{mediaId}",
     *   description="Media related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="DELETE",
     *       summary="Delete media by ID",
     *       notes="Returns deleted media",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="mediaId",
     *           description="ID of media",
     *           paramType="path",
     *           required=true,
     *           type="integer"
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function deleteAction()
    {
         $id = $this->dispatcher->getParam('id');
         $media = Models\Media::findFirst($id);
         if (!$media) {
             throw new Exception\ResourceNotFoundException('Request media not exist');
         }
         $mediainfo = $media->dump(Models\Media::$defaultDump);
         try {
             $media->removeMedia($id);
             return $this->response->setJsonContent($mediainfo);
         } catch (\Exception $e) {
             return $this->displayExceptionForJson($e, $media->getMessages());
         }
    }
}
