<?php

namespace App\Http\Requests\UserDevice;

use App\Enum\GuestDevices;
use App\Enum\UserType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterDevice extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->type === UserType::GUEST;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'platform' => [
                'required',
                new Enum(GuestDevices::class),
            ],

            'device_token' => [
                'required',
                'string',
                'max:500',
                'unique:user_devices,device_token',            ],
        ];
    }
}
