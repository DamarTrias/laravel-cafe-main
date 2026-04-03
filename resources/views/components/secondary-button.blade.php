<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-dark bg-opacity-20 border border-light border-opacity-10 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm']) }} style="background: rgba(255, 255, 255, 0.05) !important;">
    {{ $slot }}
</button>
