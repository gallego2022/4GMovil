<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta-description')">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/style_inicio.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }

        /* Estilos para el carrito lateral */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            background-color: white;
            margin: 2rem auto;
            padding: 1rem;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-body {
            padding: 1rem 0;
            max-height: 60vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
            padding: 0.5rem;
            transition: color 0.2s;
        }

        .close:hover {
            color: #1f2937;
        }

        /* Animaciones del modal */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-10%); }
            to { transform: translateY(0); }
        }

        .modal.show {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        .modal.show .modal-content {
            animation: slideIn 0.3s ease-out;
        }

        /* Estilo para el scroll del carrito */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        @media (max-width: 640px) {
            .modal-content {
                width: 100%;
                right: -100%;
            }
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div class="bg-primary text-white py-2 px-4 flex justify-between items-center text-sm">
        <div class="flex items-center space-x-4">
            <span><i class="fas fa-phone-alt mr-2"></i> +57 320 123 4567</span>
            <span><i class="fas fa-envelope mr-2"></i> info@4gmovil.com.co</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="https://www.facebook.com/cuatro.g.movil.2025/" class="text-white hover:text-warning" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/tumovil4g/?fbclid=IwY2xjawJClENleHRuA2FlbQIxMAABHfgoRN_xkx-QlgRi5XJX_YY8IVnmcJTaee4R2UWXoOMJTTip9ml-DYoVXw_aem_-9MriOuo88DFcLIGBeizRw" class="text-white hover:text-warning" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://api.whatsapp.com/send/?phone=573117337272&text&type=phone_number&app_absent=0" class="text-white hover:text-warning" target="_blank"><i class="fab fa-whatsapp"></i></a>
            <a href="https://www.tiktok.com/@tumovil4g" class="text-white hover:text-warning" target="_blank"><i class="fab fa-tiktok"></i></a>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar bg-white sticky top-0 z-50 shadow-sm">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('landing') }}" class="text-2xl font-bold text-primary">
                    <img src="{{ asset('storage/imagenes/Logo_2.png') }}" alt="4GMovil " class="h-13">
                </a>
            </div>

            <div class="navbar-menu hidden md:flex items-center space-x-6">
                <a href="{{ route('landing') }}" class="text-dark hover:text-primary font-medium">Inicio</a>

                <div class="dropdown relative">
                    <button class="text-dark hover:text-primary font-medium flex items-center">
                       <a href="{{ route('productos.lista') }}"> Productos <i class="fas fa-chevron-down ml-1 text-sm"></i></a>
                    </button>
                    <div class="dropdown-menu absolute hidden bg-white mt-2 py-2 w-48 rounded-md shadow-lg z-50">
                        <a href="{{ route('productos.lista') }}" class="block px-4 py-2 text-gray-800 hover:bg-blue-50">Celulares</a>
                        <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-blue-50">Accesorios</a>
                        <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-blue-50">Servicio Tecnico </a>
                    </div>
                </div>

                <a href="#" class="text-dark hover:text-primary font-medium">Servicios</a>
                <a href="{{ route('nosotros') }}" class="text-dark hover:text-primary font-medium">Nosotros</a>
                <a href="{{ route('contactanos') }}" class="text-dark hover:text-primary font-medium">Contacto</a>
            </div>

            <div class="flex items-center space-x-4">
                <a href="#" class="text-dark hover:text-primary"><i class="fas fa-search"></i></a>
                @auth
                    <div class="relative group" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button @click="open = !open" class="text-dark hover:text-primary flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            <span class="text-sm">Mi Perfil</span>
                        </button>
                        <div x-show="open" 
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('perfil') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out">
                                <i class="fas fa-user-circle mr-2"></i> Ver Perfil
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="block">
                                @csrf
                                <button type="button" 
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-dark hover:text-primary">
                        <i class="fas fa-user"></i>
                    </a>
                @endauth
                <a href="#" id="cart-btn" class="text-dark hover:text-primary relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count" class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                </a>
                <button class="mobile-menu-btn md:hidden text-dark">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')

        <footer class="footer py-12">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Column 1 -->
                    <div>
                        <p class="text-gray-400 mb-4">Venta y reparación de celulares con la mejor calidad y garantía en el mercado.</p>
                        <div class="flex">
                            <a href="https://www.facebook.com/cuatro.g.movil.2025/" class="social-icon" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.instagram.com/tumovil4g/?fbclid=IwY2xjawJClENleHRuA2FlbQIxMAABHfgoRN_xkx-QlgRi5XJX_YY8IVnmcJTaee4R2UWXoOMJTTip9ml-DYoVXw_aem_-9MriOuo88DFcLIGBeizRw" class="social-icon" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="https://api.whatsapp.com/send/?phone=573117337272&text&type=phone_number&app_absent=0" class="social-icon" target="_blank"><i class="fab fa-whatsapp"></i></a>
                            <a href="https://www.tiktok.com/@tumovil4g" class="social-icon" target="_blank"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div>
                        <h3 class="text-white text-lg font-bold mb-4">Enlaces rápidos</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('landing') }}" class="text-gray-400 hover:text-white">Inicio</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Productos</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Servicios</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Nosotros</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Contacto</a></li>
                        </ul>
                    </div>

                    <!-- Column 3 -->
                    <div>
                        <h3 class="text-white text-lg font-bold mb-4">Productos</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Celulares</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Accesorios</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Repuestos</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Ofertas</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Nuevos lanzamientos</a></li>
                        </ul>
                    </div>

                    <!-- Column 4 -->
                    <div>
                        <h3 class="text-white text-lg font-bold mb-4">Contacto</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                                <span>Calle 123 #45-67, Bogotá, Colombia</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-phone-alt mr-3"></i>
                                <span>+57 320 123 4567</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-envelope mr-3"></i>
                                <span>info@4gmovil.com.co</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-clock mr-3"></i>
                                <span>Lun-Vie: 9am - 7pm / Sáb: 9am - 2pm</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                    <div class="mt-4">
                        <img src="{{ asset('storage/imagenes/Logo_2.png') }}" alt="Métodos de pago" class="h-13 mx-auto">
                    </div>
                    <br>
                    <p>© 2025 4GMovil. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>

        <!-- Cart Modal -->
        <div id="cart-modal" class="modal" role="dialog" aria-modal="true">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="text-xl font-bold">Tu Carrito</h2>
                    <button class="close" aria-label="Cerrar carrito">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div id="cart-items" class="space-y-4">
                        <!-- Cart items will be added here dynamically -->
                        <p class="text-gray-600 text-center py-4">Tu carrito está vacío</p>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal" class="font-medium">$0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold">Total:</span>
                            <span id="cart-total" class="text-lg font-bold text-primary">$0</span>
                        </div>
                        <form id="checkout-form" action="{{ route('checkout.index') }}" method="POST" style="width: 100%;">
                            @csrf
                            <input type="hidden" name="cart" id="cart-data">
                            <button type="submit" id="checkout-btn" 
                                    class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    role="button"
                                    aria-disabled="true">
                                Finalizar Compra
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- WhatsApp flotante -->
        <div class="fixed bottom-6 left-6 z-50 group">
            <a id="btnWhatsApp" 
               href="https://wa.me/573001234567" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="Chatear por WhatsApp"
               class="bg-green-500 hover:bg-green-600 text-white w-14 h-14 flex items-center justify-center rounded-full shadow-lg transition-opacity duration-300 opacity-100 visible">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                    <path d="M12.04 2.004a10 10 0 0 0-8.723 14.942l-1.34 4.888 5.014-1.313A10 10 0 1 0 12.04 2.004Zm.007 18.005a8.006 8.006 0 0 1-4.116-1.152l-.293-.174-2.974.78.792-2.905-.19-.3a7.992 7.992 0 1 1 6.781 3.75Zm4.294-5.893c-.234-.117-1.383-.683-1.596-.76-.213-.078-.368-.117-.523.117-.155.233-.6.76-.736.915-.135.156-.271.175-.506.058-.234-.117-.99-.364-1.885-1.161-.696-.618-1.165-1.38-1.3-1.615-.136-.234-.015-.36.102-.476.105-.104.234-.271.351-.406.118-.135.156-.233.234-.389.078-.156.039-.292-.02-.409-.058-.117-.523-1.262-.716-1.73-.188-.453-.38-.39-.523-.397l-.445-.007c-.155 0-.408.058-.622.292s-.816.797-.816 1.945.836 2.256.952 2.413c.117.155 1.643 2.507 3.982 3.516.557.24.991.383 1.33.489.559.178 1.067.152 1.47.093.448-.067 1.383-.565 1.577-1.112.194-.546.194-1.014.135-1.112-.058-.098-.213-.155-.448-.272Z"/>
                </svg>
            </a>
            <!-- Tooltip -->
            <div class="absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 rounded bg-gray-800 text-white text-sm opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                Chatea con nosotros
            </div>
        </div>

       <!-- Botón Volver Arriba -->
       <button id="btnUp" 
                title="Volver arriba" 
                aria-label="Volver al inicio" 
                class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white text-xl p-3 rounded-full shadow-lg transition-all duration-500 ease-in-out opacity-0 invisible z-50 transform translate-y-2 hover:scale-110">
            ↑
        </button>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/tu-codigo.js" crossorigin="anonymous"></script>
    
    @include('layouts.partials.sweet-alerts')


    <script type="module" src="{{ asset('js/components/main.js') }}"></script>

    <!-- Script del carrito -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Inicio del script'); // Debug
            
            // Variables globales
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            console.log('Cart inicial:', cart); // Debug
            
            // Funciones del carrito
            function updateCartCount() {
                console.log('Actualizando contador del carrito'); // Debug
                const cartCount = document.getElementById('cart-count');
                if (!cartCount) {
                    console.error('Elemento cart-count no encontrado');
                    return;
                }
                const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                cartCount.textContent = totalItems;
                console.log('Total items:', totalItems); // Debug
            }

            function numberFormat(number) {
                return new Intl.NumberFormat('es-CO').format(number);
            }

            function updateCartDisplay() {
                console.log('Actualizando display del carrito'); // Debug
                const cartItems = document.getElementById('cart-items');
                const cartSubtotal = document.getElementById('cart-subtotal');
                const cartTotal = document.getElementById('cart-total');
                const checkoutBtn = document.getElementById('checkout-btn');
                const cartData = document.getElementById('cart-data');
                
                if (!cartItems || !cartSubtotal || !cartTotal || !checkoutBtn || !cartData) {
                    console.error('Elementos del carrito no encontrados');
                    return;
                }
                
                if (cart.length === 0) {
                    cartItems.innerHTML = '<p class="text-gray-600 text-center py-4">Tu carrito está vacío</p>';
                    cartSubtotal.textContent = '$0';
                    cartTotal.textContent = '$0';
                    checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    checkoutBtn.setAttribute('aria-disabled', 'true');
                    cartData.value = '';
                    return;
                }

                // Actualizar el campo oculto con los datos del carrito
                cartData.value = JSON.stringify(cart);

                let html = '<div class="space-y-4">';
                let subtotal = 0;

                cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    
                    html += `
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-grow">
                                <h4 class="font-medium text-gray-900">${item.name}</h4>
                                <div class="flex items-center gap-4 mt-2">
                                    <div class="flex items-center gap-2">
                                        <button onclick="updateQuantity(${item.id}, -1)" class="text-gray-500 hover:text-primary">
                                            <i class="fas fa-minus-circle"></i>
                                        </button>
                                        <span class="text-gray-600 w-8 text-center">${item.quantity}</span>
                                        <button onclick="updateQuantity(${item.id}, 1)" class="text-gray-500 hover:text-primary">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    </div>
                                    <span class="text-primary font-medium">$${numberFormat(itemTotal)}</span>
                                </div>
                            </div>
                            <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 p-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                });

                html += '</div>';
                cartItems.innerHTML = html;
                cartSubtotal.textContent = `$${numberFormat(subtotal)}`;
                cartTotal.textContent = `$${numberFormat(subtotal)}`;
                checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                checkoutBtn.setAttribute('aria-disabled', 'false');
            }

            function addToCart(product) {
                console.log('Agregando al carrito:', product); // Debug
                
                // Asegurarse de que el ID sea un número
                const productId = parseInt(product.id);
                if (isNaN(productId)) {
                    console.error('ID de producto inválido:', product.id);
                    return;
                }

                const existingProduct = cart.find(item => item.id === productId);
                
                if (existingProduct) {
                    existingProduct.quantity++;
                } else {
                    cart.push({
                        id: productId,
                        name: product.name,
                        price: parseFloat(product.price),
                        quantity: 1
                    });
                }
                
                localStorage.setItem('cart', JSON.stringify(cart));
                console.log('Cart actualizado:', cart); // Debug
                updateCartCount();
                updateCartDisplay();

                Swal.fire({
                    title: '¡Producto agregado!',
                    text: 'El producto se agregó al carrito exitosamente',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }

            function updateQuantity(productId, change) {
                console.log('Actualizando cantidad:', { productId, change }); // Debug
                const product = cart.find(item => item.id === productId);
                if (product) {
                    product.quantity += change;
                    if (product.quantity <= 0) {
                        removeFromCart(productId);
        } else {
                        localStorage.setItem('cart', JSON.stringify(cart));
                        updateCartCount();
                        updateCartDisplay();
                    }
                }
            }

            function removeFromCart(productId) {
                console.log('Eliminando del carrito:', productId); // Debug
                cart = cart.filter(item => item.id !== productId);
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCount();
                updateCartDisplay();
            }

            // Configurar el modal del carrito
            const cartModal = document.getElementById('cart-modal');
            const cartBtn = document.getElementById('cart-btn');
            const closeBtn = cartModal.querySelector('.close');

            if (!cartModal || !cartBtn || !closeBtn) {
                console.error('Elementos del modal no encontrados');
                return;
            }

            console.log('Elementos del modal:', { cartModal, cartBtn, closeBtn }); // Debug

            // Función para mostrar el modal
            function showModal() {
                cartModal.style.display = 'block';
                setTimeout(() => cartModal.classList.add('show'), 10);
                document.body.style.overflow = 'hidden';
                updateCartDisplay(); // Actualizar contenido al abrir
            }

            // Función para ocultar el modal
            function hideModal() {
                cartModal.classList.remove('show');
                setTimeout(() => {
                    cartModal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            }

            // Botón para abrir el carrito
            cartBtn.addEventListener('click', function(e) {
                console.log('Click en botón del carrito'); // Debug
                e.preventDefault();
                showModal();
            });

            // Botón para cerrar el carrito
            closeBtn.addEventListener('click', function() {
                console.log('Click en botón cerrar'); // Debug
                hideModal();
            });

            // Cerrar al hacer clic fuera del carrito
            cartModal.addEventListener('click', function(e) {
                if (e.target === cartModal) {
                    console.log('Click fuera del modal'); // Debug
                    hideModal();
                }
            });

            // Event listener para los botones de agregar al carrito
            document.addEventListener('click', function(e) {
                const addButton = e.target.closest('.add-to-cart');
                if (addButton) {
                    console.log('Click en botón agregar al carrito:', addButton); // Debug
                    e.preventDefault();
                    
                    // Obtener y validar los datos del producto
                    const id = parseInt(addButton.dataset.id);
                    const name = addButton.dataset.name;
                    const price = parseFloat(addButton.dataset.price);
                    
                    console.log('Datos del producto:', { id, name, price }); // Debug
                    
                    if (isNaN(id) || !name || isNaN(price)) {
                        console.error('Datos de producto inválidos:', { id, name, price });
                        return;
                    }
                    
                    addToCart({
                        id: id,
                        name: name,
                        price: price
                    });
                }
            });

            // Inicializar carrito
            updateCartCount();
            updateCartDisplay();

            // Hacer las funciones accesibles globalmente
            window.addToCart = addToCart;
            window.updateQuantity = updateQuantity;
            window.removeFromCart = removeFromCart;
            
            console.log('Inicialización del carrito completada'); // Debug
    });

    </script>

     <!-- Script para el botón Volver Arriba -->
     <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnUp = document.getElementById('btnUp');
            let isScrolling;
            
            // Mostrar/ocultar el botón según el scroll con debounce
            window.addEventListener('scroll', function() {
                // Limpiar el timeout anterior
                window.clearTimeout(isScrolling);

                // Establecer un timeout para manejar el scroll
                isScrolling = setTimeout(function() {
                    if (window.scrollY > 300) {
                        btnUp.classList.remove('opacity-0', 'invisible', 'translate-y-2');
                        btnUp.classList.add('opacity-100', 'visible', 'translate-y-0');
                    } else {
                        btnUp.classList.remove('opacity-100', 'visible', 'translate-y-0');
                        btnUp.classList.add('opacity-0', 'invisible', 'translate-y-2');
                    }
                }, 100);
            });

            // Funcionalidad del botón con scroll suave personalizado
            btnUp.addEventListener('click', function() {
                const duration = 1000; // Duración de la animación en ms
                const start = window.scrollY;
                const startTime = performance.now();

                function easeInOutCubic(t) {
                    return t < 0.5 
                        ? 4 * t * t * t 
                        : 1 - Math.pow(-2 * t + 2, 3) / 2;
                }

                function scrollAnimation(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    window.scrollTo(0, start * (1 - easeInOutCubic(progress)));

                    if (progress < 1) {
                        requestAnimationFrame(scrollAnimation);
                    }
                }

                requestAnimationFrame(scrollAnimation);
            });
        });
    </script>
@stack('scripts')
</body>

</html>