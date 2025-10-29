@props([
    'context' => 'header', // 'header' | 'error'
    'action' => route('buscar'),
    'suggestions' => route('buscar.sugerencias'),
    'placeholder' => 'Buscar productos, servicios...'
])

@php($uid = uniqid('srch_'))

<div class="{{ $context === 'error' ? '' : 'relative' }}" style="display: flex; justify-content: center;">
    <form action="{{ $action }}" method="GET" class="{{ $context === 'error' ? 'error-search-form' : 'flex items-center gap-2 relative' }}" id="{{ $uid }}_form" autocomplete="off">
        <input type="text" name="q" id="{{ $uid }}_input"
               placeholder="{{ $placeholder }}"
               class="{{ $context === 'error' 
                    ? 'error-search-input' 
                    : 'w-56 lg:w-64 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition' }}">
        <button type="submit" class="{{ $context === 'error' ? 'error-search-btn' : 'p-2 text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition' }}" aria-label="Buscar">
            <i class="fas fa-search"></i>
        </button>
        @if($context !== 'error')
        <div id="{{ $uid }}_box" class="absolute top-full mt-2 left-0 right-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden" style="display:none; z-index: 9999;"></div>
        @endif
    </form>
    @if($context === 'error')
    <div id="{{ $uid }}_box" class="search-suggestions" style="display:none; z-index: 9999;"></div>
    @endif
</div>

@push('scripts')
<script>
(function(){
    try { console.debug('search-autocomplete init: {{ $uid }}'); } catch(e) {}
    var input = document.getElementById('{{ $uid }}_input');
    var box = document.getElementById('{{ $uid }}_box');
    var lastQ = '';
    var t;
    function hideBox(){
        if (!box) return;
        box.style.display = 'none';
        box.innerHTML = '';
        window.removeEventListener('scroll', onReposition, true);
        window.removeEventListener('resize', onReposition, true);
    }
    function repositionBox(){
        if (!box || !input || box.style.display === 'none') return;
        var rect = input.getBoundingClientRect();
        var top = Math.round(rect.bottom + 8);
        var left = Math.round(rect.left);
        var width = Math.round(rect.width);
        box.style.position = 'fixed';
        box.style.top = top + 'px';
        box.style.left = left + 'px';
        box.style.width = width + 'px';
        box.style.maxHeight = '60vh';
        box.style.overflowY = 'auto';
        box.style.zIndex = 99999;
    }
    function onReposition(){
        try { repositionBox(); } catch(e) {}
    }
    function showBox(html){
        if (!box) return;
        // Mover a body para evitar recortes por overflow oculto en contenedores padres
        if (box.parentElement !== document.body) {
            document.body.appendChild(box);
        }
        box.innerHTML = html;
        box.style.display = 'block';
        repositionBox();
        window.addEventListener('scroll', onReposition, true);
        window.addEventListener('resize', onReposition, true);
    }
    function buildHTML(data){
        var html = '';
        if (data.paginas && data.paginas.length){
            html += '<div class="{{ $context === 'error' ? 'section-title' : 'px-4 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400' }}">Páginas</div>';
            data.paginas.forEach(function(p){
                html += '<a class="{{ $context === 'error' ? 'item' : 'flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100' }}" href="'+p.url+'">' +
                        '<i class="fas fa-link {{ $context === 'error' ? '' : 'text-blue-600 dark:text-blue-400' }}"></i>'+
                        '<span>'+p.title+'</span>'+
                        '</a>';
            });
        }
        if (data.productos && data.productos.length){
            html += '<div class="{{ $context === 'error' ? 'section-title' : 'px-4 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400' }}">Productos</div>';
            data.productos.forEach(function(p){
                var precio = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(Math.round(p.precio || 0));
                var img = p.imagen 
                    ? '<img src="'+p.imagen+'" alt="'+(p.nombre||'')+'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">'
                    : '<div style="width:40px;height:40px;border-radius:8px;background:rgba(0,0,0,.08);"></div>';
                if ('{{ $context }}' === 'error') {
                    html += '<a class="item" href="'+p.url+'">'+ img +'<div style="display:flex;flex-direction:column;min-width:0"><span style="font-weight:600;">'+p.nombre+'</span><span style="opacity:.8;font-size:.85rem;">$'+precio+'</span></div></a>';
                } else {
                    html += '<a class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100" href="'+p.url+'">'+
                            img +
                            '<div class="min-w-0"><div class="font-semibold truncate">'+p.nombre+'</div><div class="text-sm text-gray-500 dark:text-gray-400">$'+precio+'</div></div>'+
                            '</a>';
                }
            });
        }
        if (!html){
            html = '<div class="{{ $context === 'error' ? 'item' : 'px-4 py-3 text-sm text-gray-500 dark:text-gray-400' }}" style="opacity:.8;">Sin resultados</div>';
        } else {
            var allUrl = '{{ $action }}' + '?q=' + encodeURIComponent((input.value||'').trim());
            html += '<div class="{{ $context === 'error' ? 'item' : 'px-4 py-3' }}" style="text-align:center;">'
                 +  '<a href="'+allUrl+'" class="{{ $context === 'error' ? '' : 'inline-flex items-center' }}" style="color:var(--text-primary); text-decoration:underline;">Ver todos los resultados</a>'
                 +  '</div>';
        }
        return html;
    }
    function debounce(fn, delay){
        return function(){
            var ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(function(){ fn.apply(ctx, args); }, delay);
        }
    }
    var search = debounce(function(){
        var q = (input.value || '').trim();
        if (q === '') { hideBox(); lastQ = ''; return; }
        if (q === lastQ) { return; }
        lastQ = q;
        var url = '{{ $suggestions }}' + '?q=' + encodeURIComponent(q);
        try { console.debug('search-autocomplete fetch', url); } catch(e) {}
        fetch(url, { headers: { 'Accept': 'application/json' }})
            .then(function(r){ 
                if (!r.ok) { console.warn('Buscar.sugerencias HTTP', r.status); throw new Error('HTTP '+r.status); }
                var ct = r.headers.get('content-type') || '';
                if (!ct.includes('application/json')) { console.warn('Buscar.sugerencias no JSON', ct); throw new Error('No JSON'); }
                return r.json();
            })
            .then(function(data){ try { console.debug('search-autocomplete data', data); } catch(e) {} showBox(buildHTML(data)); })
            .catch(function(err){ console.error('Buscar.sugerencias error', err); hideBox(); });
    }, 250);
    if (input) { input.addEventListener('input', search); }
    // Atajo '/' para enfocar
    document.addEventListener('keydown', function(e){
        if (e.key === '/' && !e.target.matches('input, textarea')) {
            e.preventDefault();
            input.focus();
        }
    });
    document.addEventListener('click', function(e){
        if (!box.contains(e.target) && e.target !== input) hideBox();
    });
})();
</script>
@endpush


