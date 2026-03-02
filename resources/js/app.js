import './bootstrap';

// Add to cart via AJAX
document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        e.preventDefault();
        const productId = btn.dataset.productId;
        const qty = btn.closest('form')?.querySelector('[name="quantity"]')?.value || 1;

        try {
            const response = await fetch('/panier/ajouter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ product_id: productId, quantity: qty }),
            });

            const data = await response.json();
            if (data.success) {
                const counter = document.getElementById('cart-count');
                if (counter) counter.textContent = data.count;

                // Show notification
                const notif = document.createElement('div');
                notif.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 text-sm font-medium transition-all';
                notif.textContent = '✅ ' + data.message;
                document.body.appendChild(notif);
                setTimeout(() => notif.remove(), 3000);
            }
        } catch (e) {
            console.error('Erreur panier:', e);
        }
    });
});
