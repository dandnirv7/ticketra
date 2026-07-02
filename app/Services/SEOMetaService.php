<?php

namespace App\Services;

use Illuminate\Support\Str;

class SEOMetaService
{
    private string $title = 'Ticketra. - Bebas Antre, Nonton Asyik';

    private string $description = 'Pesan tiket bioskop instan, pilih kursi favorit, dan kumpulkan promo eksklusif tanpa antre. Booking online bioskop di Jakarta, Bogor, Depok, Tangerang, Bekasi.';

    private string $type = 'website';

    private ?string $image = null;

    private ?string $url = null;

    private ?array $jsonLd = null;

    private string $siteName = 'Ticketra.';

    private string $locale = 'id_ID';

    private string $twitterCard = 'summary_large_image';

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = Str::limit($description, 160);

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function image(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function url(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function jsonLd(?array $data): static
    {
        $this->jsonLd = $data;

        return $this;
    }

    public function renderTitle(): string
    {
        return $this->title;
    }

    public function renderDescription(): string
    {
        return $this->description;
    }

    public function renderMetaTags(): string
    {
        $tags = '';

        $tags .= '<meta name="description" content="' . e($this->description) . '">' . "\n";

        if ($this->url) {
            $tags .= '<link rel="canonical" href="' . e($this->url) . '">' . "\n";
        }

        $tags .= '<meta property="og:site_name" content="' . e($this->siteName) . '">' . "\n";
        $tags .= '<meta property="og:locale" content="' . e($this->locale) . '">' . "\n";
        $tags .= '<meta property="og:type" content="' . e($this->type) . '">' . "\n";
        $tags .= '<meta property="og:title" content="' . e($this->title) . '">' . "\n";
        $tags .= '<meta property="og:description" content="' . e($this->description) . '">' . "\n";

        if ($this->url) {
            $tags .= '<meta property="og:url" content="' . e($this->url) . '">' . "\n";
        }

        if ($this->image) {
            $tags .= '<meta property="og:image" content="' . e($this->image) . '">' . "\n";
            $tags .= '<meta property="og:image:width" content="1200">' . "\n";
            $tags .= '<meta property="og:image:height" content="630">' . "\n";
        }

        $tags .= '<meta name="twitter:card" content="' . e($this->twitterCard) . '">' . "\n";
        $tags .= '<meta name="twitter:title" content="' . e($this->title) . '">' . "\n";
        $tags .= '<meta name="twitter:description" content="' . e($this->description) . '">' . "\n";

        if ($this->image) {
            $tags .= '<meta name="twitter:image" content="' . e($this->image) . '">' . "\n";
        }

        return $tags;
    }

    public function renderJsonLd(): string
    {
        if ($this->jsonLd === null) {
            return '';
        }

        return '<script type="application/ld+json">' . json_encode($this->jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }

    public function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Ticketra.',
            'url' => $this->url ?? config('app.url'),
            'logo' => $this->url ?? config('app.url') . '/favicon.ico',
            'description' => $this->description,
            'foundingDate' => '2024',
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Indonesia',
            ],
            'sameAs' => [
                '#',
                '#',
                '#',
            ],
        ];
    }

    public function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Ticketra.',
            'url' => $this->url ?? config('app.url'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => ($this->url ?? config('app.url')) . '/film?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }
}
