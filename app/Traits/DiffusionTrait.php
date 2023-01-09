<?php

namespace App\Traits;

trait DiffusionTrait
{

    protected function handleResponses($response)
    {
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

        if ($response['status'] == 'processing') {
            $stableDiffusionId = $response['id'];
            $queueTime = round($response['eta']);
            return response()->json(["status" => "processing", "message" => "{$response['messege']}. Fetch the image after {$queueTime}sec by calling the fetch endpoint using Response ID as {$stableDiffusionId} "], 200);
        }

        $result = [
            'status' => "success",
            "message" => "Generation completed",
            'image' => $response['output'][0],
            'width' => $response['meta']['W']??512,
            'height' => $response['meta']['H']??512,
        ];

        return response()->json($result, 200);

    }

    public function trainingResponse($response)
    {
        if (!array_key_exists('status', $response)) {
            return response()->json(["status" => "error", "message" => "ops, request failed, try again later"], 400);
        }

        if ($response['status'] == 'error') {
            $message = !is_array($response['messege']) ? $response['messege'] : "pass the appropriate parameters";
            return response()->json(["status" => "error", "message" => $message], 400);
        }

        if ($response['status'] == 'processing') {
            $message = !is_array($response['messege']) ? $response['messege'] : "pass the appropriate parameters";
            return response()->json(["status" => "processing", "message" => $message], 400);
        }

        return response()->json(["status" => "success", "message" => "{$response['data']}. Your training id is {$response['training_id']}"], 200);
    }


    protected function canProceedRequest($request)
    {
        if (empty($request->endpoint)) {
            return $this->slimResponse(false, "Kindly select the endpoint from dropdown");
        }

        if ($request->endpoint == "fetch_endpoint") {
            return $this->slimResponse(true);
        }


        if ($request->channel == "dreambooth" && empty($request->model_id)) {
            return $this->slimResponse(false, "Kindly select model from the dropdown");
        }

        return $this->slimResponse(true);
    }


    protected function hasInitialImage($request)
    {
        if (empty($request->init_image)) {
            return $this->slimResponse(false, "The initial image url is required when image to image endpoint is selected");
        }
        return $this->slimResponse(true);
    }

    protected function slimResponse(bool $status, string $message="")
    {
        return ["status" => $status, "message" => $message];
    }


    protected function requestHasResponseId($request)
    {
        if (empty($request->response_id)) {
            return $this->slimResponse(false, "The response ID is needed when fetching queued image");
        }
        return $this->slimResponse(true);
    }


    protected function hasInpaintingRequest($request)
    {
        if (empty($request->init_image)) {
            return $this->slimResponse(false, "The initial image url is required when inpainting endpoint is selected");
        }

        if (empty($request->mask_image)) {
            return $this->slimResponse(false, "The masked image url is required when inpainting endpoint is selected");
        }

        return $this->slimResponse(true);
    }



}
