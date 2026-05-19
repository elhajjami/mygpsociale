@props(['disabled' => false, 'type' => 'text'])

<input @disabled($disabled) type="{{ $type }}" {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
