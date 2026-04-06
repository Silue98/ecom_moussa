<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mise à jour commande</title>
<style>
  body { margin:0; padding:0; font-family:Arial,sans-serif; background:#f5f5f5; color:#333; }
  .wrapper { max-width:600px; margin:30px auto; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.08); }
  .header { padding:30px; text-align:center; color:white; }
  .header h1 { margin:0; font-size:1.4rem; }
  .body { padding:30px; }
  .status-box { text-align:center; padding:24px; border-radius:10px; margin:20px 0; }
  .status-icon { font-size:3rem; margin-bottom:8px; }
  .status-text { font-size:1.2rem; font-weight:700; }
  .info-box { background:#f8fafc; border-radius:6px; padding:16px; margin:16px 0; font-size:.9rem; }
  .info-box p { margin:4px 0; }
  .btn { display:inline-block; padding:12px 28px; border-radius:6px; text-decoration:none; font-weight:bold; margin:16px 0; color:white; }
  .footer { background:#f8fafc; padding:20px; text-align:center; font-size:.8rem; color:#94a3b8; }
</style>
</head>
<body>
<div class="wrapper">
  @php
    $config = match($order->status) {
      'processing' => ['bg' => '#eff6ff', 'border' => '#3b82f6', 'icon' => '⚙️', 'color' => '#1d4ed8', 'header_bg' => '#3b82f6'],
      'shipped'    => ['bg' => '#eff6ff', 'border' => '#6366f1', 'icon' => '🚚', 'color' => '#4338ca', 'header_bg' => '#6366f1'],
      'delivered'  => ['bg' => '#f0fdf4', 'border' => '#22c55e', 'icon' => '✅', 'color' => '#15803d', 'header_bg' => '#16a34a'],
      'cancelled'  => ['bg' => '#fef2f2', 'border' => '#ef4444', 'icon' => '❌', 'color' => '#dc2626', 'header_bg' => '#dc2626'],
      default      => ['bg' => '#f8fafc', 'border' => '#94a3b8', 'icon' => '📦', 'color' => '#475569', 'header_bg' => '#64748b'],
    };
  @endphp

  <div class="header" style="background: {{ $config['header_bg'] }}">
    <h1>{{ $config['icon'] }} Commande mise à jour</h1>
    <p style="margin:8px 0 0; opacity:.9;">Commande #{{ $order->order_number }}</p>
  </div>

  <div class="body">
    <p>Bonjour <strong>{{ $order->shipping_name }}</strong>,</p>
    <p>Le statut de votre commande a été mis à jour.</p>

    <div class="status-box" style="background: {{ $config['bg'] }}; border: 2px solid {{ $config['border'] }}">
      <div class="status-icon">{{ $config['icon'] }}</div>
      <div class="status-text" style="color: {{ $config['color'] }}">{{ $order->status_label }}</div>
    </div>

    <div class="info-box">
      <p><strong>N° de commande :</strong> {{ $order->order_number }}</p>
      <p><strong>Total :</strong> {{ number_format($order->total, 2) }} FCFA</p>
      @if($order->tracking_number)
      <p><strong>N° de suivi :</strong> {{ $order->tracking_number }}</p>
      @endif
    </div>

    @if($order->status === 'shipped')
    <p style="background:#fffbeb; padding:12px 16px; border-radius:6px; font-size:.88rem;">
      🚚 Votre colis est en route ! Vous pouvez suivre votre livraison avec le numéro de suivi ci-dessus.
    </p>
    @endif

    @if($order->status === 'delivered')
    <p style="background:#f0fdf4; padding:12px 16px; border-radius:6px; font-size:.88rem;">
      🎉 Votre commande a été livrée ! Nous espérons que vous êtes satisfait de votre achat.
      N'hésitez pas à laisser un avis sur les produits reçus.
    </p>
    @endif

    @if($order->status === 'cancelled')
    <p style="background:#fef2f2; padding:12px 16px; border-radius:6px; font-size:.88rem;">
      Si vous avez des questions concernant l'annulation, contactez-nous à support@ecommerce.ma
    </p>
    @endif

    <div style="text-align:center; margin-top:20px;">
      <a href="{{ url('/compte/commandes/' . $order->id) }}" class="btn" style="background: {{ $config['header_bg'] }}">
        Voir ma commande
      </a>
    </div>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} TrustPhone CI Laravel — <a href="mailto:support@ecommerce.ma" style="color:#3b82f6">support@ecommerce.ma</a></p>
  </div>
</div>
</body>
</html>
