<!-- Alerta de confirmación para eliminación -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar que SweetAlert esté disponible
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 no está cargado correctamente');
            return;
        }

        // Formularios de eliminación
        const forms = document.querySelectorAll('.form-eliminar');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Usar SweetAlert utils si está disponible, sino usar Swal directamente
                if (typeof SweetAlert !== 'undefined' && SweetAlert.delete) {
                    SweetAlert.delete().then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡Esta acción no se puede deshacer!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#4f46e5',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
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
                        cancelButtonColor: '#2563eb', // blue-600
                        confirmButtonText: 'Sí, cerrar sesión',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'swal2-confirm-custom',
                            cancelButton: 'swal2-cancel-custom'
                        },
                        didOpen: () => {
                            // Forzar los colores después de que se abra el modal
                            const confirmBtn = document.querySelector('.swal2-confirm');
                            const cancelBtn = document.querySelector('.swal2-deny');
                            if (confirmBtn) {
                                confirmBtn.style.backgroundColor = '#dc2626';
                                confirmBtn.style.color = 'white';
                            }
                            if (cancelBtn) {
                                cancelBtn.style.backgroundColor = '#2563eb';
                                cancelBtn.style.color = 'white';
                            }
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

@if(session('eliminado'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Eliminado!',
            text: @json(session('eliminado')),
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end'
        });
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
        timer: 2500,
        timerProgressBar: true,
        position: 'top-end',
        customClass: {
            popup: 'bg-white rounded-lg shadow-xl border-l-4 border-red-500',
            title: 'text-lg font-medium text-gray-900',
            content: 'text-sm text-gray-500'
        }
    });
</script>
@endif 

@if(session('logout'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Cierre de sesión!',
        text: '{{ session('logout')}}',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        customClass: {
            popup: 'bg-white rounded-lg shadow-xl border-l-4 border-green-500',
            title: 'text-lg font-medium text-gray-900',
            content: 'text-sm text-gray-500'
        }
    });
</script>
@endif