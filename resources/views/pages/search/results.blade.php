@extends('layouts.landing')

@section('title', 'Resultados de búsqueda')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Resultados para: "{{ $q }}"</h1>

        @if($q === '')
            <p class="mt-4 text-gray-600 dark:text-gray-300">Ingresa un término de búsqueda para comenzar.</p>
        @endif

        @if($paginas->count())
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Páginas</h2>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($paginas as $page)
                        <a href="{{ route($page['route']) }}"
                           class="block p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:shadow-lg transition">
                           <i class="fas fa-link mr-2 text-blue-600 dark:text-blue-400"></i>{{ $page['title'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-10">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Productos</h2>
            @if($productos->count())
                <div class="mt-4">
                    @include('components.productos-grid', ['productos' => $productos])
                </div>
            @else
                <p class="mt-3 text-gray-600 dark:text-gray-300">No se encontraron productos.</p>
            @endif
        </div>
    </div>
@endsection


