<!-- Alerta de confirmación para eliminación -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración general de SweetAlert2
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Formularios de eliminación
        const forms = document.querySelectorAll('.form-eliminar');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626', // red-600
                    cancelButtonColor: '#4f46e5', // indigo-600
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 rounded-md',
                        cancelButton: 'px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Formulario de cierre de sesión
        const logoutForms = document.querySelectorAll('form[action="{{ route('logout')}}"]');
        logoutForms.forEach(form => {
            const button = form.querySelector('button');
            if (button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Cerrar sesión?',
                        text: "Tu sesión se cerrará",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626', // red-600
                        cancelButtonColor: '#4f46e5', // indigo-600
                        confirmButtonText: 'Sí, cerrar sesión',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 rounded-md',
                            cancelButton: 'px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    });
</script>

<!-- Alertas de sesión -->
@if(session('bienvenido'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '¡Bienvenido!',
            text: @json(session('bienvenido')),
            icon: 'success',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'bg-white rounded-lg shadow-xl',
                title: 'text-lg font-medium text-gray-900',
                content: 'text-sm text-gray-500'
            }
        });
    });
</script>
@endif

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success')}}',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        customClass: {
            popup: 'bg-white rounded-lg shadow-xl',
            title: 'text-lg font-medium text-gray-900',
            content: 'text-sm text-gray-500'
        }
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '{{ session('error')}}',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        customClass: {
            popup: 'bg-white rounded-lg shadow-xl border-l-4 border-red-500',
            title: 'text-lg font-medium text-gray-900',
            content: 'text-sm text-gray-500'
        }
    });
</script>
@endif 