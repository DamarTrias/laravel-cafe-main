<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 border border-danger border-opacity-90 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-danger hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg']) }} style="background: rgba(220, 53, 69, 0.4) !important; border: 2px solid rgba(220, 53, 69, 1.0) !important; color: white !important; filter: drop-shadow(0 0 5px rgba(220, 53, 69, 0.4));">
    {{ $slot }}
</button>
