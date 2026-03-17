<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-amber border border-transparent rounded-md font-semibold text-xs text-ink uppercase tracking-widest hover:bg-amber-lt focus:outline-none focus:ring-2 focus:ring-amber focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
