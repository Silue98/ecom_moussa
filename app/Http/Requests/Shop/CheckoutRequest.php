<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_name'    => ['required', 'string', 'max:100'],
            'shipping_email'   => ['required', 'email', 'max:150'],
            'shipping_phone'   => ['nullable', 'string', 'max:20'],   // optionnel
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city'    => ['required', 'string', 'max:100'],
            'shipping_zip'     => ['nullable', 'string', 'max:10'],   // optionnel (pas toujours dispo en CI)
            'shipping_state'   => ['nullable', 'string', 'max:100'],
            'shipping_country' => ['nullable', 'string', 'max:100'],  // géré côté serveur si absent
            'payment_method'   => ['required', 'in:cod'],
            'notes'            => ['nullable', 'string', 'max:500'],
            'delivery_type'    => ['required', 'in:pickup,delivery'],

            // Facturation
            'billing_same'    => ['nullable', 'boolean'],
            'billing_name'    => ['nullable', 'string', 'max:100'],
            'billing_email'   => ['nullable', 'email', 'max:150'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'billing_city'    => ['nullable', 'string', 'max:100'],
            'billing_zip'     => ['nullable', 'string', 'max:10'],
            'billing_country' => ['nullable', 'string', 'max:100'],

            // Invité (si non connecté)
            'guest_email'     => ['nullable', 'email', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_name.required'    => 'Le nom complet est obligatoire.',
            'shipping_email.required'   => "L'adresse e-mail est obligatoire.",
            'shipping_email.email'      => "L'adresse e-mail n'est pas valide.",
            'shipping_address.required' => "L'adresse de livraison est obligatoire.",
            'shipping_city.required'    => 'La ville est obligatoire.',
            'payment_method.required'   => 'Veuillez choisir un mode de paiement.',
            'payment_method.in'         => 'Le mode de paiement sélectionné est invalide.',
            'delivery_type.required'    => 'Veuillez choisir entre retrait en boutique ou livraison.',
            'delivery_type.in'          => 'Le mode de livraison sélectionné est invalide.',
        ];
    }
}