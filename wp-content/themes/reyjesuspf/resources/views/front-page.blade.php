{{--
    Template Name: Front Page
--}}

@extends('layouts.app')

@section('content')
    @if(is_user_logged_in())
        {{-- Vista para usuarios logueados --}}
        <div class="bg-gray-100 min-h-screen py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                    <div class="p-6 bg-blue-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h1 class="text-2xl font-bold">¡Bienvenido de vuelta, {{ wp_get_current_user()->display_name }}!</h1>
                                <p class="text-blue-100">Has iniciado sesión exitosamente.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Mi Perfil</h3>
                                <p class="text-gray-600 mb-4">Gestiona tu información personal y preferencias.</p>
                                <a href="/wp-admin/profile.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">Ver Perfil</a>
                            </div>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Eventos</h3>
                                <p class="text-gray-600 mb-4">Ve los próximos eventos y actividades.</p>
                                <a href="/eventos" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">Ver Eventos</a>
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <a href="<?php echo wp_logout_url(home_url('/')); ?>" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition duration-200">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Vista normal para usuarios no logueados --}}
        @include('partials.Homepage.heropage.heropage')
        @include('partials.Homepage.pasodefe.pasodefe')
        @include('partials.Homepage.multimediapage.multimedia')
        @include('partials.Homepage.eventspage.events')
        @include('partials.Homepage.nuestrahistoriapage.nuestrahistoria')
        @include('partials.Homepage.new-pastora.pastora')
        @include('partials.Homepage.contact.contactn')
    @endif
{{--    
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection
