<?php

namespace App\Http\Controllers;

use App\Traits\DiffusionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlaygroundController extends Controller
{
    use DiffusionTrait;

    public $httpClient;
    public $apiKey;

    public function __construct()
    {
        $this->httpClient = Http::baseUrl(config('services.stable_diffusion_api.baseurl'))->withHeaders(['Content-Type' => 'application/json']);

        $this->apiKey = config('services.stable_diffusion_api.key');
    }

    public function index(Request $request)
    {
       $channel = $request->channel??"stable-diffusion";
       return view('index', compact('channel'));
    }


    public function stableDiffusion(Request $request)
    {

        $canProceed = $this->canProceedRequest($request);

        if (!$canProceed['status']) {
           return response()->json(["status" => "error", "message" => $canProceed['message']], 400);
        }

        switch ($request->endpoint) {

            case 'text_to_image':
                return $this->handleTextToImage($request);
                break;

            case 'image_to_image':
            return $this->handleImageToImage($request);
                break;

            case 'inpaint':
            return  $this->handleInpainting($request);
                break;

            default:
                return  $this->handleFetchQueuedImages($request);
                break;
        }
    }


    public function dreambooth(Request $request)
    {

       $canProceed = $this->canProceedRequest($request);

       if (!$canProceed['status']) {
          return response()->json(["status" => "error", "message" => $canProceed['message']], 400);
       }

        switch ($request->endpoint) {

            case 'text_to_image':
                return $this->handleDreamboothTextToImage($request);
                break;

            case 'image_to_image':
               return $this->handleDreamboothImageToImage($request);
                break;
            case 'inpaint':
                return $this->handleDreamboothInpainting($request);
                break;

            default:
                return  $this->handleDreamboothFetchQueuedImages($request);
                break;
          }
    }

    public function dreamboothTraining(Request $request)
    {

       if (empty($request->endpoint)) {
          return response()->json(["status" => "error", "message" => "Kindly select the endpoint from dropdown"], 400);
       }


        switch ($request->endpoint) {

            case 'create_request':
                return $this->handleTrainCreateRequest($request);
                break;
            default:
               return $this->handleTrainStatus($request);
                break;
          }

    }


    public function imageUpload(Request $request)
    {


        if (empty($request->image)) {
            return response()->json(["status" => "error", "message" => "kindly upload image"], 400);
         }

        $data = [
            "key" => $this->apiKey,
            "image" => $request->image,
            "crop" =>  $request->crop
        ];

        $result = $this->httpClient->post("base64_crop", $data);
        $response = json_decode($result, true);

        info($response);
        if (!array_key_exists('status', $response)) {
            return response()->json(["status" => "error", "message" => "ops, request failed, try again later"], 400);
        }

        if ($response['status'] == 'error') {
            if (isset($response['messege']) && !is_array($response['messege'])) {
                $message = $response['messege'];
            } elseif (isset($response['message']) && !is_array($response['message'])) {
                $message = $response['message'];
            }else {
                $message = "pass the appropriate parameters";
            }
            return response()->json(["status" => "error", "message" => $message], 400);
        }

        return response()->json(["status" => "success", "message" => $response['messege']], 200);
    }


    public function publicModels()
    {
        $data = [
            "key" => $this->apiKey
        ];

        $result = Http::post("https://stablediffusionapi.com/api/v4/dreambooth/model_list", $data);
        $response = json_decode($result, true);

         return response()->json($response, 200);

    }



    private function handleTextToImage($request)
    {

        $data = [
            'key' => $this->apiKey,
            'prompt' => $request->prompt,
            'width' => $request->width,
            'height' => $request->height,
            "negative_prompt" => $request->negative_prompt,
            "samples" => 1,
            "num_inference_steps" => "20",
            "seed" => null,
            "guidance_scale" => 7.5,
            "webhook" => null,
            "track_id" => null
        ];

       $result = $this->httpClient->post("text2img", $data);
       $response = json_decode($result, true);

        return $this->handleResponses($response);

    }


    private function handleImageToImage($request)
    {
       $hasInitialImage = $this->hasInitialImage($request);

       if (!$hasInitialImage['status']) {
           return response()->json(["status" => "error", "message" => $hasInitialImage['message']], 400);
       }


        $data = [
            "key" => $this->apiKey,
            "prompt" =>  $request->prompt,
            "negative_prompt" =>  $request->negative_prompt,
            "init_image" => $request->init_image,
            'width' => $request->width,
            'height' => $request->height,
            "negative_prompt" => $request->negative_prompt,
            "samples" => $request->request->add(['samples' => 1]),
            "num_inference_steps" => "30",
            "guidance_scale" =>  7.5,
            "strength" =>  0.7,
            "seed" => null,
            "webhook" => null,
            "track_id" => null
        ];

        $result = $this->httpClient->post("img2img", $data);
        $response = json_decode($result, true);

        return $this->handleResponses($response);

    }


    public function handleFetchQueuedImages($request)
    {
          $hasResponseId = $this->requestHasResponseId($request);

          if (!$hasResponseId['status']) {
            return response()->json(["status" => "error", "message" => $hasResponseId['message']], 400);
           }

        $data = [
            "key" => $this->apiKey
        ];


        $result = $this->httpClient->post("fetch/{$request->response_id}", $data);
        $response = json_decode($result, true);

        return $this->handleResponses($response);
    }


    private function handleInpainting($request)
    {

        $hasInpaintingRequest = $this->hasInpaintingRequest($request);

        if (!$hasInpaintingRequest['status']) {
            return response()->json(["status" => "error", "message" => $hasInpaintingRequest['message']], 400);
        }

        $data = [
            "key" => $this->apiKey,
            "prompt" => $request->prompt,
            "negative_prompt" => $request->negative_prompt,
            "init_image" => $request->init_image,
            "mask_image" => $request->mask_image,
            'width' => $request->width,
            'height' => $request->height,
            "samples" => 1,
            "num_inference_steps" => "30",
            "guidance_scale" => 7.5,
            "strength" =>  0.7,
            "seed" => null,
            "webhook" => null,
            "track_id" => null
        ];

        $result = $this->httpClient->post("inpaint", $data);
        $response = json_decode($result, true);

        return $this->handleResponses($response);

    }




    private function handleDreamboothTextToImage($request)
    {

        $data = [
            "key"=> $this->apiKey,
            "model_id"=> $request->model_id,
            "prompt"=> $request->prompt,
            'width' => $request->width,
            'height' => $request->height,
            "samples" => 1,
            "num_inference_steps" =>  "30",
            "seed" => null,
            "guidance_scale"=> 7.5,
            "webhook" => null,
            "track_id" => null
        ];

        $result = $this->httpClient->post("dreambooth", $data);
        $response = json_decode($result, true);

        return $this->handleResponses($response);
    }

    private function handleDreamboothImageToImage($request)
    {
        $hasInitialImage = $this->hasInitialImage($request);

       if (!$hasInitialImage['status']) {
           return response()->json(["status" => "error", "message" => $hasInitialImage['message']], 400);
       }

        $data = [
            "key"=> $this->apiKey,
            "model_id"=> $request->model_id,
            "prompt"=> $request->prompt,
            "init_image" => $request->init_image,
            'width' => $request->width,
            'height' => $request->height,
            "samples" => 1,
            "num_inference_steps" =>  "30",
            "seed" => null,
            "guidance_scale"=> 7.5,
            "webhook" => null,
            "track_id" => null
        ];


        $result = $this->httpClient->post("dreambooth/img2img", $data);
        $response = json_decode($result, true);

        return $this->handleResponses($response);

    }

    private function handleDreamboothInpainting($request)
    {

        $hasInpaintingRequest = $this->hasInpaintingRequest($request);

        if (!$hasInpaintingRequest['status']) {
            return response()->json(["status" => "error", "message" => $hasInpaintingRequest['message']], 400);
        }

        $data = [
            "key"=> $this->apiKey,
            "model_id"=> $request->model_id,
            "prompt"=> $request->prompt,
            "init_image" => $request->init_image,
            "mask_image" => $request->masked_image,
            'width' => $request->width,
            'height' => $request->height,
            "samples" => 1,
            "num_inference_steps" =>  "30",
            "seed" => null,
            "guidance_scale"=> 7.5,
            "webhook" => null,
            "track_id" => null
        ];


        $result = $this->httpClient->post("dreambooth/inpaint", $data);
        $response = json_decode($result, true);

        return $this->handleResponses($response);
    }

    private function handleDreamboothFetchQueuedImages($request)
    {

        if (empty($request->response_id)) {
            return response()->json(["status" => "error", "message" => "The response ID is needed when fetching queued image"], 400);
        }

        $data = [
            "key" => $this->apiKey
        ];


        $result = $this->httpClient->post("fetch/{$request->response_id}", $data);
        $response = json_decode($result, true);

        return $this->handleResponses($response);
    }




    private function handleTrainCreateRequest($request)
    {

        if (empty($request->images)) {
            return response()->json(["status" => "error", "message" => "Kindly add images"], 400);
        }

        if (empty($request->training_type)) {
            return response()->json(["status" => "error", "message" => "Kindly select the training type"], 400);
        }

        if (empty($request->class_prompt) || empty($request->base_model_id) || empty($request->training_type) || empty($request->instance_prompt)) {
            return response()->json(["status" => "error", "message" => "Fill all required parameters"], 400);
        }

        $data = [
            "key" => $this->apiKey,
            "instance_prompt" => $request->instance_prompt,
            "class_prompt"  =>  $request->class_prompt,
            "base_model_id"  =>  $request->base_model_id,
            "images" => $request->images,
            "seed" => "0",
            "training_type" => $request->training_type,
            "max_train_steps" => "2000",
            "webhook" => ""
        ];

        $result = $this->httpClient->post("fine_tune", $data);
        $response = json_decode($result, true);

        return $this->trainingResponse($response);
    }

    private function handleTrainStatus($request)
    {
        $data = [
            "key" => $this->apiKey
        ];

        if (empty($request->training_id)) {
            return response()->json(["status" => "error", "message" => "Training ID is required when trying to get training status"], 400);
        }


        $result = $this->httpClient->post("fine_tune_status/{$request->training_id}", $data);
        $response = json_decode($result, true);

        if ($response['status'] == 'error') {
            $message = !is_array($response['messege']) ? $response['messege'] : "pass the appropriate parameters";
            return response()->json(["status" => "error", "message" => $message], 400);
        }

        if ($response['status'] == 'processing') {
            $message = !is_array($response['messege']) ? $response['messege'] : "pass the appropriate parameters";
            return response()->json(["status" => "processing", "message" => $message], 400);
        }

        return response()->json(["status" => "success", "message" => $response['message'] ], 200);
    }


}
