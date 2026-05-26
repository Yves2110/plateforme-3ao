<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ $feedTitle }}</title>
    <link>{{ url('/') }}</link>
    <description>{{ $feedDesc }}</description>
    <language>fr</language>
    <atom:link href="{{ $feedUrl }}" rel="self" type="application/rss+xml"/>
    <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
@foreach($items as $item)
    <item>
        <title><![CDATA[{{ $item->title }}]]></title>
        <link>{{ route('actualites.show', $item->slug) }}</link>
        <guid isPermaLink="true">{{ route('actualites.show', $item->slug) }}</guid>
        <pubDate>{{ $item->published_at?->toRfc2822String() }}</pubDate>
        <description><![CDATA[{{ Str::limit(strip_tags($item->content), 300) }}]]></description>
        @if($item->category)
        <category>{{ $item->category }}</category>
        @endif
    </item>
@endforeach
</channel>
</rss>
