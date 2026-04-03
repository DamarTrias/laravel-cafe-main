@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/10 border-white/20 focus:border-[#d4a373] focus:ring-[#d4a373] text-white rounded-md shadow-sm py-2']) }}>
