<button {{ $attributes->merge(['type' => 'button', 'class' => 'brutal-btn bg-white text-foreground hover:bg-gray-100']) }}>
    {{ $slot }}
</button>
