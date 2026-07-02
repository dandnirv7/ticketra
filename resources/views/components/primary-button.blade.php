<button {{ $attributes->merge(['type' => 'submit', 'class' => 'brutal-btn bg-background text-foreground hover:bg-background/80']) }}>
    {{ $slot }}
</button>
