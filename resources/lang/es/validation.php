<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other sea :value.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo debe contener letras.',
    'alpha_dash' => 'El campo :attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo debe contener letras y números.',
    'array' => 'El campo :attribute debe ser un conjunto.',
    'ascii' => 'El campo :attribute solo debe contener caracteres alfanuméricos y símbolos de un solo byte.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute tiene que tener entre :min - :max elementos.',
        'file' => 'El campo :attribute debe pesar entre :min - :max kilobytes.',
        'numeric' => 'El campo :attribute tiene que estar entre :min - :max.',
        'string' => 'El campo :attribute tiene que tener entre :min - :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe tener un valor verdadero o falso.',
    'can' => 'El campo :attribute contiene un valor no autorizado.',
    'confirmed' => 'La confirmación del campo :attribute no coincide.',
    'current_password' => 'La contraseña es incorrecta.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute no corresponde al formato :format.',
    'decimal' => 'El campo :attribute debe tener :decimal decimales.',
    'declined' => 'El campo :attribute debe ser rechazado.',
    'declined_if' => 'El campo :attribute debe ser rechazado cuando :other sea :value.',
    'different' => 'El campo :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'Las dimensiones de la imagen :attribute no son válidas.',
    'distinct' => 'El campo :attribute contiene un valor duplicado.',
    'doesnt_end_with' => 'El campo :attribute no debe terminar con uno de los siguientes: :values.',
    'doesnt_start_with' => 'El campo :attribute no debe comenzar con uno de los siguientes: :values.',
    'email' => 'El campo :attribute no es un correo válido.',
    'ends_with' => 'El campo :attribute debe terminar con uno de los siguientes: :values.',
    'enum' => 'El campo :attribute seleccionado no es válido.',
    'exists' => 'El campo :attribute seleccionado no es válido.',
    'extensions' => 'El campo :attribute debe tener una de las siguientes extensiones: :values.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute es obligatorio.',
    'gt' => [
        'array' => 'El campo :attribute debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe tener más de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
    ],
    'gte' => [
        'array' => 'El campo :attribute debe tener como mínimo :value elementos.',
        'file' => 'El campo :attribute debe tener como mínimo :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string' => 'El campo :attribute debe tener como mínimo :value caracteres.',
    ],
    'hex_color' => 'El campo :attribute debe ser un color hexadecimal válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo :attribute seleccionado no es válido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe tener una cadena JSON válida.',
    'lowercase' => 'El campo :attribute debe estar en minúsculas.',
    'lt' => [
        'array' => 'El campo :attribute debe tener menos de :value elementos.',
        'file' => 'El campo :attribute debe tener menos de :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string' => 'El campo :attribute debe tener menos de :value caracteres.',
    ],
    'lte' => [
        'array' => 'El campo :attribute debe tener como máximo :value elementos.',
        'file' => 'El campo :attribute debe tener como máximo :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string' => 'El campo :attribute debe tener como máximo :value caracteres.',
    ],
    'mac_address' => 'El campo :attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
        'file' => 'El campo :attribute no debe ser mayor que :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe ser mayor que :max caracteres.',
    ],
    'max_digits' => 'El campo :attribute no debe tener más de :max dígitos.',
    'mimes' => 'El campo :attribute debe ser un archivo con formato: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo con formato: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El campo :attribute debe tener al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits' => 'El campo :attribute debe tener al menos :min dígitos.',
    'missing' => 'El campo :attribute no está presente.',
    'missing_if' => 'El campo :attribute no está presente cuando :other sea :value.',
    'missing_unless' => 'El campo :attribute no está presente a menos que :other sea :value.',
    'missing_with' => 'El campo :attribute no está presente cuando :values esté presente.',
    'missing_with_all' => 'El campo :attribute no está presente cuando :values estén presentes.',
    'multiple_of' => 'El campo :attribute debe ser múltiplo de :value.',
    'not_in' => 'El campo :attribute seleccionado no es válido.',
    'not_regex' => 'El formato del campo :attribute no es válido.',
    'numeric' => 'El campo :attribute debe ser numérico.',
    'password' => [
        'letters' => 'El campo :attribute debe contener al menos una letra.',
        'mixed' => 'El campo :attribute debe contener al menos una letra mayúscula y una minúscula.',
        'numbers' => 'El campo :attribute debe contener al menos un número.',
        'symbols' => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El campo :attribute ha aparecido en una filtración de datos. Por favor, elige un :attribute diferente.',
    ],
    'present' => 'El campo :attribute debe estar presente.',
    'present_if' => 'El campo :attribute debe estar presente cuando :other sea :value.',
    'present_unless' => 'El campo :attribute debe estar presente a menos que :other sea :value.',
    'present_with' => 'El campo :attribute debe estar presente cuando :values esté presente.',
    'present_with_all' => 'El campo :attribute debe estar presente cuando :values estén presentes.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'prohibited_if' => 'El campo :attribute está prohibido cuando :other sea :value.',
    'prohibited_unless' => 'El campo :attribute está prohibido a menos que :other sea :value.',
    'prohibits' => 'El campo :attribute prohíbe que :other esté presente.',
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_array_keys' => 'El campo :attribute debe contener entradas para: :values.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other sea :value.',
    'required_if_accepted' => 'El campo :attribute es obligatorio cuando :other sea aceptado.',
    'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with' => 'El campo :attribute es obligatorio cuando :values esté presente.',
    'required_with_all' => 'El campo :attribute es obligatorio cuando :values estén presentes.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no esté presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values estén presentes.',
    'same' => 'El campo :attribute y :other deben coincidir.',
    'size' => [
        'array' => 'El campo :attribute debe contener :size elementos.',
        'file' => 'El campo :attribute debe tener :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string' => 'El campo :attribute debe tener :size caracteres.',
    ],
    'starts_with' => 'El campo :attribute debe comenzar con uno de los siguientes: :values.',
    'string' => 'El campo :attribute debe ser una cadena de caracteres.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'El campo :attribute ya ha sido registrado.',
    'uploaded' => 'Subir :attribute ha fallado.',
    'uppercase' => 'El campo :attribute debe estar en mayúsculas.',
    'url' => 'El campo :attribute no es una URL válida.',
    'ulid' => 'El campo :attribute debe ser un ULID válido.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'correo_electronico' => [
            'required' => 'El correo electrónico es obligatorio.',
            'email' => 'Por favor, ingresa un correo electrónico válido.',
            'unique' => 'Este correo electrónico ya está registrado en nuestro sistema.',
        ],
        'password' => [
            'required' => 'La contraseña es obligatoria.',
            'min' => 'La contraseña debe tener al menos :min caracteres.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'mixed' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
            'numbers' => 'La contraseña debe contener al menos un número.',
            'symbols' => 'La contraseña debe contener al menos un símbolo especial.',
        ],
        'nombre_usuario' => [
            'required' => 'El nombre de usuario es obligatorio.',
            'max' => 'El nombre de usuario no puede tener más de :max caracteres.',
            'regex' => 'El nombre de usuario solo puede contener letras y espacios.',
        ],
        'telefono' => [
            'required' => 'El teléfono es obligatorio.',
            'regex' => 'El teléfono debe tener 10 dígitos y comenzar con 3.',
        ],
        'acepta_terminos' => [
            'accepted' => 'Debes aceptar los términos y condiciones para continuar.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'nombre',
        'username' => 'nombre de usuario',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'city' => 'ciudad',
        'country' => 'país',
        'address' => 'dirección',
        'phone' => 'teléfono',
        'mobile' => 'móvil',
        'age' => 'edad',
        'sex' => 'sexo',
        'gender' => 'género',
        'day' => 'día',
        'month' => 'mes',
        'year' => 'año',
        'hour' => 'hora',
        'minute' => 'minuto',
        'second' => 'segundo',
        'title' => 'título',
        'content' => 'contenido',
        'description' => 'descripción',
        'excerpt' => 'extracto',
        'date' => 'fecha',
        'time' => 'hora',
        'available' => 'disponible',
        'size' => 'tamaño',
    ],

]; 