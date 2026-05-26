<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ $feedTitle }}</title>
    <link>{{ route('evenements.index') }}</link>
    <description>{{ $feedDesc }}</description>
    <language>fr</language>
    <atom:link href="{{ $feedUrl }}" rel="self" type="application/rss+xml"/>
    <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
@foreach($items as $item)
    <item>
        <title><![CDATA[{{ $item->title }}]]></title>
        <link>{{ route('evenements.show', $item->slug) }}</link>
        <guid isPermaLink="true">{{ route('evenements.show', $item->slug) }}</guid>
        <pubDate>{{ $item->start_date->toRfc2822String() }}</pubDate>
        <description><![CDATA[{{ Str::limit(strip_tags($item->description ?? ''), 300) }}
📅 {{ $item->start_date->translatedFormat('d F Y') }}
{{ $item->is_online ? '🌐 En ligne' : '📍 ' . $item->location }}]]></description>
        @if($item->type)
        <category>{{ $item->type }}</category>
        @endif
    </item>
@endforeach
</channel>
</rss>
