@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-border bg-cream text-ink focus:border-amber focus:ring-amber rounded-md shadow-sm']) }}>
