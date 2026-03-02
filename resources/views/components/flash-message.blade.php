@if(session('success') || session('error') || session('warning') || session('info'))
<div
  x-data="{ show: true }"
  x-show="show"
  x-init="setTimeout(() => show = false, 5000)"
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0 translate-y-2"
  x-transition:enter-end="opacity-100 translate-y-0"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed bottom-6 right-6 z-50 max-w-sm w-full"
>
  @if(session('success'))
  <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl shadow-lg p-4 flex items-start gap-3">
    <span class="text-xl flex-shrink-0">✅</span>
    <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
    <button @click="show = false" class="text-green-400 hover:text-green-600 flex-shrink-0">✕</button>
  </div>
  @endif

  @if(session('error'))
  <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl shadow-lg p-4 flex items-start gap-3">
    <span class="text-xl flex-shrink-0">❌</span>
    <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
    <button @click="show = false" class="text-red-400 hover:text-red-600 flex-shrink-0">✕</button>
  </div>
  @endif

  @if(session('warning'))
  <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl shadow-lg p-4 flex items-start gap-3">
    <span class="text-xl flex-shrink-0">⚠️</span>
    <div class="flex-1 text-sm font-medium">{{ session('warning') }}</div>
    <button @click="show = false" class="text-yellow-400 hover:text-yellow-600 flex-shrink-0">✕</button>
  </div>
  @endif

  @if(session('info'))
  <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl shadow-lg p-4 flex items-start gap-3">
    <span class="text-xl flex-shrink-0">ℹ️</span>
    <div class="flex-1 text-sm font-medium">{{ session('info') }}</div>
    <button @click="show = false" class="text-blue-400 hover:text-blue-600 flex-shrink-0">✕</button>
  </div>
  @endif
</div>
@endif
