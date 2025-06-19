@extends('layouts.landing')
@section('title', '4GMovil - Productos Destacados')
@section('meta-description', 'Descubre nuestros productos destacados en 4GMovil. Encuentra lo mejor en tecnología móvil y accesorios.')
@section('content')
<style>
      .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      }
      .brand-logo {
        transition: all 0.3s ease;
      }
      .brand-logo:hover {
        transform: scale(1.05);
      }
      .filter-btn.active {
        background-color: #3b82f6;
        color: white;
      }
      .search-box:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
      }
      .price-tag {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #10b981;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.8rem;
      }
      @media (max-width: 640px) {
        .brands-grid {
          grid-template-columns: repeat(2, 1fr);
        }
      }
    </style>
  <!-- Breadcrumb -->
  <div class="container mx-auto px-4 py-3 bg-gray-100">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <a
              href="{{ route('landing') }}"
              class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600"
            >
              <i class="fas fa-home mr-2"></i>
              Inicio
            </a>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <i class="fas fa-angle-right text-gray-400 mx-2"></i>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Catálogo de Celulares</span
              >
            </div>
          </li>
        </ol>
      </nav>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <!-- Marcas destacadas -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
          <i class="fas fa-star text-yellow-400 mr-2"></i>
          Marcas Destacadas
        </h2>
        <div
          class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 brands-grid">
          <a
            href="#"
            class="brand-logo2 bg-white p-4 rounded-lg shadow-sm flex flex-col items-center justify-center border border-gray-200 hover:border-blue-300"
          >
            <img
              src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg"
              alt="Apple"
              class="h-12 mb-2"
            />
            <span class="text-sm font-medium text-gray-700">Apple</span>
          </a>
          <a
            href="#"
            class="brand-logo2 bg-white p-4 rounded-lg shadow-sm flex flex-col items-center justify-center border border-gray-200 hover:border-blue-300">
            <img
              src="https://upload.wikimedia.org/wikipedia/commons/2/24/Samsung_Logo.svg"
              alt="Samsung"
              class="h-8 mb-2"
            />
            <span class="text-sm font-medium text-gray-700">Samsung</span>
          </a>
          <a
            href="#"
            class="brand-logo2 bg-white p-4 rounded-lg shadow-sm flex flex-col items-center justify-center border border-gray-200 hover:border-blue-300"
          >
            <img
              src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Xiaomi_logo_%282021-%29.svg/768px-Xiaomi_logo_%282021-%29.svg.png"
              alt="Xiaomi"
              class="h-8 mb-2"
            />
            <span class="text-sm font-medium text-gray-700">Xiaomi</span>
          </a>
          <a
            href="#"
            class="brand-logo2 bg-white p-4 rounded-lg shadow-sm flex flex-col items-center justify-center border border-gray-200 hover:border-blue-300"
          >
            <img
              src="https://i.pinimg.com/736x/bf/6b/69/bf6b690c34ed54068c227be2c77d3b0c.jpg"
              alt="Oppo"
              class="h-8 mb-2"
            />
            <span class="text-sm font-medium text-gray-700">Oppo</span>
          </a>
          <a
            href="#"
            class="brand-logo2 bg-white p-4 rounded-lg shadow-sm flex flex-col items-center justify-center border border-gray-200 hover:border-blue-300"
          >
            <img
              src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/96/LOGO_Honor.svg/1200px-LOGO_Honor.svg.png"
              alt="Honor"
              class="h-8 mb-2"
            />
            <span class="text-sm font-medium text-gray-700">Honor</span>
          </a>
          <a
            href="#"
            class="brand-logo2 bg-white p-4 rounded-lg shadow-sm flex flex-col items-center justify-center border border-gray-200 hover:border-blue-300"
          >
            <img
              src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQnCiR6dtcVHc4_WzLJlXeol7-pvOQKHQ3yxQ&s"
              alt="Motorola"
              class="h-8 mb-2"
            />
            <span class="text-sm font-medium text-gray-700">Motorola</span>
          </a>
        </div>
      </section>

      <!-- Filtros y ordenamiento -->
      <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 bg-white p-4 rounded-lg shadow-sm"
      >
        <div class="mb-4 md:mb-0">
          <h3 class="text-lg font-semibold text-gray-700 mb-2">Filtrar por:</h3>
          <div class="flex flex-wrap gap-2">
            <button
              class="filter-btn px-3 py-1 bg-gray-100 rounded-full text-sm font-medium hover:bg-blue-100 hover:text-blue-600 active"
            >
              Todos
            </button>
            <button
              class="filter-btn px-3 py-1 bg-gray-100 rounded-full text-sm font-medium hover:bg-blue-100 hover:text-blue-600"
            >
              Celulares 
            </button>
            <button
              class="filter-btn px-3 py-1 bg-gray-100 rounded-full text-sm font-medium hover:bg-blue-100 hover:text-blue-600"
            >
              Portatiles 
            </button>
            <button
              class="filter-btn px-3 py-1 bg-gray-100 rounded-full text-sm font-medium hover:bg-blue-100 hover:text-blue-600"
            >
              Accesorios 
            </button>
            <button
              class="filter-btn px-3 py-1 bg-gray-100 rounded-full text-sm font-medium hover:bg-blue-100 hover:text-blue-600"
            >
              parlantes 
            </button>
          </div>
        </div>
        <div>
          <label for="sort" class="text-sm font-medium text-gray-700 mr-2"
            >Ordenar por:</label
          >
          <select
            id="sort"
            class="border border-gray-300 rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option>Relevancia</option>
            <option>Precio: Menor a Mayor</option>
            <option>Precio: Mayor a Menor</option>
            <option>Novedades</option>
            <option>Mejor valorados</option>
          </select>
        </div>
      </div>

      <!-- Catálogo de productos -->
      <section>
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
          <i class="fas fa-mobile-alt text-blue-500 mr-2"></i>
          Celulares
        </h2>

        <div
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
        >
          <!-- Producto 1 -->
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

        <!-- Paginación -->
        <div class="mt-10 flex justify-center">
          <nav class="inline-flex rounded-md shadow-sm">
            <a
              href="#"
              class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50"
            >
              <i class="fas fa-chevron-left"></i>
            </a>
            <a
              href="#"
              class="px-4 py-2 border-t border-b border-gray-300 bg-white text-blue-600 font-medium"
              >1</a
            >
            <a
              href="#"
              class="px-4 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50"
              >2</a
            >
            <a
              href="#"
              class="px-4 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50"
              >3</a
            >
            <a
              href="#"
              class="px-4 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50"
              >4</a
            >
            <a
              href="#"
              class="px-4 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50"
              >5</a
            >
            <a
              href="#"
              class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50"
            >
              <i class="fas fa-chevron-right"></i>
            </a>
          </nav>
        </div>
      </section>
    </main>
@endsection