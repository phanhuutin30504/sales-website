<?php

namespace App\Admin\Http\Requests\Slider;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Slider\SliderStatus;
use Illuminate\Validation\Rules\Enum;

class SliderItemRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'title' => ['required', 'string'],
            'slider_id' => ['required', 'exists:App\Models\Slider,id'],
            'link' => ['nullable'],
            'position' => ['required', 'integer'],
            'image' => ['required'],
            'mobile_image' => ['required'],
            'status' => ['required', new Enum(SliderStatus::class)],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\SliderItem,id'],
            'slider_id' => ['required', 'exists:App\Models\Slider,id'],
            'title' => ['required', 'string'],
            'link' => ['nullable'],
            'position' => ['required', 'integer'],
            'image' => ['required'],
            'mobile_image' => ['required'],
            'status' => ['required', new Enum(SliderStatus::class)],
        ];
    }
}