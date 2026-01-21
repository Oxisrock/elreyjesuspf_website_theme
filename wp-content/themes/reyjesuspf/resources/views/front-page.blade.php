{{--
    Template Name: Front Page
--}}

@extends('layouts.app')

@section('content')
    @include('partials.Homepage.heropage.heropage')
    @include('partials.Homepage.pasodefe.pasodefe')
    @include('partials.Homepage.multimediapage.multimedia')
    @include('partials.Homepage.eventspage.events')
    @include('partials.Homepage.nuestrahistoriapage.nuestrahistoria')
    @include('partials.Homepage.new-pastora.pastora')
    @include('partials.Homepage.contact.contactn')
{{--    
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection
