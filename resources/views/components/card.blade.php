@props(['class' => '', 'padding' => 'p-6'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 {{ $padding }} {{ $class }}">
    {{ $slot }}
</div>

