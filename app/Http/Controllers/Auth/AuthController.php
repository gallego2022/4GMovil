<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\WebController;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuario;
use App\Models\OtpCode;

class AuthController extends WebController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Esta función muestra el formulario de inicio de sesión
    public function index()
    {
        $this->applyLocalization();
        return View::make('modules.auth.login');
    }

    // Esta funcion muestra el formulario para cambiar contraseña
    public function formCambiarContrasena()
    {
        $this->applyLocalization();
        return View::make('modules.auth.cambiar-contrasena');
    }

    // Funcion para cambiar contraseña
    public function cambiarContrasena(Request $request)
    {
        $request->validate([
            'contrasena_actual' => [
                'required',
                'string',
                'min:8'
            ],
            'nueva_contrasena' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:contrasena_actual',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'nueva_contrasena_confirmation' => 'required'
        ]);

        $result = $this->authService->cambiarContrasena($request);

            if ($request->expectsJson()) {
            return Response::json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return $this->redirectSuccess('perfil', $result['message']);
        }

        return $this->backWithInput($result['message']);
    }

    // Funcion para registrar un nuevo usuario
    public function registrar(Request $request)
    {
        $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:25', 'regex:/^[\pL\s]+$/u'],
            'correo_electronico' => 'required|email|unique:usuarios,correo_electronico',
            'contrasena' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'telefono' => ['required', 'regex:/^3\d{9}$/'],
            'acepta_terminos' => 'accepted',
        ], [
            'nombre_usuario.required' => trans('auth.validation.nombre_usuario.required'),
            'nombre_usuario.max' => trans('auth.validation.nombre_usuario.max'),
            'nombre_usuario.regex' => trans('auth.validation.nombre_usuario.regex'),
            'correo_electronico.required' => trans('auth.validation.correo_electronico.required'),
            'correo_electronico.email' => trans('auth.validation.correo_electronico.email'),
            'correo_electronico.unique' => trans('auth.validation.correo_electronico.unique'),
            'telefono.required' => trans('auth.validation.telefono.required'),
            'telefono.regex' => trans('auth.validation.telefono.regex'),
            'contrasena.required' => trans('auth.validation.contrasena.required'),
            'contrasena.min' => trans('auth.validation.contrasena.min'),
            'contrasena.confirmed' => trans('auth.validation.contrasena.confirmed'),
            'contrasena.regex' => trans('auth.validation.contrasena.regex'),
            'acepta_terminos.accepted' => trans('auth.validation.acepta_terminos.accepted'),
        ]);

        try {
            $result = $this->authService->registrar($request);

            if ($result['success']) {
            return Redirect::route('otp.verify.register')
                    ->with('verification_email', $result['usuario']->correo_electronico)
                    ->with('mensaje', $result['message'])
                ->with('registro_exitoso', trans('auth.register_success'));
            }

            return $this->backWithInput($result['message']);

        } catch (\Exception $e) {
            Log::error('Error en registro: ' . $e->getMessage());
            return $this->backWithInput(trans('messages.error'));
        }
    }

    // Funcion para iniciar sesión
    public function logear(Request $request)
    {
        $request->validate([
            'correo_electronico' => 'required|email',
            'contrasena' => 'required|string'
        ]);

        try {
            $result = $this->authService->logear($request);

            if ($result['success']) {
                if ($result['usuario']->rol === 'admin') {
                return Redirect::route('admin.index')
                    ->with('status', trans('auth.login_success'))
                    ->with('status_type', 'success');
            } else {
                return Redirect::route('landing')
                    ->with('status', trans('auth.login_success'))
                    ->with('status_type', 'success');
            }
        }

            // Manejar diferentes tipos de error
            switch ($result['error_type']) {
                case 'google_account':
                    return Redirect::back()
                        ->with('error_login', $result['message'])
                        ->withInput(['correo_electronico' => $request->correo_electronico]);
                
                case 'inactive_account':
                    return Redirect::back()
                        ->with('error_login', $result['message'])
                        ->withInput(['correo_electronico' => $request->correo_electronico]);
                
                case 'unverified_email':
                    return Redirect::route('otp.verify.register')
                        ->with('verification_email', $result['usuario']->correo_electronico)
                        ->with('error_login', $result['message'])
                        ->withInput(['correo_electronico' => $request->correo_electronico]);
                
                default:
                    return Redirect::back()
                        ->with('error_login', $result['message'])
                        ->withInput(['correo_electronico' => $request->correo_electronico]);
            }

        } catch (\Exception $e) {
            Log::error('Error en login: ' . $e->getMessage());
        return Redirect::back()
            ->with('error_login', trans('auth.login_error'))
            ->withInput(['correo_electronico' => $request->correo_electronico]);
        }
    }

    // Esta función cierra la sesión del usuario
    public function logout(Request $request)
    {
        try {
            $result = $this->authService->logout($request);
        
            if ($result['success']) {
                return redirect()
                    ->route('landing')
                    ->with('status', $result['message'])
                    ->with('status_type', 'info');
            }

            return $this->backError($result['message']);

        } catch (\Exception $e) {
            Log::error('Error en logout: ' . $e->getMessage());
            return $this->backError(trans('auth.logout_error'));
        }
    }

    // Esta función muestra el perfil del usuario autenticado
    public function perfil()
    {
        try {
            $result = $this->authService->getPerfil();
            
            if ($result['success']) {
                $usuario = $result['usuario'];
                return View::make($result['view'], compact('usuario'));
            }

            return $this->backError($result['message']);

        } catch (\Exception $e) {
            Log::error('Error al obtener perfil: ' . $e->getMessage());
            return $this->backError('Error al cargar el perfil');
        }
    }

    // Esta función actualiza el perfil del usuario autenticado
    public function actualizarPerfil(Request $request)
    {
        $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:25', 'regex:/^[\pL\s]+$/u'],
            'correo_electronico' => 'required|email|unique:usuarios,correo_electronico,' . Auth::id() . ',usuario_id',
            'telefono' => ['nullable', 'regex:/^3\d{9}$/'],
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
        ]);

        try {
            $result = $this->authService->actualizarPerfil($request);

            if ($result['success']) {
                return $this->backSuccess($result['message']);
            }

            return $this->backWithInput($result['message']);

        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil: ' . $e->getMessage());
            return $this->backWithInput(trans('profile.update_error'));
        }
    }
    // Eliminar foto
    public function eliminarFoto()
    {
        try {
            $result = $this->authService->eliminarFoto();

            if ($result['success']) {
                return Response::json([
                    'tipo' => 'success',
                    'mensaje' => $result['message']
                ]);
            }

                return Response::json([
                    'tipo' => 'error',
                'mensaje' => $result['message']
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar la foto de perfil: ' . $e->getMessage());
            
            return Response::json([
                'tipo' => 'error',
                'mensaje' => 'Error interno del servidor al eliminar la foto'
            ], 500);
        }
    }

    public function validarContrasenaActual(Request $request)
    {
        try {
            $result = $this->authService->validarContrasenaActual($request);

            if ($result['success']) {
                return Response::json([
                    'valid' => $result['valid'],
                    'message' => $result['message']
                ]);
            }

            return Response::json([
                'valid' => false,
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            Log::error('Error al validar contraseña: ' . $e->getMessage());
            
            return Response::json([
                'valid' => false,
                'message' => 'Error al validar la contraseña'
            ], 500);
        }
    }

    // ===== MÉTODOS DE RECUPERACIÓN DE CONTRASEÑA (Consolidados desde ForgotPasswordController) =====

    /**
     * Mostrar el formulario de solicitud de restablecimiento de contraseña con OTP
     */
    public function showLinkRequestForm()
    {
        // Al abrir el flujo de recuperación, limpiar correo persistido en sesión
        Session::forget('email_sent');
        return View::make('auth.passwords.email-otp');
    }

    /**
     * Enviar código OTP para restablecimiento de contraseña
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,correo_electronico',
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.exists' => 'No encontramos una cuenta registrada con ese correo electrónico.',
        ]);

        try {
            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            // Verificar si ya tiene un código válido
            if (OtpCode::tieneCodigoValido($usuario->usuario_id, 'password_reset')) {
                $otp = OtpCode::obtenerCodigoValido($usuario->usuario_id, 'password_reset');
                $tiempoRestante = $otp->tiempoRestante();
                
                // Si han pasado menos de 1 minuto desde la última solicitud, permitir invalidar
                if (OtpCode::puedeSolicitarNuevoCodigo($usuario->usuario_id, 'password_reset', 1)) {
                    // Invalidar códigos existentes y continuar
                    OtpCode::invalidarCodigosExistentes($usuario->usuario_id, 'password_reset');
                    Log::info("Códigos OTP anteriores invalidados para: {$usuario->correo_electronico}");
                } else {
                    // Si es una petición AJAX, devolver JSON
                    if ($request->expectsJson()) {
                        return Response::json([
                            'success' => false,
                            'message' => "Ya tienes un código válido. Espera {$tiempoRestante} minutos para solicitar uno nuevo."
                        ], 429);
                    }
                    
                    return Redirect::back()->withErrors([
                        'email' => "Ya tienes un código válido. Espera {$tiempoRestante} minutos para solicitar uno nuevo."
                    ]);
                }
            }

            // Crear nuevo código OTP
            $otp = OtpCode::crear($usuario->usuario_id, 'password_reset', 10);

            // Enviar correo
            Mail::to($usuario->correo_electronico)
                ->send(new \App\Mail\OtpVerification($usuario, $otp->codigo, 'password_reset', 10));

            Log::info("Código OTP para restablecimiento enviado a: {$usuario->correo_electronico}");

            // Crear un token temporal para el email
            $tempToken = base64_encode($request->email . '|' . time());
            
            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson()) {
                return Response::json([
                    'success' => true,
                    'message' => '¡Código OTP enviado! Revisa tu correo electrónico para continuar.',
                    'email_sent' => $request->email,
                    'temp_token' => $tempToken
                ]);
            }
            
            return Redirect::route('password.reset.otp')->with([
                'status' => '¡Código OTP enviado! Revisa tu correo electrónico para continuar.',
                'email_sent' => $request->email,
                'temp_token' => $tempToken
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando OTP de restablecimiento: ' . $e->getMessage());
            
            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Error al enviar el código OTP. Intenta nuevamente.'
                ], 500);
            }
            
            return Redirect::back()->withErrors([
                'email' => 'Error al enviar el código OTP. Intenta nuevamente.'
            ]);
        }
    }

    // ===== MÉTODOS DE GOOGLE OAUTH (Consolidados desde GoogleController) =====

    /**
     * Redirige al usuario a Google para autenticación
     */
    public function redirectToGoogle()
    {
        try {
            // Verificar configuración de Google
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            
            if (empty($clientId) || $clientId === 'your-google-client-id-here') {
                Log::error('Google OAuth no configurado correctamente');
                return Redirect::route('login')->with('error', 'Google OAuth no está configurado. Contacta al administrador.');
            }
            
            if (empty($clientSecret) || $clientSecret === 'your-google-client-secret-here') {
                Log::error('Google OAuth secret no configurado');
                return Redirect::route('login')->with('error', 'Google OAuth no está configurado. Contacta al administrador.');
            }
            
            $url = Socialite::driver('google')
                ->redirect()
                ->getTargetUrl();
                
            // Agregar parámetro para forzar selección de cuenta
            $url .= '&prompt=select_account';
                
            Log::info('Redirigiendo a Google OAuth: ' . $url);
            return redirect($url);
        } catch (\Exception $e) {
            Log::error('Error al redirigir a Google: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return Redirect::route('login')->with('error', 'Error al conectar con Google. Verifica la configuración.');
        }
    }

    /**
     * Maneja la respuesta de Google después de la autenticación
     */
    public function handleGoogleCallback()
    {
        try {
            // Verificar que no hay errores en la URL
            if (request()->has('error')) {
                $error = request()->get('error');
                Log::error('Error de Google OAuth: ' . $error);
                return Redirect::route('login')->with('error', 'Error de autenticación con Google: ' . $error);
            }
            
            $googleUser = Socialite::driver('google')->user();
            
            Log::info("Google callback recibido para: " . $googleUser->getEmail());
            Log::info("Google ID: " . $googleUser->getId());
            Log::info("Google Name: " . $googleUser->getName());
            
            // Buscar usuario existente por google_id o email
            $usuario = Usuario::where('google_id', $googleUser->getId())
                ->orWhere('correo_electronico', $googleUser->getEmail())
                ->first();
            
            if ($usuario) {
                Log::info("Usuario encontrado en BD: " . $usuario->correo_electronico);
                Log::info("Usuario google_id: " . $usuario->google_id);
                Log::info("Usuario estado: " . $usuario->estado);
                
                // CASO 1: Usuario existe con google_id
                if ($usuario->google_id === $googleUser->getId()) {
                    Log::info("Usuario existente con google_id: {$usuario->correo_electronico}");
                    
                    // Actualizar foto de perfil si es de Google
                    if (\App\Helpers\PhotoHelper::isGooglePhotoUrl($usuario->foto_perfil)) {
                        $usuario->foto_perfil = \App\Helpers\PhotoHelper::cleanGooglePhotoUrl($googleUser->getAvatar());
                        $usuario->save();
                    }
                    
                    Auth::login($usuario);
                    Log::info("Auth::login ejecutado para: " . $usuario->correo_electronico);
                    Log::info("Usuario autenticado: " . (Auth::check() ? 'SÍ' : 'NO'));
                    Log::info("Usuario actual: " . (Auth::user() ? Auth::user()->correo_electronico : 'NONE'));
                    
                    Log::info("Usuario existente logueado con Google: {$usuario->correo_electronico}");
                    return Redirect::intended('/perfil')->with('success', '¡Bienvenido de vuelta!');
                }
                
                // CASO 2: Usuario existe con email pero sin google_id (convertir a Google)
                if ($usuario->correo_electronico === $googleUser->getEmail() && !$usuario->google_id) {
                    Log::info("Convirtiendo usuario existente a Google: {$usuario->correo_electronico}");
                    
                    $usuario->google_id = $googleUser->getId();
                    $usuario->foto_perfil = \App\Helpers\PhotoHelper::cleanGooglePhotoUrl($googleUser->getAvatar());
                    $usuario->email_verified_at = Carbon::now();
                    $usuario->save();
                    
                    Auth::login($usuario);
                    Log::info("Auth::login ejecutado para usuario convertido: " . $usuario->correo_electronico);
                    Log::info("Usuario autenticado: " . (Auth::check() ? 'SÍ' : 'NO'));
                    
                    Log::info("Usuario convertido a Google: {$usuario->correo_electronico}");
                    return Redirect::intended('/perfil')->with('success', '¡Cuenta vinculada con Google exitosamente!');
                }
                
                // CASO 3: Conflicto - google_id diferente (puede pasar en desarrollo)
                if ($usuario->google_id && $usuario->google_id !== $googleUser->getId()) {
                    Log::warning("Conflicto de google_id para: {$usuario->correo_electronico}");
                    
                    // En desarrollo, permitir sobrescribir
                    if (app()->environment('local', 'development')) {
                        $usuario->google_id = $googleUser->getId();
                        $usuario->foto_perfil = \App\Helpers\PhotoHelper::cleanGooglePhotoUrl($googleUser->getAvatar());
                        $usuario->save();
                        
                        Auth::login($usuario);
                        Log::info("Auth::login ejecutado para conflicto resuelto: " . $usuario->correo_electronico);
                        Log::info("Usuario autenticado: " . (Auth::check() ? 'SÍ' : 'NO'));
                        
                        Log::info("Conflicto resuelto en desarrollo para: {$usuario->correo_electronico}");
                        return Redirect::intended('/perfil')->with('success', '¡Bienvenido! Conflicto resuelto.');
                    } else {
                        return Redirect::route('login')->with('error', 'Esta cuenta ya está asociada con otra cuenta de Google.');
                    }
                }
            } else {
                // CASO 4: Nuevo usuario
                Log::info("Creando nuevo usuario de Google: {$googleUser->getEmail()}");
                
                $usuario = Usuario::create([
                    'nombre_usuario' => $googleUser->getName() ?: explode('@', $googleUser->getEmail())[0],
                    'correo_electronico' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'contrasena' => null, // Sin contraseña para usuarios de Google
                    'foto_perfil' => \App\Helpers\PhotoHelper::cleanGooglePhotoUrl($googleUser->getAvatar()),
                    'estado' => true,
                    'rol' => 'cliente',
                    'fecha_registro' => Carbon::now(),
                    'email_verified_at' => Carbon::now(), // Google ya verificó el email
                ]);
                
                Auth::login($usuario);
                Log::info("Auth::login ejecutado para nuevo usuario: " . $usuario->correo_electronico);
                Log::info("Usuario autenticado: " . (Auth::check() ? 'SÍ' : 'NO'));
                
                Log::info("Nuevo usuario creado con Google: {$usuario->correo_electronico}");
                
                // Devolver JSON para mostrar modal
                if (request()->expectsJson()) {
                    return Response::json([
                        'success' => true,
                        'message' => '¡Cuenta creada exitosamente!',
                        'redirect' => '/perfil',
                        'showPasswordModal' => true
                    ]);
                } else {
                    // Para navegadores normales, redirigir directamente
                    return Redirect::route('perfil')->with('success', '¡Cuenta creada exitosamente!');
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error en callback de Google: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return Redirect::route('login')->with('error', 'Error al autenticarse con Google. Inténtalo de nuevo.');
        }
    }

    // ===== MÉTODOS DE CONTRASEÑA PARA GOOGLE (Consolidados desde GooglePasswordController) =====

    /**
     * Establece la contraseña para el usuario de Google
     */
    public function setPassword(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return Response::json(['error' => 'No autenticado'], 401);
            }
            return Redirect::route('login');
        }

        $usuario = Auth::user();

        // Validar la solicitud
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un símbolo.',
        ]);

        try {
            // Establecer la contraseña
            $usuario->contrasena = Hash::make($request->password);
            $usuario->save();

            Log::info("Contraseña establecida para usuario de Google: {$usuario->correo_electronico}");

            if ($request->expectsJson()) {
                return Response::json([
                    'success' => true,
                    'message' => '¡Contraseña establecida exitosamente! Ahora puedes hacer login manual con tu correo y contraseña.',
                    'redirect' => route('perfil')
                ]);
            }

            return Redirect::route('perfil')
                ->with('success', '¡Contraseña establecida exitosamente! Ahora puedes hacer login manual con tu correo y contraseña.');

        } catch (\Exception $e) {
            Log::error('Error al establecer contraseña para usuario de Google: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
        return Response::json([
                    'error' => 'Error al establecer la contraseña. Inténtalo de nuevo.'
                ], 500);
            }
            
            return Redirect::back()
                ->withErrors(['error' => 'Error al establecer la contraseña. Inténtalo de nuevo.'])
                ->withInput();
        }
    }

    // ===== MÉTODOS DE RESTABLECIMIENTO DE CONTRASEÑA (Consolidados desde ResetPasswordController) =====

    /**
     * Mostrar el formulario de restablecimiento de contraseña con OTP
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = null;
        
        // Si hay un token, intentar decodificarlo
        if ($token && $token !== 'otp') {
            try {
                $decoded = base64_decode($token);
                if ($decoded && strpos($decoded, '|') !== false) {
                    list($emailFromToken, $timestamp) = explode('|', $decoded, 2);
                    
                    // Verificar que el token no sea muy antiguo (máximo 1 hora)
                    if (time() - $timestamp < 3600) {
                        $email = $emailFromToken;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error decodificando token: ' . $e->getMessage());
            }
        }
        
        // Si no hay email del token, intentar otras fuentes
        if (!$email) {
            $email = $request->query('email') ?? $request->email ?? session('email_sent');
        }
        
        // Debug: Log para ver qué está pasando
        Log::info('ResetPasswordController - Email sources:', [
            'token' => $token,
            'query_email' => $request->query('email'),
            'request_email' => $request->email,
            'session_email' => session('email_sent'),
            'final_email' => $email
        ]);
        
        // Si no hay email, redirigir al formulario de solicitud
        if (!$email) {
            return Redirect::route('password.request')
                ->withErrors(['email' => 'Debes solicitar un código OTP primero.']);
        }
        
        // Guardar el email en la sesión para mantenerlo durante el proceso
        session(['email_sent' => $email]);
        
        return View::make('auth.passwords.reset-otp')->with([
            'email' => $email
        ]);
    }

    /**
     * Restablecer la contraseña usando OTP
     */
    public function reset(Request $request)
    {
        // Validar que el email esté presente y sea válido
        if (!$request->filled('email')) {
            return Redirect::back()
                ->withInput($request->only('otp_code'))
                ->withErrors(['email' => 'El campo correo electrónico es obligatorio.']);
        }

        $request->validate([
            'email' => 'required|email|exists:usuarios,correo_electronico',
            'otp_code' => 'required|string|size:6',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.exists' => 'No encontramos una cuenta registrada con ese correo electrónico.',
            'otp_code.required' => 'El código OTP es obligatorio.',
            'otp_code.size' => 'El código OTP debe tener 6 dígitos.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.mixed_case' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un carácter especial.',
        ]);

        try {
            $usuario = Usuario::where('correo_electronico', $request->email)->first();
            
            if (!$usuario) {
                return Redirect::back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Usuario no encontrado.']);
            }

            // Verificar código OTP
            if (!OtpCode::verificar($usuario->usuario_id, $request->otp_code, 'password_reset')) {
                return Redirect::back()
                    ->withInput($request->only('email'))
                    ->withErrors(['otp_code' => 'Código OTP inválido o expirado.']);
            }

            // Validar que la nueva contraseña no sea igual a la anterior
            if (!empty($usuario->contrasena) && Hash::check($request->password, $usuario->contrasena)) {
                return Redirect::back()
                    ->withInput($request->only('email'))
                    ->withErrors(['password' => 'Utiliza una contraseña diferente.']);
            }

            // Actualizar contraseña
            $usuario->forceFill([
                'contrasena' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            Log::info("Contraseña restablecida exitosamente para: {$usuario->correo_electronico}");

            // Limpiar el email almacenado en sesión para no prellenar futuros intentos
            Session::forget('email_sent');

            return Redirect::route('login')
                ->with('status', '¡Tu contraseña ha sido restablecida exitosamente! Ahora puedes iniciar sesión con tu nueva contraseña.')
                ->with('status_type', 'success');

        } catch (\Exception $e) {
            Log::error('Error restableciendo contraseña: ' . $e->getMessage());
            
            return Redirect::back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Error al restablecer la contraseña. Intenta nuevamente.']);
        }
    }
}
