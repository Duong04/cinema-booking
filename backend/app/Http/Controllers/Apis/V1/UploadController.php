<?php

namespace App\Http\Controllers\Apis\V1;


use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use App\Http\Requests\Upload\DeleteFileRequest;
use App\Http\Requests\Upload\DeleteMultipleRequest;
use App\Http\Requests\Upload\UploadFileRequest;
use App\Http\Requests\Upload\UploadImageRequest;
use App\Http\Requests\Upload\UploadMultipleRequest;
use App\Traits\ResponseHelper;

class UploadController extends Controller
{
    use ResponseHelper;
    public function __construct(
        private readonly CloudinaryService $cloudinaryService
    ) {
    }

    public function uploadImage(UploadImageRequest $request)
    {
        $folder = $request->input('folder', 'images');
        $url = $this->cloudinaryService->upload($request->file('file'), $folder);

        return $this->success(['url' => $url], 'Upload ảnh thành công', 201);
    }

    public function uploadFile(UploadFileRequest $request)
    {
        $folder = $request->input('folder', 'files');
        $url = $this->cloudinaryService->uploadFile($request->file('file'), $folder);

        return $this->success(['url' => $url], 'Upload file thành công', 201);
    }

    public function uploadMultiple(UploadMultipleRequest $request)
    {
        $folder = $request->input('folder', 'files');
        $urls = $this->cloudinaryService->uploadMultiple($request->file('files'), $folder);

        return $this->success(['url' => $urls], 'Upload ' . count($urls) . ' file thành công', 201);
    }

    public function delete(DeleteFileRequest $request)
    {
        $this->cloudinaryService->delete($request->input('url'));

        return $this->success(null, 'Xóa file thành công', 200);
    }

    public function deleteMultiple(DeleteMultipleRequest $request)
    {
        $this->cloudinaryService->deleteMultiple($request->input('urls'));
        return $this->success(null, 'Xóa ' . count($request->input('urls')) . ' file thành công', 200);
    }
}