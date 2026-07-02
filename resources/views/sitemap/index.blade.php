<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ url('/film') }}</loc>
        <priority>0.9</priority>
        <changefreq>daily</changefreq>
    </url>
    <!-- /auth di-skip: halaman login/register gak berguna buat indexing -->
    @foreach($films as $film)
    <url>
        <loc>{{ url('/film/' . $film->id) }}</loc>
        <lastmod>{{ $film->updated_at->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <priority>0.8</priority>
        <changefreq>weekly</changefreq>
    </url>
    @endforeach
</urlset>
