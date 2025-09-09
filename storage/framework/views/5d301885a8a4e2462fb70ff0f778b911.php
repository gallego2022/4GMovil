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
        const logoutForms = document.querySelectorAll('form[action="<?php echo e(route('logout')); ?>"]');
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
<?php if(session('bienvenido')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '¡Bienvenido!',
            text: <?php echo json_encode(session('bienvenido'), 15, 512) ?>,
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
<?php endif; ?>

<?php if(session('success')): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo e(session('success')); ?>',
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
<?php endif; ?>

<?php if(session('eliminado')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Eliminado!',
            text: <?php echo json_encode(session('eliminado'), 15, 512) ?>,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end'
        });
    });
</script>
<?php endif; ?>

<?php if(session('error')): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '<?php echo e(session('error')); ?>',
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
<?php endif; ?> 

<?php if(session('logout')): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Cierre de sesión!',
        text: '<?php echo e(session('logout')); ?>',
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
<?php endif; ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/layouts/partials/sweet-alerts.blade.php ENDPATH**/ ?>