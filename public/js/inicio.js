
        // Slider functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const totalSlides = slides.length;
        
        function showSlide(index) {
            if (index >= totalSlides) {
                currentSlide = 0;
            } else if (index < 0) {
                currentSlide = totalSlides - 1;
            } else {
                currentSlide = index;
            }
            
            const slider = document.getElementById('slider');
            slider.style.transform = `translateX(-${currentSlide * 20}%)`;
            
            // Update dots
            dots.forEach(dot => dot.classList.remove('active'));
            dots[currentSlide].classList.add('active');
        }
        
        document.getElementById('next').addEventListener('click', () => {
            showSlide(currentSlide + 1);
        });
        
        document.getElementById('prev').addEventListener('click', () => {
            showSlide(currentSlide - 1);
        });
        
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                showSlide(parseInt(dot.getAttribute('data-slide')));
            });
        });
        
        // Auto slide change
        setInterval(() => {
            showSlide(currentSlide + 1);
        }, 5000);
        
        // Products slider functionality
        let currentProductSlide = 0;
        const productSlideItems = document.querySelectorAll('.product-slide-item');
        const totalProductSlides = productSlideItems.length;
        const productsSlide = document.getElementById('products-slide');
        
        function updateProductsSlider() {
            const itemWidth = productSlideItems[0].offsetWidth;
            productsSlide.style.transform = `translateX(-${currentProductSlide * itemWidth}px)`;
        }
        
        document.getElementById('products-next').addEventListener('click', () => {
            if (currentProductSlide < totalProductSlides - 4) {
                currentProductSlide++;
                updateProductsSlider();
            }
        });
        
        document.getElementById('products-prev').addEventListener('click', () => {
            if (currentProductSlide > 0) {
                currentProductSlide--;
                updateProductsSlider();
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', updateProductsSlider);
        
        // Modal functionality
        const cartBtn = document.getElementById('cart-btn');
        const cartModal = document.getElementById('cart-modal');
        const closeBtns = document.querySelectorAll('.close');
       
        cartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            updateCartModal();
            cartModal.style.display = 'block';
        });
        
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                cartModal.style.display = 'none';
            });
        });
        
        window.addEventListener('click', (e) => {
            if (e.target === cartModal) {
                cartModal.style.display = 'none';
            }
        });
        
        // Cart functionality
        let cart = [];
        
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const price = parseFloat(button.getAttribute('data-price'));
                
                // Check if item already in cart
                const existingItem = cart.find(item => item.id === id);
                
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({
                        id,
                        name,
                        price,
                        quantity: 1
                    });
                }
                
                updateCartCount();
                showAddedToCart(name);
            });
        });
        
        function updateCartCount() {
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            document.getElementById('cart-count').textContent = count;
        }
        
        function updateCartModal() {
            const cartItems = document.getElementById('cart-items');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-gray-600 text-center py-4">Tu carrito está vacío</p>';
                checkoutBtn.disabled = true;
            } else {
                let html = '';
                let subtotal = 0;
                
                cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    
                    html += `
                        <div class="cart-item flex justify-between items-center py-3 border-b">
                            <div>
                                <h4 class="font-bold">${item.name}</h4>
                                <p class="text-gray-600">$${item.price.toLocaleString()}</p>
                            </div>
                            <div class="flex items-center">
                                <button class="decrease-quantity px-2 py-1 bg-gray-200 rounded" data-id="${item.id}">-</button>
                                <span class="mx-2">${item.quantity}</span>
                                <button class="increase-quantity px-2 py-1 bg-gray-200 rounded" data-id="${item.id}">+</button>
                                <button class="remove-item ml-4 text-red-500" data-id="${item.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                cartItems.innerHTML = html;
                document.getElementById('cart-subtotal').textContent = `$${subtotal.toLocaleString()}`;
                document.getElementById('cart-total').textContent = `$${subtotal.toLocaleString()}`;
                checkoutBtn.disabled = false;
                
                // Add event listeners to quantity buttons
                document.querySelectorAll('.increase-quantity').forEach(button => {
                    button.addEventListener('click', () => {
                        const id = button.getAttribute('data-id');
                        const item = cart.find(item => item.id === id);
                        if (item) {
                            item.quantity += 1;
                            updateCartModal();
                            updateCartCount();
                        }
                    });
                });
                
                document.querySelectorAll('.decrease-quantity').forEach(button => {
                    button.addEventListener('click', () => {
                        const id = button.getAttribute('data-id');
                        const item = cart.find(item => item.id === id);
                        if (item && item.quantity > 1) {
                            item.quantity -= 1;
                            updateCartModal();
                            updateCartCount();
                        }
                    });
                });
                
                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', () => {
                        const id = button.getAttribute('data-id');
                        cart = cart.filter(item => item.id !== id);
                        updateCartModal();
                        updateCartCount();
                    });
                });
            }
        }
        
        function showAddedToCart(productName) {
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg flex items-center';
            notification.innerHTML = `
                <i class="fas fa-check-circle mr-2"></i>
                ${productName} agregado al carrito
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-1');
                setTimeout(() => {
                    notification.remove();
                }, 1);
            }, 3000);
        }
        
        
        // Checkout button
        document.getElementById('checkout-btn').addEventListener('click', () => {
            alert('Compra finalizada (simulado)\nTotal: ' + document.getElementById('cart-total').textContent);
            cart = [];
            updateCartCount();
            cartModal.style.display = 'none';
        });
        
        // Dropdown menu functionality
        const dropdowns = document.querySelectorAll('.dropdown');
        
        dropdowns.forEach(dropdown => {
            const button = dropdown.querySelector('button');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            button.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
            
            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
   