@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input id="{{ $name }}"
           type="{{ $type }}"
           name="{{ $name }}"
           value="{{ old($name, $value) }}"
           @error($name) aria-invalid="true" @enderror
           {{ $attributes->merge([
               'class' => 'mt-1 block w-full rounded '
                   . ($errors->has($name)
                       ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                       : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500'),
           ]) }}>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
