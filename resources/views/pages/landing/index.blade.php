@extends('layouts.landing')
@section('title', '4GMovil - Productos Destacados')
@section('meta-description', 'Descubre nuestros productos destacados en 4GMovil. Encuentra lo mejor en tecnología móvil y accesorios.')
@section('content')

    <!-- Slider Section -->
    <div class="slider-container">
        <div class="slider" id="slider">
            <!-- Slide 1 -->
            <div class="slide" style="background-image: url('{{ asset('img/slaider/Slaider 1.png')}}')">
                <div class="slide-content">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">Los mejores smartphones</h2>
                    <p class="text-xl mb-8 max-w-2xl mx-auto">Encuentra los últimos modelos al mejor precio</p>
                    <a href="#" class="btn-primary">Ver productos</a>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="slide"
                style="background-image: url('https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')">
                <div class="slide-content">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">Reparación profesional</h2>
                    <p class="text-xl mb-8 max-w-2xl mx-auto">Deja tu dispositivo en manos de expertos</p>
                    <a href="#" class="btn-primary">Conoce nuestros servicios</a>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="slide"
                style="background-image: url('https://images.unsplash.com/photo-1567581935884-3349723552ca?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80')">
                <div class="slide-content">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">Ofertas especiales</h2>
                    <p class="text-xl mb-8 max-w-2xl mx-auto">Aprovecha nuestros descuentos por tiempo limitado</p>
                    <a href="#" class="btn-primary">Ver ofertas</a>
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="slide"
                style="background-image: url('https://images.unsplash.com/photo-1546054454-aa26e2b734c7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1480&q=80')">
                <div class="slide-content">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">Accesorios premium</h2>
                    <p class="text-xl mb-8 max-w-2xl mx-auto">Protege y personaliza tu dispositivo</p>
                    <a href="#" class="btn-primary">Comprar ahora</a>
                </div>
            </div>

            <!-- Slide 5 -->
            <div class="slide"
                style="background-image: url('https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')">
                <div class="slide-content">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">Garantía de calidad</h2>
                    <p class="text-xl mb-8 max-w-2xl mx-auto">Todos nuestros productos y servicios incluyen garantía</p>
                    <a href="#" class="btn-primary">Más información</a>
                </div>
            </div>
        </div>

        <button class="slider-btn prev" id="prev"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-btn next" id="next"><i class="fas fa-chevron-right"></i></button>

        <div class="slider-nav" id="slider-nav">
            <div class="slider-dot active" data-slide="0"></div>
            <div class="slider-dot" data-slide="1"></div>
            <div class="slider-dot" data-slide="2"></div>
            <div class="slider-dot" data-slide="3"></div>
            <div class="slider-dot" data-slide="4"></div>
        </div>
    </div>

    <!-- Brands Section -->
    <section class="py-8 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Samsung_Logo.svg/1280px-Samsung_Logo.svg.png"
                    alt="Samsung" class="brand-logo">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/505px-Apple_logo_black.svg.png"
                    alt="Apple" class="brand-logo">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Xiaomi_logo_%282021-%29.svg/1024px-Xiaomi_logo_%282021-%29.svg.png"
                    alt="Xiaomi" class="brand-logo ">
                <img src="https://i.pinimg.com/736x/98/a3/2e/98a32e0ef25e6148b8a08954956eb0e4.jpg" alt="Infinix"
                    class="brand-logo">
                <img src="https://w7.pngwing.com/pngs/801/372/png-transparent-motorola-logo-motorola-droid-moto-x-logo-motorola-mobility-moto-blue-angle-text-thumbnail.png"
                    alt="Motorola" class="brand-logo">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR8oDIBg2zyPE1ree8dclglIj_ppBgHGziUhw&s"
                    alt="Oppo" class="brand-logo">
                <img src="https://w7.pngwing.com/pngs/376/216/png-transparent-zte-blue-logo-tech-companies.png" alt="ZTE"
                    class="brand-logo">
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Productos Destacados</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Los mejores dispositivos al mejor precio del mercado</p>
            </div>

            <div class="products-slider relative">
                <!-- Contenedor del slider -->
                <div class="products-slide flex overflow-x-auto scroll-smooth gap-4" id="products-slide">
                    @foreach ($productos as $producto)
                        <!-- Tarjeta de producto -->
                        <div class="product-slide-item min-w-[300px] flex-shrink-0">
                            <div class="product-card bg-white rounded shadow hover:shadow-md transition duration-300">
                                <div class="relative overflow-hidden h-[200px] w-full rounded-t">
                                    @if($producto->imagenes->isNotEmpty())
                                        <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}"
                                            class="w-full h-full object-cover" alt="{{ $producto->nombre_producto }}">
                                    @else
                                        <img src="{{ asset('img/Logo_2.png') }}" class="w-full h-full object-cover"
                                            alt="Sin imagen">
                                    @endif
                                   <div class="absolute top-2 left-2 bg-{{ $producto->estado == 'nuevo' ? 'green' : 'gray' }}-600 text-white text-xs px-2 py-1 rounded">{{ ucfirst($producto->estado) }}</div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-2">{{ $producto->nombre_producto }}</h3>
                                    <div class="flex items-center mb-2">
                                        <div class="text-yellow-500">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span class="text-gray-600 text-sm ml-2">(24)</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span
                                            class="font-bold text-blue-600 text-xl">${{ number_format($producto->precio, 0, ',', '.') }}</span>
                                    </div>
                                    <button type="button"
                                        class="add-to-cart w-full mt-4 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
                                        data-id="{{ $producto->producto_id }}" 
                                        data-name="{{ $producto->nombre_producto }}"
                                        data-price="{{ $producto->precio }}">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Agregar al carrito
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Botones del slider -->
                <button class="slider-btn prev" id="prevButton"><i class="fas fa-chevron-left"></i></button>
                <button class="slider-btn next" id="nextButton"><i class="fas fa-chevron-right"></i></button>
            </div>


            <div class="text-center mt-8">
                <a href="{{ route('productos.lista') }}" class="inline-block bg-primary text-white px-6 py-3 rounded hover:bg-blue-700 transition">Ver
                    todos los productos</a>
            </div>
        </div>
    </section>

    <!-- Allies Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Nuestros Aliados</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Empresas que confían en nosotros y con las que trabajamos</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- Ally 1 - Alquería -->
                <div class="bg-white p-6 rounded-lg text-center shadow-sm hover:shadow-md transition">
                    <img src="https://yt3.googleusercontent.com/7oj-EkIyJE1hS06HKVpqz7Nzmg4wJEz3Lu9CD4JO39Mzf1k-1XYp-_KMlHJQYZocuBdRGKf7=s900-c-k-c0x00ffffff-no-rj"
                        alt="Alquería" class="w-full h-32 object-contain mb-4">
                    <h3 class="text-xl font-bold mb-3">Alquería</h3>
                    <p class="text-gray-600">Productos lácteos de la más alta calidad para toda la familia</p>
                </div>

                <!-- Ally 2 - Grupo Forma Íntimas -->
                <div class="bg-white p-6 rounded-lg text-center shadow-sm hover:shadow-md transition">
                    <img src="{{ asset('img/gfi.png') }}"
                        alt="Grupo Forma Íntimas" class="w-full h-32 object-contain mb-4">
                    <h3 class="text-xl font-bold mb-3">Grupo Forma Íntimas</h3>
                    <p class="text-gray-600">Ropa interior femenina con diseños innovadores y cómodos</p>
                </div>

                <!-- Ally 3 - Centro Colombo Americano -->
                <div class="bg-white p-6 rounded-lg text-center shadow-sm hover:shadow-md transition">
                    <img src="https://pbs.twimg.com/profile_images/827330867097980928/Rm4yC6YI_400x400.jpg"
                        alt="Centro Colombo Americano" class="w-full h-32 object-contain mb-4">
                    <h3 class="text-xl font-bold mb-3">Centro Colombo Americano</h3>
                    <p class="text-gray-600">Educación y cultura para el desarrollo de habilidades bilingües</p>
                </div>

                <!-- Ally 4 - Aderezos -->
                <div class="bg-white p-6 rounded-lg text-center shadow-sm hover:shadow-md transition">
                    <img src="https://cdn.shopify.com/s/files/1/0559/1266/1155/files/Logo-aderezos-original_c6d4acd6-d8e7-4356-97f8-0f3eda5c0886.png?v=1625758391" alt="Aderezos"
                        class="w-full h-32 object-contain mb-4">
                    <h3 class="text-xl font-bold mb-3">Aderezos</h3>
                    <p class="text-gray-600">Salsas y aderezos para realzar el sabor de tus comidas</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Lo que dicen nuestros clientes</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Experiencias reales de personas que han confiado en nosotros</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Cliente"
                            class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">María González</h4>
                            <div class="text-warning text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"Excelente servicio, repararon mi celular en menos de una hora y quedó como
                        nuevo. Muy recomendados!"</p>
                   
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Cliente"
                            class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Carlos Martínez</h4>
                            <div class="text-warning text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"Compré un iPhone 14 Pro y el precio fue el mejor que encontré en el mercado.
                        Además, la atención fue muy personalizada."</p>
                   
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Cliente"
                            class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Laura Rodríguez</h4>
                            <div class="text-warning text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"Recuperaron todas mis fotos de un celular que se mojó. Estoy muy agradecida
                        con el equipo de 4GMovil."</p>
                   
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-primary text-white">
        <div class="container mx-auto px-4 text-center">
            <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                alt="Contacto" class="w-full h-64 object-cover rounded-lg mb-8">
            <h2 class="text-3xl font-bold mb-4">¿Necesitas ayuda con tu dispositivo?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Contáctanos y recibe asesoría personalizada de nuestros expertos</p>
            <a href="#"
                class="inline-block bg-white text-primary px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">Contáctanos
                ahora</a>
        </div>
    </section>

@endsection