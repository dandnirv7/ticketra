<button {{ $attributes->merge(['type' => 'submit', 'class' => 'brutal-btn bg-accent-red text-white hover:bg-accent-red/80']) }}>
    {{ $slot }}
</button>
