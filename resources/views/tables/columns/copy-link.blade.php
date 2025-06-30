@php
    $url = Storage::url($getState());
@endphp

<div x-data="{ copied: false }">
    <button x-on:click="navigator.clipboard.writeText('{{ $url }}'); copied = true;"
        class="text-blue-600 underline">
        Copy Link
    </button>
    <span x-show="copied" class="text-green-600 ml-2">Copied!</span>
</div>
