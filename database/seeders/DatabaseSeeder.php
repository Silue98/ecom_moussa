<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Utilisateurs ─────────────────────────────────────────────
        User::create([
            'name'      => 'Administrateur TrustPhone',
            'email'     => 'admin@trustphone-ci.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Client Test',
            'email'     => 'client@trustphone-ci.com',
            'password'  => Hash::make('password'),
            'role'      => 'customer',
            'is_active' => true,
        ]);

        // ── Paramètres généraux ───────────────────────────────────────
        $settings = [
            // Général
            ['key' => 'site_name',               'value' => 'TrustPhone CI',                   'group' => 'general'],
            ['key' => 'site_email',              'value' => 'contact@trustphone-ci.com',        'group' => 'general'],
            ['key' => 'currency',                'value' => 'FCFA',                             'group' => 'shop'],
            ['key' => 'free_shipping_threshold', 'value' => '50000',                            'group' => 'shop'],
            ['key' => 'shipping_price',          'value' => '2000',                             'group' => 'shop'],
            ['key' => 'tax_rate',                'value' => '0',                                'group' => 'shop'],
            // Boutique
            ['key' => 'shop_name',               'value' => 'TrustPhone CI',                   'group' => 'boutique'],
            ['key' => 'shop_address',            'value' => 'Votre adresse, Abidjan',          'group' => 'boutique'],
            ['key' => 'shop_city',               'value' => "Abidjan, Côte d'Ivoire",          'group' => 'boutique'],
            ['key' => 'shop_phone',              'value' => '+225 07 00 00 00 00',             'group' => 'boutique'],
            ['key' => 'shop_hours',              'value' => 'Lun – Sam : 8h00 – 19h00',       'group' => 'boutique'],
            ['key' => 'shop_gmaps_url',          'value' => 'https://maps.google.com',         'group' => 'boutique'],
            ['key' => 'pickup_enabled',          'value' => '1',                               'group' => 'boutique'],
            ['key' => 'pickup_message',          'value' => 'Venez récupérer votre iPhone directement en boutique, sans frais supplémentaires. Nous vérifions chaque appareil avant remise.', 'group' => 'boutique'],
            ['key' => 'credit_enabled',          'value' => '1',                               'group' => 'boutique'],
            ['key' => 'credit_message',          'value' => '',                                'group' => 'boutique'],
            ['key' => 'credit_conditions',       'value' => '',                                'group' => 'boutique'],
            // Règles échéancier crédit
            ['key' => 'credit_nb_echeances',    'value' => '3',           'group' => 'credit'],
            ['key' => 'credit_pourcentages',     'value' => '30,40,30',    'group' => 'credit'],
            ['key' => 'credit_taux_interet',     'value' => '0',           'group' => 'credit'],
            ['key' => 'credit_montant_min',      'value' => '100000',      'group' => 'credit'],
            ['key' => 'credit_documents',        'value' => "Carte Nationale d'Identité (CNI) valide
Une photo d'identité récente
Justificatif de domicile (facture CIE/SODECI)
Justificatif de revenus ou contrat de travail", 'group' => 'credit'],
        ];
        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        // ── Marque Apple uniquement ───────────────────────────────────
        $apple = Brand::create([
            'name'      => 'Apple',
            'slug'      => 'apple',
            'is_active' => true,
        ]);

        // ── Catégories iPhone ─────────────────────────────────────────
        $cats = [
            ['name' => 'iPhone 16 Series',  'slug' => 'iphone-16-series',  'sort_order' => 1],
            ['name' => 'iPhone 15 Series',  'slug' => 'iphone-15-series',  'sort_order' => 2],
            ['name' => 'iPhone 14 Series',  'slug' => 'iphone-14-series',  'sort_order' => 3],
            ['name' => 'iPhone 13 Series',  'slug' => 'iphone-13-series',  'sort_order' => 4],
            ['name' => 'Accessoires Apple', 'slug' => 'accessoires-apple', 'sort_order' => 5],
        ];
        $catMap = [];
        foreach ($cats as $c) {
            $catMap[$c['slug']] = Category::create(array_merge($c, ['is_active' => true]));
        }

        // ── Produits iPhone ───────────────────────────────────────────
        $products = [
            // iPhone 16 Series
            ['name' => 'iPhone 16 Pro Max 256 Go',    'slug' => 'iphone-16-pro-max-256',   'price' => 1350000, 'compare_price' => 1500000, 'qty' => 15, 'cat' => 'iphone-16-series', 'featured' => true,  'new' => true,  'sale' => true,  'desc' => 'Le plus grand écran ProMotion 6,9". Puce A18 Pro. Titane naturel. Caméra 48 MP Fusion.'],
            ['name' => 'iPhone 16 Pro 128 Go',        'slug' => 'iphone-16-pro-128',       'price' => 1150000, 'compare_price' => null,    'qty' => 20, 'cat' => 'iphone-16-series', 'featured' => true,  'new' => true,  'sale' => false, 'desc' => 'Écran Super Retina XDR 6,3". Puce A18 Pro. Photo/vidéo professionnels.'],
            ['name' => 'iPhone 16 256 Go',            'slug' => 'iphone-16-256',           'price' => 950000,  'compare_price' => null,    'qty' => 25, 'cat' => 'iphone-16-series', 'featured' => true,  'new' => true,  'sale' => false, 'desc' => 'Puce A18, bouton Action et bouton Appareil photo. Tout nouveau design.'],
            ['name' => 'iPhone 16 128 Go',            'slug' => 'iphone-16-128',           'price' => 850000,  'compare_price' => null,    'qty' => 30, 'cat' => 'iphone-16-series', 'featured' => false, 'new' => true,  'sale' => false, 'desc' => 'La puissance d\'A18 dans un format compact. Apple Intelligence intégré.'],
            // iPhone 15 Series
            ['name' => 'iPhone 15 Pro Max 256 Go',    'slug' => 'iphone-15-pro-max-256',   'price' => 1100000, 'compare_price' => 1280000, 'qty' => 18, 'cat' => 'iphone-15-series', 'featured' => true,  'new' => false, 'sale' => true,  'desc' => 'Titane. Caméra 5× zoom optique. Puce A17 Pro. Écran Always-On 6,7".'],
            ['name' => 'iPhone 15 Pro 128 Go',        'slug' => 'iphone-15-pro-128',       'price' => 950000,  'compare_price' => 1100000, 'qty' => 22, 'cat' => 'iphone-15-series', 'featured' => false, 'new' => false, 'sale' => true,  'desc' => 'Châssis titane ultraléger. Puce A17 Pro. Bouton Action personnalisable.'],
            ['name' => 'iPhone 15 256 Go',            'slug' => 'iphone-15-256',           'price' => 780000,  'compare_price' => 900000,  'qty' => 35, 'cat' => 'iphone-15-series', 'featured' => true,  'new' => false, 'sale' => true,  'desc' => 'Dynamic Island. Charge USB-C. Caméra principale 48 MP.'],
            ['name' => 'iPhone 15 128 Go',            'slug' => 'iphone-15-128',           'price' => 680000,  'compare_price' => 780000,  'qty' => 40, 'cat' => 'iphone-15-series', 'featured' => false, 'new' => false, 'sale' => true,  'desc' => 'Dynamic Island et USB-C pour la première fois sur iPhone standard.'],
            // iPhone 14 Series
            ['name' => 'iPhone 14 Pro Max 256 Go',    'slug' => 'iphone-14-pro-max-256',   'price' => 850000,  'compare_price' => 980000,  'qty' => 10, 'cat' => 'iphone-14-series', 'featured' => false, 'new' => false, 'sale' => true,  'desc' => 'Dynamic Island. Caméra 48 MP. Always-On Display. Puce A16 Bionic.'],
            ['name' => 'iPhone 14 128 Go',            'slug' => 'iphone-14-128',           'price' => 580000,  'compare_price' => 680000,  'qty' => 20, 'cat' => 'iphone-14-series', 'featured' => false, 'new' => false, 'sale' => true,  'desc' => 'Crash Detection. Mode Action vidéo. Connectivité satellite d\'urgence.'],
            // iPhone 13 Series
            ['name' => 'iPhone 13 128 Go',            'slug' => 'iphone-13-128',           'price' => 420000,  'compare_price' => 500000,  'qty' => 30, 'cat' => 'iphone-13-series', 'featured' => false, 'new' => false, 'sale' => true,  'desc' => 'Puce A15 Bionic. Mode cinématique. Écran Super Retina XDR. Excellent rapport qualité/prix.'],
            ['name' => 'iPhone 13 Mini 128 Go',       'slug' => 'iphone-13-mini-128',      'price' => 380000,  'compare_price' => 450000,  'qty' => 15, 'cat' => 'iphone-13-series', 'featured' => false, 'new' => false, 'sale' => true,  'desc' => 'Le plus petit iPhone 5G. Puce A15 Bionic. Format ultra compact.'],
            // Accessoires
            ['name' => 'AirPods Pro 2ème génération', 'slug' => 'airpods-pro-2',           'price' => 185000,  'compare_price' => 210000,  'qty' => 50, 'cat' => 'accessoires-apple','featured' => true,  'new' => false, 'sale' => true,  'desc' => 'Réduction de bruit active. Audio spatial personnalisé. Boîtier MagSafe.'],
            ['name' => 'Apple Watch Series 10',       'slug' => 'apple-watch-series-10',   'price' => 320000,  'compare_price' => null,    'qty' => 25, 'cat' => 'accessoires-apple','featured' => true,  'new' => true,  'sale' => false, 'desc' => 'La montre connectée la plus fine. Détection apnée du sommeil. Toujours allumée.'],
            ['name' => 'MagSafe Chargeur 15W',        'slug' => 'magsafe-chargeur',        'price' => 35000,   'compare_price' => 42000,   'qty' => 100,'cat' => 'accessoires-apple','featured' => false, 'new' => false, 'sale' => true,  'desc' => 'Chargeur magnétique certifié Apple. 15W pour iPhone 12 et ultérieur.'],
            ['name' => 'Coque iPhone 16 Pro Silicone','slug' => 'coque-iphone-16-pro-silicone','price'=> 18000, 'compare_price' => 25000,   'qty' => 200,'cat' => 'accessoires-apple','featured' => false, 'new' => true,  'sale' => true,  'desc' => 'Coque officielle Apple en silicone. Compatible MagSafe. Doublure en microfibre.'],
        ];

        foreach ($products as $p) {
            Product::create([
                'name'              => $p['name'],
                'slug'              => $p['slug'],
                'price'             => $p['price'],
                'compare_price'     => $p['compare_price'],
                'quantity'          => $p['qty'],
                'category_id'       => $catMap[$p['cat']]->id,
                'brand_id'          => $apple->id,
                'is_featured'       => $p['featured'],
                'is_new'            => $p['new'],
                'on_sale'           => $p['sale'],
                'is_active'         => true,
                'low_stock_threshold'=> 3,
                'sku'               => 'TP-' . strtoupper(Str::random(6)),
                'short_description' => $p['desc'],
                'description'       => '<p>' . $p['desc'] . '</p><p>Tous nos iPhones sont <strong>neufs, débloqués tous opérateurs</strong> et accompagnés d\'une garantie vendeur de 3 mois. Livraison sécurisée à Abidjan et dans toute la Côte d\'Ivoire.</p><p><strong>Paiement à la réception</strong> — vous vérifiez votre iPhone avant de payer.</p>',
                'meta_title'        => $p['name'] . ' — TrustPhone CI',
                'meta_description'  => $p['desc'] . ' Livraison Abidjan. Paiement à la réception.',
            ]);
        }
    }
}
