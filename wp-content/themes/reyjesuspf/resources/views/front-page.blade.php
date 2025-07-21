{{--
    Template Name: Front Page
--}}

@extends('layouts.app')

@section('content')
    @include('partials.heropage.heropage')
    @include('partials.pasodefe.pasodefe')
    @include('partials.multimediapage.multimedia')
    @include('partials.eventspage.events')
    @include('partials.nuestrahistoriapage.nuestrahistoria')
    @include('partials.new-pastora.pastora')
    @include('partials.contact.contactn')
{{--    
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection
