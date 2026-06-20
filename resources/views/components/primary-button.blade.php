<button {{ $attributes->merge(['type' => 'submit', 'class' => 'brutal-btn bg-pastel-mint text-foreground hover:bg-pastel-mint/80']) }}>
    {{ $slot }}
</button>
