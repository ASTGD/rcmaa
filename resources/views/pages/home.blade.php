<x-layout :title="$title">
    @include('partials.home.hero')
    @include('partials.home.about')
    {{-- The association asked for the Convenor's and Member Secretary's messages
         on the front page — they were only on /committee, where nobody found
         them. They introduce the people whose committee follows. Side by side
         here, at their request; /committee keeps them full width. --}}
    @include('partials.leadership-messages', ['columns' => 2])
    @include('partials.home.committee')
    @include('partials.home.events')
    @include('partials.home.registration')
    @include('partials.home.directory')
    @include('partials.home.gallery')
    @include('partials.home.sponsors')
</x-layout>
