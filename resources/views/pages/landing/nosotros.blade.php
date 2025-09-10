@extends('layouts.landing')
@section('title', '4GMovil - Sobre Nosotros')
@section('meta-description', 'Conoce más sobre 4GMovil, nuestra historia, valores y el equipo que hace posible conectar a Colombia con la tecnología más avanzada.')
@section('content')

<!-- Breadcrumb -->
<div class="container mx-auto px-4 py-3 bg-gray-100 dark:bg-gray-800">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('landing') }}" class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>
                    {{ __('messages.nav.home') }}
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-angle-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">{{ __('messages.nav.about') }}</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Hero Section -->
<section class="min-h-[60vh] flex items-center relative overflow-hidden pt-16 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800">
    <!-- Elementos decorativos -->
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="absolute top-0 left-0 w-full h-full">
        <div class="absolute top-10 left-4 sm:left-10 w-16 h-16 sm:w-20 sm:h-20 bg-white/10 rounded-full"></div>
        <div class="absolute top-20 right-4 sm:right-20 w-24 h-24 sm:w-32 sm:h-32 bg-white/5 rounded-full"></div>
        <div class="absolute bottom-10 left-1/4 w-12 h-12 sm:w-16 sm:h-16 bg-white/10 rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-3xl sm:text-4xl lg:text-6xl font-bold mb-4 sm:mb-6 text-white leading-tight">
            {{ __('messages.about.title') }}
        </h1>
        <p class="text-lg sm:text-xl lg:text-2xl mb-6 sm:mb-8 text-blue-100 leading-relaxed max-w-3xl mx-auto px-4">
            {{ __('messages.about.subtitle') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#historia" class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-history mr-2"></i>{{ __('messages.about.know_our_history') }}
            </a>
            <a href="#equipo" class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-gray-100 hover:text-blue-600 transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-users mr-2"></i>{{ __('messages.about.our_team') }}
            </a>
        </div>
    </div>
</section>

<!-- Historia Section -->
<section id="historia" class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-6">{{ __('messages.about.our_history') }}</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('messages.about.history_desc') }}
                </p>
                
                <div class="grid grid-cols-2 gap-6 mt-8">
                    <div class="text-center bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">10+</div>
                        <div class="text-gray-600 dark:text-gray-300">{{ __('messages.about.years_experience') }}</div>
                    </div>
                    <div class="text-center bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">100K+</div>
                        <div class="text-gray-600 dark:text-gray-300">{{ __('messages.about.satisfied_clients') }}</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-2xl p-8 text-white shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-6xl mb-4 opacity-90"></i>
                        <h3 class="text-2xl font-bold mb-4">{{ __('messages.about.constant_growth') }}</h3>
                        <p class="text-lg opacity-90 leading-relaxed">
                            {{ __('messages.about.growth_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Valores Section -->
<section class="py-20 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-6">{{ __('messages.about.our_values') }}</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                {{ __('messages.about.values_desc') }}
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 dark:border-gray-600">
                <div class="text-center">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-handshake text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">{{ __('messages.about.trust') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('messages.about.trust_desc') }}
                    </p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 dark:border-gray-600">
                <div class="text-center">
                    <div class="bg-gradient-to-br from-green-600 to-emerald-700 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-lightbulb text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">{{ __('messages.about.innovation') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('messages.about.innovation_desc') }}
                    </p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 dark:border-gray-600">
                <div class="text-center">
                    <div class="bg-gradient-to-br from-purple-600 to-pink-700 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-heart text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">{{ __('messages.about.passion') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('messages.about.passion_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estadísticas Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 text-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">{{ __('messages.about.our_numbers') }}</h2>
            <p class="text-xl opacity-90">
                {{ __('messages.about.numbers_desc') }}
            </p>
        </div>
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm hover:bg-opacity-20 transition-all duration-300">
                <div class="text-4xl md:text-5xl font-bold mb-2 counter" data-target="100000">0</div>
                <div class="text-lg opacity-90">{{ __('messages.about.satisfied_clients') }}</div>
            </div>
            <div class="text-center bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm hover:bg-opacity-20 transition-all duration-300">
                <div class="text-4xl md:text-5xl font-bold mb-2 counter" data-target="50000">0</div>
                <div class="text-lg opacity-90">{{ __('messages.about.devices_sold') }}</div>
            </div>
            <div class="text-center bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm hover:bg-opacity-20 transition-all duration-300">
                <div class="text-4xl md:text-5xl font-bold mb-2 counter" data-target="25">0</div>
                <div class="text-lg opacity-90">{{ __('messages.about.stores_colombia') }}</div>
            </div>
            <div class="text-center bg-white bg-opacity-10 rounded-xl p-6 backdrop-blur-sm hover:bg-opacity-20 transition-all duration-300">
                <div class="text-4xl md:text-5xl font-bold mb-2 counter" data-target="98">0</div>
                <div class="text-lg opacity-90">{{ __('messages.about.satisfaction_rate') }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Equipo Section -->
<section id="equipo" class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-6">{{ __('messages.about.our_team') }}</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                {{ __('messages.about.team_desc') }}
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center group">
                <div class="relative mb-6">
                    <div class="w-48 h-48 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-full mx-auto flex items-center justify-center text-white text-6xl font-bold group-hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Osman Gallego</h3>
                <p class="text-blue-600 dark:text-blue-400 font-semibold mb-4">{{ __('messages.about.ceo_founder') }}</p>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ __('messages.about.ceo_desc') }}
                </p>
            </div>
            <div class="text-center group">
                <div class="relative mb-6">
                    <div class="w-48 h-48 bg-gradient-to-br from-green-600 via-emerald-600 to-teal-700 rounded-full mx-auto flex items-center justify-center text-white text-6xl font-bold group-hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Estiven Lopera</h3>
                <p class="text-green-600 dark:text-green-400 font-semibold mb-4">{{ __('messages.about.operations_director') }}</p>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ __('messages.about.operations_director_desc') }}
                </p>
            </div>
            <div class="text-center group">
                <div class="relative mb-6">
                    <div class="w-48 h-48 bg-gradient-to-br from-purple-600 via-pink-600 to-red-700 rounded-full mx-auto flex items-center justify-center text-white text-6xl font-bold group-hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Jhoan Florez</h3>
                <p class="text-purple-600 dark:text-purple-400 font-semibold mb-4">{{ __('messages.about.technical_director') }}</p>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ __('messages.about.technical_director_desc') }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Misión y Visión Section -->
<section class="py-20 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12">
            <div class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-lg border border-gray-100 dark:border-gray-600 hover:shadow-xl transition-all duration-300">
                <div class="text-center mb-6">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Nuestra Misión</h3>
                </div>
                <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                    En 4gmovil, somos una tienda especializada en la venta de dispositivos tecnológicos de última generación, 
                    como celulares, tablets y computadoras. Nos enorgullece ofrecer a nuestros clientes productos de alta calidad, 
                    junto con un servicio al cliente excepcional, con el objetivo de satisfacer las necesidades tecnológicas de todos.
                </p>
            </div>
            <div class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-lg border border-gray-100 dark:border-gray-600 hover:shadow-xl transition-all duration-300">
                <div class="text-center mb-6">
                    <div class="bg-gradient-to-br from-green-600 to-emerald-700 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-eye text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Nuestra Visión</h3>
                </div>
                <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                    Ser la empresa líder en tecnología móvil en Colombia, reconocida por la innovación, 
                    la excelencia en el servicio al cliente y el compromiso con el desarrollo tecnológico 
                    del país, contribuyendo a la transformación digital de la sociedad colombiana.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">{{ __('messages.about.ready_connect_future') }}</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto opacity-90">
            {{ __('messages.about.join_thousands') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('productos.lista') }}" class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-shopping-cart mr-2"></i>{{ __('messages.about.view_products') }}
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
// Animación de contadores
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 20);
}

// Observador de intersección para animar contadores
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counter = entry.target;
            const target = parseInt(counter.dataset.target);
            animateCounter(counter, target);
            observer.unobserve(counter);
        }
    });
}, { threshold: 0.5 });

// Observar todos los contadores
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => observer.observe(counter));
});
</script>
@endpush