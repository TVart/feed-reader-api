<?php

namespace App\Http\Controllers;

use App\Services\ApiFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    private const SOURCES = [
        'lemonde',
        '20minutes'
    ];

    private const SOURCE_NOT_FOUND_MESSAGE = "Source %s not available";

    /**
     * @var ApiFactory
     */
    private ApiFactory $apiFactory;

    /**
     * @var array|string[]
     */
    private array $rules = [
        'source' => 'required|string'
    ];

    public function __construct(ApiFactory $apiFactory)
    {
        $this->apiFactory = $apiFactory;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request,$this->rules);
            $source = $validated['source'];
            $api = $this->apiFactory::createFromSource($source);
            return new JsonResponse($api->getAll(), Response::HTTP_OK);
        } catch (\Exception  $exception){
            return $this->error($exception->getMessage());
        }
    }

    public function show($id, Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request,$this->rules);
            $source = $validated['source'];
            if(empty($id)){
                throw new \Exception("Empty id");
            }
            $api = $this->apiFactory::createFromSource($source);
            return new JsonResponse($api->getOne($id), Response::HTTP_OK);
        } catch (\Exception $exception)
        {
            return $this->error($exception->getMessage());
        }

    }

    private function error(string $message) : JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $message
        ], Response::HTTP_BAD_REQUEST);
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validated = $request->validate($rules);
        $source = $validated['source'];
        if(!in_array($source, self::SOURCES)){
            throw new \Exception(sprintf(self::SOURCE_NOT_FOUND_MESSAGE, $source));
        }
        return $validated;
    }
}
