@props(['disabled' => false, 'hasError' => false])

@php
$classes = 'block w-full rounded-md shadow-sm transition duration-150 ease-in-out sm:text-sm';
$classes .= $hasError || ($attributes->has('error') && $attributes->get('error'))
    ? ' border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
    : ' border-gray-300 focus:border-indigo-500 focus:ring-indigo-500';
$classes .= $disabled ? ' bg-gray-50 cursor-not-allowed' : ' bg-white';
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>