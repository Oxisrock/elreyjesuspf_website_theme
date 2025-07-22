{{--
    Template Name: Yoconstruyo page
--}}

@extends('layouts.app')

@section('content')

    @include('partials.Yoconstruyo.yoconstruyopage.yoconstruyo')
    @include('partials.Yoconstruyo.historiayoconstruyopage.historiayoconstruyo')
    @include('partials.NuestraIglesiapage.aportepage.aporte')
    @include('partials.Homepage.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection