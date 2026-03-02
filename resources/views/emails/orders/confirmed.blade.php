<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmation de commande</title>
<style>
  body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
  .wrapper { max-width: 600px; margin: 30px auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
  .header { background: linear-gradient(135deg, #1d4ed8, #3b82f6); padding: 30px; text-align: center; color: white; }
  .header h1 { margin: 0; font-size: 1.5rem; }
  .header p { margin: 8px 0 0; opacity: .9; }
  .body { padding: 30px; }
  .greeting { font-size: 1.1rem; margin-bottom: 16px; }
  .info-box { background: #f0f7ff; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 16px; margin: 20px 0; }
  .info-box p { margin: 4px 0; font-size: .9rem; }
  .info-box strong { color: #1d4ed8; }
  table.items { width: 100%; border-collapse: collapse; margin: 20px 0; }
  table.items th { background: #f8fafc; padding: 10px; text-align: left; font-size: .85rem; color: #64748b; border-bottom: 2px solid #e2e8f0; }
  table.items td { padding: 10px; font-size: .85rem; border-bottom: 1px solid #f1f5f9; }
  .total-row td { font-weight: bold; font-size: .95rem; border-top: 2px solid #e2e8f0; }
  .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-weight: bold; margin: 20px 0; }
  .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: .8rem; color: #94a3b8; }
  .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: .78rem; font-weight: 600; }
  .badge-warning { background: #fef3c7; color: #d97706; }
  .badge-success { background: #dcfce7; color: #16a34a; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>✅ Commande confirmée !</h1>
    <p>Merci pour votre achat, {{ $order->shipping_name }} 🎉</p>
  </div>

  <div class="body">
    <p class="greeting">Bonjour <strong>{{ $order->shipping_name }}</strong>,</p>
    <p>Nous avons bien reçu votre commande et elle est en cours de traitement.</p>

    <div class="info-box">
      <p><strong>N° de commande :</strong> {{ $order->order_number }}</p>
      <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
      <p><strong>Mode de paiement :</strong> {{ $order->payment_method_label }}</p>
      <p><strong>Statut :</strong>
        <span class="badge badge-warning">⏳ En attente de traitement</span>
      </p>
    </div>

    <h3 style="font-size:1rem; margin-bottom:12px;">📦 Détail de la commande</h3>
    <table class="items">
      <thead>
        <tr>
          <th>Produit</th>
          <th style="text-align:center">Qté</th>
          <th style="text-align:right">Prix</th>
          <th style="text-align:right">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
        <tr>
          <td>{{ $item->name }}<br><small style="color:#94a3b8">{{ $item->sku }}</small></td>
          <td style="text-align:center">{{ $item->quantity }}</td>
          <td style="text-align:right">{{ number_format($item->price, 2) }} FCFA</td>
          <td style="text-align:right">{{ number_format($item->total, 2) }} FCFA</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="text-align:right; padding:8px; font-size:.85rem; color:#64748b;">Sous-total</td>
          <td style="text-align:right; padding:8px;">{{ number_format($order->subtotal, 2) }} FCFA</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
          <td colspan="3" style="text-align:right; padding:8px; font-size:.85rem; color:#16a34a;">Réduction ({{ $order->coupon_code }})</td>
          <td style="text-align:right; padding:8px; color:#16a34a;">-{{ number_format($order->discount_amount, 2) }} FCFA</td>
        </tr>
        @endif
        <tr>
          <td colspan="3" style="text-align:right; padding:8px; font-size:.85rem; color:#64748b;">Livraison</td>
          <td style="text-align:right; padding:8px;">
            @if($order->shipping_amount == 0) <span style="color:#16a34a">Gratuite</span> @else {{ number_format($order->shipping_amount, 2) }} FCFA @endif
          </td>
        </tr>
        <tr class="total-row">
          <td colspan="3" style="text-align:right; padding:10px;">TOTAL</td>
          <td style="text-align:right; padding:10px; color:#1d4ed8; font-size:1.1rem;">{{ number_format($order->total, 2) }} FCFA</td>
        </tr>
      </tfoot>
    </table>

    <h3 style="font-size:1rem; margin-bottom:10px;">📍 Adresse de livraison</h3>
    <p style="font-size:.88rem; color:#475569; line-height:1.8;">
      {{ $order->shipping_name }}<br>
      {{ $order->shipping_address }}<br>
      {{ $order->shipping_zip }} {{ $order->shipping_city }}<br>
      {{ $order->shipping_country }}<br>
      @if($order->shipping_phone) 📞 {{ $order->shipping_phone }} @endif
    </p>

    <div style="text-align:center; margin-top:24px;">
      <a href="{{ url('/compte/commandes/' . $order->id) }}" class="btn">
        🔍 Suivre ma commande
      </a>
    </div>

    <p style="font-size:.85rem; color:#64748b; margin-top:20px;">
      Vous recevrez un e-mail dès que votre commande sera expédiée avec votre numéro de suivi.
    </p>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} E-Commerce Laravel. Tous droits réservés.</p>
    <p style="margin-top:6px;">Des questions ? Contactez-nous à <a href="mailto:support@ecommerce.ma" style="color:#3b82f6">support@ecommerce.ma</a></p>
  </div>
</div>
</body>
</html>
