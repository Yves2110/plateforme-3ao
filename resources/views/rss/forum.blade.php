<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ $feedTitle }}</title>
    <link>{{ route('forum.index') }}</link>
    <description>{{ $feedDesc }}</description>
    <language>fr</language>
    <atom:link href="{{ $feedUrl }}" rel="self" type="application/rss+xml"/>
    <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
@foreach($items as $item)
    <item>
        <title><![CDATA[{{ $item->title }}]]></title>
        <link>{{ route('thread.show', [$item->category, $item->slug]) }}</link>
        <guid isPermaLink="true">{{ route('thread.show', [$item->category, $item->slug]) }}</guid>
        <pubDate>{{ $item->created_at->toRfc2822String() }}</pubDate>
        <author>{{ $item->author?->name }}</author>
        <description><![CDATA[{{ Str::limit(strip_tags($item->body ?? ''), 300) }}]]></description>
        <category>{{ $item->category }}</category>
    </item>
@endforeach
</channel>
</rss>
