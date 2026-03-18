<div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden group">
    <a href="{{ route('products.show', $product) }}">
        <div class="relative overflow-hidden bg-gray-100 aspect-square">
        @if($product->mainImage)
            @php
                $imgSrc = str_starts_with($product->mainImage->image_path, 'http')
                    ? $product->mainImage->image_path
                    : asset('storage/' . $product->mainImage->image_path);
            @endphp
            <img src="{{ $imgSrc }}"
                alt="{{ $product->name }}"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <span class="text-5xl">📦</span>
            </div>
        @endif

            @if($product->on_sale && $product->discount_percent > 0)
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                    -{{ $product->discount_percent }}%
                </span>
            @endif
            @if($product->is_new)
                <span class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">NOUVEAU</span>
            @endif
        </div>
    </a>

    <div class="p-4">
        @if($product->category)
            <span class="text-xs text-blue-600 font-medium">{{ $product->category->name }}</span>
        @endif
        <a href="{{ route('products.show', $product) }}" class="block">
            <h3 class="font-semibold text-gray-800 mt-1 hover:text-blue-600 transition line-clamp-2 text-sm">
                {{ $product->name }}
            </h3>
        </a>

        <!-- Rating -->
        <div class="flex items-center mt-1">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3 h-3 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
            <span class="text-xs text-gray-500 ml-1">({{ $product->reviews()->count() }})</span>
        </div>

        <div class="flex items-center justify-between mt-3 gap-1">
            <div class="min-w-0">
                <span class="text-sm sm:text-base font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} XOF</span>
                @if($product->compare_price)
                    <span class="text-xs text-gray-400 line-through block sm:inline sm:ml-1">{{ number_format($product->compare_price, 0, ',', ' ') }}</span>
                @endif
            </div>

            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit"
                    class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                    🛒 Ajouter
                </button>
            </form>
        </div>
    </div>
</div>
