@php
    $url = Storage::url($getState());
@endphp

<a href="{{ $url }}" download class="text-primary-600 hover:underline">
    Download
</a>
