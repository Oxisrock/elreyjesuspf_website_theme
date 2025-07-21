@extends('layouts.app')

@section('content')
    @include('partials.summarypage.summary')
    @include('partials.speakerspage.speakers')
    @include('partials.programapage.programa')
    @include('partials.preguntaspage.preguntas')
    @include('partials.otroseventspage.otrosevents')
    @include('partials.unetepage.unete')
    @include('partials.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection