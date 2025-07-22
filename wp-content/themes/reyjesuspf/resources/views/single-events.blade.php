@extends('layouts.app')

@section('content')
    @include('partials.Seccioneventospage.summarypage.summary')
    @include('partials.Seccioneventospage.speakerspage.speakers')
    @include('partials.Seccioneventospage.programapage.programa')
    @include('partials.Seccioneventospage.preguntaspage.preguntas')
    @include('partials.Seccioneventospage.otroseventspage.otrosevents')
    @include('partials.Seccioneventospage.unetepage.unete')
    @include('partials.Homepage.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection