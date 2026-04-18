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
use OpenApi\Attributes as OA;

class UploadController extends Controller
{
    use ResponseHelper;
    public function __construct(
        private readonly CloudinaryService $cloudinaryService
    ) {
    }

    #[OA\Post(
        path: "/api/v1/upload/image",
        summary: "Upload a single image",
        tags: ["Upload"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["file"],
                    properties: [
                        new OA\Property(
                            property: "file",
                            type: "string",
                            format: "binary",
                            description: "Image file (jpeg, png, jpg, gif, webp, max 5MB)"
                        ),
                        new OA\Property(
                            property: "folder",
                            type: "string",
                            example: "images"
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Upload image successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Upload ảnh thành công"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "url", type: "string", example: "https://res.cloudinary.com/demo/image/upload/v123456/sample.jpg"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            ),
        ]
    )]
    public function uploadImage(UploadImageRequest $request)
    {
        $folder = $request->input('folder', 'images');
        $url = $this->cloudinaryService->upload($request->file('file'), $folder);

        return $this->success(['url' => $url], 'Upload ảnh thành công', 201);
    }

    #[OA\Post(
        path: "/api/v1/upload/file",
        summary: "Upload a single file",
        tags: ["Upload"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["file"],
                    properties: [
                        new OA\Property(
                            property: "file",
                            type: "string",
                            format: "binary",
                            description: "File (pdf, doc, docx, mp4, mov, avi, max 50MB)"
                        ),
                        new OA\Property(
                            property: "folder",
                            type: "string",
                            example: "files"
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Upload file successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Upload file thành công"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "url", type: "string", example: "https://res.cloudinary.com/demo/raw/upload/v123456/sample.pdf"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            ),
        ]
    )]
    public function uploadFile(UploadFileRequest $request)
    {
        $folder = $request->input('folder', 'files');
        $url = $this->cloudinaryService->uploadFile($request->file('file'), $folder);

        return $this->success(['url' => $url], 'Upload file thành công', 201);
    }

    #[OA\Post(
        path: "/api/v1/upload/multiple",
        summary: "Upload multiple files",
        tags: ["Upload"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["files"],
                    properties: [
                        new OA\Property(
                            property: "files",
                            type: "array",
                            maxItems: 10,
                            items: new OA\Items(type: "string", format: "binary"),
                            description: "Max 10 files, each ≤ 10MB"
                        ),
                        new OA\Property(
                            property: "folder",
                            type: "string",
                            example: "files"
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Upload multiple files successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Upload 3 file thành công"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "url",
                                    type: "array",
                                    items: new OA\Items(type: "string"),
                                    example: [
                                        "https://res.cloudinary.com/demo/raw/upload/v1/file1.pdf",
                                        "https://res.cloudinary.com/demo/raw/upload/v1/file2.pdf"
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            ),
        ]
    )]
    public function uploadMultiple(UploadMultipleRequest $request)
    {
        $folder = $request->input('folder', 'files');
        $urls = $this->cloudinaryService->uploadMultiple($request->file('files'), $folder);

        return $this->success(['url' => $urls], 'Upload ' . count($urls) . ' file thành công', 201);
    }

    #[OA\Delete(
        path: "/api/v1/upload",
        summary: "Delete a file by URL",
        tags: ["Upload"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["url"],
                properties: [
                    new OA\Property(property: "url", type: "string", example: "https://res.cloudinary.com/demo/image/upload/v123/sample.jpg"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Deleted successfully",
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
            ),
        ]
    )]
    public function delete(DeleteFileRequest $request)
    {
        $this->cloudinaryService->delete($request->input('url'));

        return $this->success(null, 'Xóa file thành công', 200);
    }

    #[OA\Delete(
        path: "/api/v1/upload/multiple",
        summary: "Delete multiple files",
        tags: ["Upload"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["urls"],
                properties: [
                    new OA\Property(
                        property: "urls",
                        type: "array",
                        items: new OA\Items(type: "string"),
                        example: [
                            "https://res.cloudinary.com/demo/image/upload/v1/file1.jpg",
                            "https://res.cloudinary.com/demo/image/upload/v1/file2.jpg"
                        ]
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Deleted successfully",
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
            ),
        ]
    )]
    public function deleteMultiple(DeleteMultipleRequest $request)
    {
        $this->cloudinaryService->deleteMultiple($request->input('urls'));
        return $this->success(null, 'Xóa ' . count($request->input('urls')) . ' file thành công', 200);
    }
}