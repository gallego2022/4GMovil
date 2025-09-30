<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ValidationService
{
    /**
     * @var LoggingService
     */
    protected $loggingService;

    /**
     * Constructor
     */
    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    /**
     * Validar datos con reglas específicas
     */
    public function validate(array $data, array $rules, array $messages = []): array
    {
        try {
            $validator = Validator::make($data, $rules, $messages);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $this->loggingService->validationError($errors, ['data' => $data]);
                throw new ValidationException($validator);
            }
            
            return $validator->validated();
        } catch (ValidationException $e) {
            $this->loggingService->error('Error de validación', [
                'errors' => $e->errors(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Validar datos de producto
     */
    public function validateProduct(array $data): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:10000|max:20000000',
            'stock' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'activo' => 'boolean',
            'especificaciones' => 'nullable|array',
            'especificaciones.*.nombre' => 'required|string',
            'especificaciones.*.valor' => 'required|string',
        ];

        $messages = [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'precio.required' => 'El precio del producto es obligatorio',
            'precio.numeric' => 'El precio debe ser un número',
            'precio.min' => 'El precio mínimo es $10,000 COP',
            'precio.max' => 'El precio máximo es $20,000,000 COP',
            'stock.required' => 'El stock es obligatorio',
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock no puede ser negativo',
            'categoria_id.required' => 'La categoría es obligatoria',
            'categoria_id.exists' => 'La categoría seleccionada no existe',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.mimes' => 'La imagen debe ser JPEG, PNG, JPG o WEBP',
            'imagen.max' => 'La imagen no puede superar 2MB',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de usuario
     */
    public function validateUser(array $data): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ];

        $messages = [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe tener un formato válido',
            'email.unique' => 'El email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de checkout
     */
    public function validateCheckout(array $data): array
    {
        $rules = [
            'usuario_id' => 'required|exists:usuarios,id',
            'direccion_envio_id' => 'required|exists:direcciones,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ];

        $messages = [
            'usuario_id.required' => 'El usuario es obligatorio',
            'usuario_id.exists' => 'El usuario no existe',
            'direccion_envio_id.required' => 'La dirección de envío es obligatoria',
            'direccion_envio_id.exists' => 'La dirección de envío no existe',
            'metodo_pago_id.required' => 'El método de pago es obligatorio',
            'metodo_pago_id.exists' => 'El método de pago no existe',
            'productos.required' => 'Debe seleccionar al menos un producto',
            'productos.min' => 'Debe seleccionar al menos un producto',
            'productos.*.producto_id.required' => 'El producto es obligatorio',
            'productos.*.producto_id.exists' => 'El producto no existe',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'productos.*.precio_unitario.required' => 'El precio unitario es obligatorio',
            'productos.*.precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'productos.*.precio_unitario.min' => 'El precio unitario no puede ser negativo',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de inventario
     */
    public function validateInventory(array $data): array
    {
        $rules = [
            'producto_id' => 'required|exists:productos,id',
            'tipo_movimiento' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:255',
            'fecha_movimiento' => 'required|date',
        ];

        $messages = [
            'producto_id.required' => 'El producto es obligatorio',
            'producto_id.exists' => 'El producto no existe',
            'tipo_movimiento.required' => 'El tipo de movimiento es obligatorio',
            'tipo_movimiento.in' => 'El tipo de movimiento debe ser entrada, salida o ajuste',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'motivo.required' => 'El motivo es obligatorio',
            'motivo.max' => 'El motivo no puede superar 255 caracteres',
            'fecha_movimiento.required' => 'La fecha del movimiento es obligatoria',
            'fecha_movimiento.date' => 'La fecha debe tener un formato válido',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de pago
     */
    public function validatePayment(array $data): array
    {
        $rules = [
            'pedido_id' => 'required|exists:pedidos,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'monto' => 'required|numeric|min:0',
            'moneda' => 'required|string|size:3',
            'estado' => 'required|in:pending,completed,failed,cancelled',
            'referencia_externa' => 'nullable|string|max:255',
            'datos_pago' => 'nullable|array',
        ];

        $messages = [
            'pedido_id.required' => 'El pedido es obligatorio',
            'pedido_id.exists' => 'El pedido no existe',
            'metodo_pago_id.required' => 'El método de pago es obligatorio',
            'metodo_pago_id.exists' => 'El método de pago no existe',
            'monto.required' => 'El monto es obligatorio',
            'monto.numeric' => 'El monto debe ser un número',
            'monto.min' => 'El monto no puede ser negativo',
            'moneda.required' => 'La moneda es obligatoria',
            'moneda.size' => 'La moneda debe tener 3 caracteres',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser válido',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de dirección
     */
    public function validateAddress(array $data): array
    {
        $rules = [
            'usuario_id' => 'required|exists:usuarios,id',
            'tipo' => 'required|in:casa,trabajo,otro',
            'calle' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:20',
            'pais' => 'required|string|max:100',
            'es_principal' => 'boolean',
        ];

        $messages = [
            'usuario_id.required' => 'El usuario es obligatorio',
            'usuario_id.exists' => 'El usuario no existe',
            'tipo.required' => 'El tipo de dirección es obligatorio',
            'tipo.in' => 'El tipo debe ser casa, trabajo u otro',
            'calle.required' => 'La calle es obligatoria',
            'numero.required' => 'El número es obligatorio',
            'ciudad.required' => 'La ciudad es obligatoria',
            'estado.required' => 'El estado es obligatorio',
            'codigo_postal.required' => 'El código postal es obligatorio',
            'pais.required' => 'El país es obligatorio',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de carrito
     */
    public function validateCart(array $data): array
    {
        $rules = [
            'usuario_id' => 'required|exists:usuarios,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ];

        $messages = [
            'usuario_id.required' => 'El usuario es obligatorio',
            'usuario_id.exists' => 'El usuario no existe',
            'productos.required' => 'Debe seleccionar al menos un producto',
            'productos.min' => 'Debe seleccionar al menos un producto',
            'productos.*.producto_id.required' => 'El producto es obligatorio',
            'productos.*.producto_id.exists' => 'El producto no existe',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de búsqueda
     */
    public function validateSearch(array $data): array
    {
        $rules = [
            'query' => 'nullable|string|max:255',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio_min' => 'nullable|numeric|min:0',
            'precio_max' => 'nullable|numeric|min:0',
            'ordenar_por' => 'nullable|in:nombre,precio,fecha',
            'orden' => 'nullable|in:asc,desc',
            'pagina' => 'nullable|integer|min:1',
            'por_pagina' => 'nullable|integer|min:1|max:100',
        ];

        $messages = [
            'query.max' => 'La búsqueda no puede superar 255 caracteres',
            'categoria_id.exists' => 'La categoría no existe',
            'precio_min.numeric' => 'El precio mínimo debe ser un número',
            'precio_min.min' => 'El precio mínimo no puede ser negativo',
            'precio_max.numeric' => 'El precio máximo debe ser un número',
            'precio_max.min' => 'El precio máximo no puede ser negativo',
            'ordenar_por.in' => 'El campo de ordenamiento no es válido',
            'orden.in' => 'El orden debe ser ascendente o descendente',
            'pagina.integer' => 'La página debe ser un número entero',
            'pagina.min' => 'La página debe ser mayor a 0',
            'por_pagina.integer' => 'El número por página debe ser un entero',
            'por_pagina.min' => 'El número por página debe ser mayor a 0',
            'por_pagina.max' => 'El número por página no puede superar 100',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de filtro
     */
    public function validateFilter(array $data): array
    {
        $rules = [
            'filtros' => 'nullable|array',
            'filtros.*.campo' => 'required|string',
            'filtros.*.operador' => 'required|in:equals,contains,greater_than,less_than,between,in',
            'filtros.*.valor' => 'required',
            'filtros.*.valor.*' => 'nullable',
        ];

        $messages = [
            'filtros.array' => 'Los filtros deben ser un array',
            'filtros.*.campo.required' => 'El campo del filtro es obligatorio',
            'filtros.*.operador.required' => 'El operador del filtro es obligatorio',
            'filtros.*.operador.in' => 'El operador del filtro no es válido',
            'filtros.*.valor.required' => 'El valor del filtro es obligatorio',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de archivo
     */
    public function validateFile(array $data): array
    {
        $rules = [
            'archivo' => 'required|file',
            'tipo' => 'required|in:imagen,documento,archivo',
            'tamaño_maximo' => 'nullable|integer|min:1',
            'extensiones_permitidas' => 'nullable|array',
            'extensiones_permitidas.*' => 'string',
        ];

        $messages = [
            'archivo.required' => 'El archivo es obligatorio',
            'archivo.file' => 'El archivo debe ser válido',
            'tipo.required' => 'El tipo de archivo es obligatorio',
            'tipo.in' => 'El tipo de archivo no es válido',
            'tamaño_maximo.integer' => 'El tamaño máximo debe ser un número entero',
            'tamaño_maximo.min' => 'El tamaño máximo debe ser mayor a 0',
        ];

        return $this->validate($data, $rules, $messages);
    }

    /**
     * Validar datos de paginación
     */
    public function validatePagination(array $data): array
    {
        $rules = [
            'pagina' => 'nullable|integer|min:1',
            'por_pagina' => 'nullable|integer|min:1|max:100',
            'ordenar_por' => 'nullable|string',
            'orden' => 'nullable|in:asc,desc',
        ];

        $messages = [
            'pagina.integer' => 'La página debe ser un número entero',
            'pagina.min' => 'La página debe ser mayor a 0',
            'por_pagina.integer' => 'El número por página debe ser un entero',
            'por_pagina.min' => 'El número por página debe ser mayor a 0',
            'por_pagina.max' => 'El número por página no puede superar 100',
            'orden.in' => 'El orden debe ser ascendente o descendente',
        ];

        return $this->validate($data, $rules, $messages);
    }
}
