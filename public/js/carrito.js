/**
 * CarritoController.js
 * Manejo del carrito de compras con sistema de stock con variantes
 */

class CarritoController {
    constructor() {
        this.cart = [];
        this.total = 0;
        this.cantidadItems = 0;
        this.init();
    }

    init() {
        this.cargarCarrito();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Botones de agregar al carrito
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-agregar-carrito')) {
                e.preventDefault();
                this.agregarAlCarrito(e.target);
            }
        });

        // Botones de actualizar cantidad
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-actualizar-cantidad')) {
                e.preventDefault();
                this.actualizarCantidad(e.target);
            }
        });

        // Botones de eliminar item
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-eliminar-item')) {
                e.preventDefault();
                this.eliminarItem(e.target);
            }
        });

        // Botón de limpiar carrito
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-limpiar-carrito')) {
                e.preventDefault();
                this.limpiarCarrito();
            }
        });

        // Verificar stock al cargar la página
        if (window.location.pathname.includes('/checkout')) {
            this.verificarStock();
        }
    }

    async cargarCarrito() {
        try {
            const response = await fetch('/carrito/obtener', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.cart = data.data.items || [];
                this.total = data.data.total_precio || 0;
                this.cantidadItems = data.data.total_items || 0;
                this.actualizarUI();
            }
        } catch (error) {
            console.error('Error al cargar carrito:', error);
        }
    }

    async agregarAlCarrito(button) {
        const form = button.closest('form');
        const formData = new FormData(form);
        
        // Mostrar loading
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agregando...';

        try {
            const response = await fetch('/carrito/agregar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.cart = data.data.items || [];
                this.total = data.data.total_precio || 0;
                this.cantidadItems = data.data.total_items || 0;
                this.actualizarUI();
                
                // Mostrar mensaje de éxito
                this.mostrarNotificacion('Producto agregado al carrito', 'success');
                
                // Actualizar contador del carrito
                this.actualizarContadorCarrito();
            } else {
                this.mostrarNotificacion(data.message, 'error');
            }
        } catch (error) {
            console.error('Error al agregar al carrito:', error);
            this.mostrarNotificacion('Error al agregar producto al carrito', 'error');
        } finally {
            // Restaurar botón
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-shopping-cart"></i> Agregar al Carrito';
        }
    }

    async actualizarCantidad(button) {
        const itemId = button.dataset.itemId;
        const cantidad = parseInt(button.dataset.cantidad);

        try {
            const response = await fetch(`/carrito/actualizar/${itemId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    cantidad: cantidad
                })
            });

            const data = await response.json();

            if (data.success) {
                this.cart = data.data.items || [];
                this.total = data.data.total_precio || 0;
                this.cantidadItems = data.data.total_items || 0;
                this.actualizarUI();
                this.actualizarContadorCarrito();
            } else {
                this.mostrarNotificacion(data.message, 'error');
            }
        } catch (error) {
            console.error('Error al actualizar cantidad:', error);
            this.mostrarNotificacion('Error al actualizar cantidad', 'error');
        }
    }

    async eliminarItem(button) {
        const itemId = button.dataset.itemId;

        if (!confirm('¿Estás seguro de que quieres eliminar este item del carrito?')) {
            return;
        }

        try {
            const response = await fetch(`/carrito/eliminar/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Recargar el carrito completo después de eliminar
                await this.cargarCarrito();
                this.mostrarNotificacion('Item eliminado del carrito', 'success');
            } else {
                this.mostrarNotificacion(data.message, 'error');
            }
        } catch (error) {
            console.error('Error al eliminar item:', error);
            this.mostrarNotificacion('Error al eliminar item', 'error');
        }
    }

    async limpiarCarrito() {
        if (!confirm('¿Estás seguro de que quieres limpiar todo el carrito?')) {
            return;
        }

        try {
            const response = await fetch('/carrito/limpiar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Recargar el carrito completo después de limpiar
                await this.cargarCarrito();
                this.mostrarNotificacion('Carrito limpiado', 'success');
            } else {
                this.mostrarNotificacion(data.message, 'error');
            }
        } catch (error) {
            console.error('Error al limpiar carrito:', error);
            this.mostrarNotificacion('Error al limpiar carrito', 'error');
        }
    }

    async verificarStock() {
        try {
            const response = await fetch('/carrito/verificar-stock', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!data.success && data.errores.length > 0) {
                this.mostrarErroresStock(data.errores);
            }
        } catch (error) {
            console.error('Error al verificar stock:', error);
        }
    }

    actualizarUI() {
        // Actualizar mini carrito si existe
        const miniCarrito = document.getElementById('mini-carrito');
        if (miniCarrito) {
            this.actualizarMiniCarrito();
        }

        // Actualizar carrito completo si existe
        const carritoCompleto = document.getElementById('carrito-completo');
        if (carritoCompleto) {
            this.actualizarCarritoCompleto();
        }

        // Actualizar totales
        this.actualizarTotales();
    }

    actualizarMiniCarrito() {
        const miniCarrito = document.getElementById('mini-carrito');
        const itemsContainer = miniCarrito.querySelector('.carrito-items');
        
        itemsContainer.innerHTML = '';

        if (this.cart.length === 0) {
            itemsContainer.innerHTML = '<p class="text-center text-muted">Tu carrito está vacío</p>';
            return;
        }

        this.cart.forEach((item, key) => {
            const itemElement = document.createElement('div');
            itemElement.className = 'carrito-item d-flex align-items-center mb-2';
            itemElement.innerHTML = `
                <img src="${item.producto?.imagen_url || '/img/default-product.png'}" alt="${item.producto?.nombre_producto || 'Producto'}" class="carrito-item-img me-2" style="width: 40px; height: 40px; object-fit: cover;">
                <div class="flex-grow-1">
                    <h6 class="mb-0">${item.producto?.nombre_producto || 'Producto'}</h6>
                    ${item.variante?.nombre_variante ? `<small class="text-muted">Variante: ${item.variante.nombre_variante}</small>` : ''}
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Cantidad: ${item.cantidad}</span>
                        <span class="fw-bold">$${this.formatearPrecio(item.subtotal || 0)}</span>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="carritoController.eliminarItem(this)" data-item-id="${item.id}">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            itemsContainer.appendChild(itemElement);
        });
    }

    actualizarCarritoCompleto() {
        const carritoCompleto = document.getElementById('carrito-completo');
        const itemsContainer = carritoCompleto.querySelector('.carrito-items');
        
        itemsContainer.innerHTML = '';

        if (this.cart.length === 0) {
            itemsContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5>Tu carrito está vacío</h5>
                    <p class="text-muted">Agrega algunos productos para comenzar</p>
                    <a href="/" class="btn btn-primary">Continuar Comprando</a>
                </div>
            `;
            return;
        }

        this.cart.forEach((item, key) => {
            const itemElement = document.createElement('div');
            itemElement.className = 'carrito-item card mb-3';
            itemElement.innerHTML = `
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="${item.producto?.imagen_url || '/img/default-product.png'}" alt="${item.producto?.nombre_producto || 'Producto'}" class="img-fluid rounded">
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1">${item.producto?.nombre_producto || 'Producto'}</h6>
                            ${item.variante?.nombre_variante ? `
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge me-2" style="background-color: ${item.variante.codigo_color || '#ccc'}; width: 20px; height: 20px; border-radius: 50%;"></span>
                                    <small class="text-muted">Variante: ${item.variante.nombre_variante}</small>
                                </div>
                            ` : ''}
                            <p class="text-muted mb-0">Precio unitario: $${this.formatearPrecio(item.producto?.precio || 0)}</p>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="carritoController.actualizarCantidad(this)" data-item-id="${item.id}" data-cantidad="${item.cantidad - 1}">-</button>
                                <input type="number" class="form-control form-control-sm text-center" value="${item.cantidad}" min="1" max="100" readonly>
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="carritoController.actualizarCantidad(this)" data-item-id="${item.id}" data-cantidad="${item.cantidad + 1}">+</button>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="fw-bold">$${this.formatearPrecio(item.subtotal || 0)}</span>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-outline-danger btn-sm" onclick="carritoController.eliminarItem(this)" data-item-id="${item.id}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            itemsContainer.appendChild(itemElement);
        });
    }

    actualizarTotales() {
        const totalElement = document.getElementById('carrito-total');
        const cantidadElement = document.getElementById('carrito-cantidad');
        
        if (totalElement) {
            totalElement.textContent = `$${this.formatearPrecio(this.total)}`;
        }
        
        if (cantidadElement) {
            cantidadElement.textContent = this.cantidadItems;
        }
    }

    actualizarContadorCarrito() {
        const contador = document.getElementById('carrito-contador');
        if (contador) {
            contador.textContent = this.cantidadItems;
            contador.style.display = this.cantidadItems > 0 ? 'inline' : 'none';
        }
    }

    mostrarNotificacion(mensaje, tipo) {
        // Usar SweetAlert2 si está disponible
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: tipo === 'success' ? '¡Éxito!' : 'Error',
                text: mensaje,
                icon: tipo,
                timer: tipo === 'success' ? 2000 : null,
                showConfirmButton: tipo !== 'success'
            });
        } else {
            // Fallback a alert básico
            alert(mensaje);
        }
    }

    mostrarErroresStock(errores) {
        let mensaje = '<ul class="text-left">';
        errores.forEach(error => {
            mensaje += `<li class="mb-1">• ${error}</li>`;
        });
        mensaje += '</ul>';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Stock Insuficiente',
                html: mensaje,
                icon: 'warning',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0088ff'
            });
        } else {
            alert('Stock insuficiente: ' + errores.join(', '));
        }
    }

    formatearPrecio(precio) {
        return new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(precio);
    }
}

// Inicializar el controlador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.carritoController = new CarritoController();
});
