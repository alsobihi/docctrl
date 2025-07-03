@props(['checked' => false, 'disabled' => false])

<input type="checkbox"
    {{ $checked ? 'checked' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500']) !!}>
