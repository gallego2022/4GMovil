@extends('layouts.landing')
@section('title', '4GMovil - Productos Destacados')
@section('meta-description', 'Descubre nuestros productos destacados en 4GMovil. Encuentra lo mejor en tecnología móvil y accesorios.')
@section('content')
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
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Nosotros</span
              >
            </div>
          </li>
        </ol>
      </nav>
    </div>

    <!-- Main Content -->
       <!-- Sección QUIENES SOMOS -->
  <section class="py-12 text-center bg-white">
    <h1 class="text-4xl font-bold">QUIENES SOMOS</h1>
    <p class="text-lg mt-2">Conoce un poco más de nuestra historia.</p>
  </section>

  <!-- Sección Presentación -->
  <section class="py-8 bg-gray">
    <div class="container mx-auto px-6 md:px-12 flex flex-col md:flex-row items-center justify-between gap-5">
      <div class="texto md:w-1/2 mb-4 md:mb-0">
        <h1 class="text-5xl font-bold mb-3"><span class="text-blue-600">4G</span>Movil</h1>
        <p class="text-lg" style="text-align:justify">
          En 4gmovil, somos una tienda especializada en la venta de dispositivos tecnológicos de última generación, como
          celulares, tablets y computadoras.
          Nos enorgullece ofrecer a nuestros clientes productos de alta calidad, junto con un servicio al cliente
          excepcional, con el objetivo de satisfacer
          las necesidades tecnológicas de todos.
        </p>
      </div>
      <div class="imagen-circulo md:w-1/3 flex justify-center">
        <div class="circulo rounded-full overflow-hidden w-80 h-80 flex items-center justify-center bg-white shadow">
          <img src="../img/Logo_2.png" alt="Logo 4GMovil" class="object-contain w-60 h-60" />
        </div>
      </div>
    </div>
  </section>

  <section class="py-16 bg-white">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12 text-center">

        <!-- Misión -->
        <div>
          <i class="fas fa-comments text-5xl text-blue-600 mb-4"></i>
          <h2 class="text-2xl font-semibold mb-3">Misión</h2>
          <p class="text-gray-700 leading-relaxed">
            En <strong>4GMovil</strong>, nuestra misión es conectar a las personas con la tecnología de forma accesible,
            confiable y asequible.
            Queremos ser tu primera opción cuando pienses en adquirir un dispositivo, y más allá de eso, brindar
            soluciones tecnológicas que mejoren tu día a día.
          </p>
        </div>

        <!-- Visión -->
        <div>
          <i class="fas fa-hand-pointer text-5xl text-blue-600 mb-4"></i>
          <h2 class="text-2xl font-semibold mb-3">Visión</h2>
          <p class="text-gray-700 leading-relaxed">
            Ser reconocidos como líderes en el sector tecnológico por nuestra innovación, atención al cliente y
            compromiso con la calidad.
            Aspiramos a transformar la vida de nuestros usuarios facilitando el acceso a la tecnología más avanzada.
          </p>
        </div>

      </div>
    </div>
  </section>


  <section class="bg-white py-16">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-4xl font-extrabold mb-4 text-black">NUESTROS ALIADOS</h2>
      <p class="text-gray-700 mb-10">Actualmente contamos con varios aliados, algunos de ellos son:</p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

        <!-- Aliado 1 -->
        <div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-lg transition">
          <img
            src="https://yt3.googleusercontent.com/7oj-EkIyJE1hS06HKVpqz7Nzmg4wJEz3Lu9CD4JO39Mzf1k-1XYp-_KMlHJQYZocuBdRGKf7=s900-c-k-c0x00ffffff-no-rj"
            alt="Alqueria" class="w-full h-32 object-contain mb-4">
          <p class="text-gray-700">Aliado importante en innovación tecnológica y desarrollo social.</p>

        </div>

        <!-- Aliado 2 -->
        <div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-lg transition">
          <img src="https://grupoformasintimas.com.co/wp-content/uploads/2023/04/LOGO-GFI-SIN-MARCAS-.png"
            alt="Grupo Formas Intimas" class="w-full h-32 object-contain mb-4">
          <p class="text-gray-700">Trabajamos en conjunto para mejorar el acceso a dispositivos móviles.</p>

        </div>

        <!-- Aliado 3 -->
        <div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-lg transition">
          <img src="https://pbs.twimg.com/profile_images/827330867097980928/Rm4yC6YI_400x400.jpg"
            alt="Centro Colombo Americano" class="w-full h-32 object-contain mb-4">
          <p class="text-gray-900 font-bold">
            En 4GMovil nos aliamos con el Centro Colombo Americano para ofrecer lo mejor en tecnología y educación.
            Juntos buscamos que más personas tengan acceso a productos innovadores.
          </p>

        </div>

        <!-- Aliado 4 -->
        <div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-lg transition">
          <img
            src="https://cdn.shopify.com/s/files/1/0559/1266/1155/files/Logo-aderezos-original_c6d4acd6-d8e7-4356-97f8-0f3eda5c0886.png?v=1625758391"
            alt="Aderezos" class="w-full h-32 object-contain mb-4">
          <p class="text-gray-700">Aliado estratégico en soluciones tecnológicas de última generación.</p>

        </div>

      </div>
    </div>
  </section>

@endsection