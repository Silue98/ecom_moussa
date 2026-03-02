<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; }
        .body { padding: 30px; }
        .order-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .total-row { font-weight: bold; font-size: 18px; color: #2563eb; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 14px 28px; border-radius: 6px; text-decoration: none; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>✅ Commande confirmée !</h1>
        <p>N° {{ $order->order_number }}</p>
    </div>

    <div class="body">
        <p>Bonjour <strong>{{ $order->shipping_name }}</strong>,</p>
        <p>Nous avons bien reçu votre commande. Elle est en cours de traitement.</p>

        <div class="order-box">
            <h3 style="margin-top:0">Récapitulatif</h3>
            @foreach($order->items as $item)
            <div class="row">
                <span>{{ $item->name }} × {{ $item->quantity }}</span>
                <span>{{ number_format($item->total, 0, ',', ' ') }} XOF</span>
            </div>
            @endforeach

            <div class="row">
                <span>Livraison</span>
                <span>{{ $order->shipping_amount > 0 ? number_format($order->shipping_amount, 0, ',', ' ') . ' XOF' : 'Gratuite' }}</span>
            </div>
            <div class="row">
                <span>TVA (20%)</span>
                <span>{{ number_format($order->tax_amount, 0, ',', ' ') }} XOF</span>
            </div>
            <div class="row total-row">
                <span>Total</span>
                <span>{{ number_format($order->total, 0, ',', ' ') }} XOF</span>
            </div>
        </div>

        <p><strong>Adresse de livraison :</strong><br>
        {{ $order->shipping_address }}, {{ $order->shipping_city }}</p>

        <p><strong>Mode de paiement :</strong> {{ $order->payment_method_label }}</p>

        <p>Merci pour votre confiance ! 🙏</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} — Votre Boutique
    </div>
</div>
</body>
</html>
