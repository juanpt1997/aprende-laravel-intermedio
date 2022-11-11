<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        // ? Queremos editar el proyecto sin que sea obligatoria la imagen
        // if ($this->getMethod() == "POST"){
        //     // Podríamos retornar según el método
        // }
        
        return [
            'title' => 'required',
            'url' => ['required', Rule::unique('projects')->ignore($this->route('project'))],
            // 'image' => 'required|image', // jpeg, png, bmp, gif, svg o webp
            'image' => [
                        $this->route('project') ? 'nullable' : 'required', 
                        'mimes:jpeg,png',
                        // 'dimensions:min_width=600,height=400'
                        // 'dimensions:ratio=16/9'
                        'max:2000'
                        ],
            'description' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El proyecto necesita un título'
        ];
    }
}
