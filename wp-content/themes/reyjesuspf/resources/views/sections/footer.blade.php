<footer class="bg-slate-800 text-white" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Pie de página</h2>

    <div class="mx-auto max-w-7xl px-6 pb-8 pt-16 sm:pt-24 lg:px-8 lg:pt-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-16">
            <div class="space-y-6 pr-8">
                <h3 class="text-2xl font-bold tracking-tight text-white">
                    <?php the_field('titulo_del_footer', 'option'); ?>
                </h3>
                <p class="text-base leading-6 text-slate-300">
                    <?php the_field('descripcion_del_footer', 'option'); ?>
                </p>
                <form class="flex w-full max-w-md gap-x-4">
                    <label for="email-address" class="sr-only">Correo electrónico</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required
                        class="min-w-0 flex-auto rounded-md border-0 bg-white/10 px-3.5 py-2 text-white shadow-sm ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6"
                        placeholder="Ingresa tu correo">
                    <button type="submit"
                        class="flex-none rounded-full bg-blue-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500">
                        Suscribirme
                    </button>
                </form>
                <p class="text-xs text-slate-400">
                    Al suscribirte automáticamente estas aceptando los <a href="#"
                        class="underline hover:text-white">términos y condiciones</a>.
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-3 lg:mt-0">
                <div>
                    <h4 class="text-sm font-semibold leading-6 text-white">Mapa del sitio</h4>
                    <?php
// Para leer desde una página de opciones, se añade 'option' como segundo parámetro.
if ( have_rows( 'mapa_del_sitio', 'option' ) ) :
?>
                    <ul role="list" class="mt-6 space-y-4">
                        <?php
        // El bucle también necesita el parámetro 'option'.
        while ( have_rows( 'mapa_del_sitio', 'option' ) ) : the_row();

            if ( get_row_layout() == 'sitio' ) :
            
                // 'get_sub_field' también necesita el parámetro 'option'.
                $enlace = get_sub_field( 'link_de_sitio' );
                $titulo = get_sub_field( 'titulo_de_sitio' );

                if ( $enlace && $titulo ) :
?>
                        <li>
                            <a href="<?php echo $enlace['url']; ?>" target="<?php echo $enlace['target'] ? $enlace['target'] : '_self'; ?>"
                                class="text-sm leading-6 text-slate-300 hover:text-white">
                                <?php echo $titulo; ?>
                            </a>
                        </li>
                        <?php
                endif;
            endif; 
        endwhile;
?>
                    </ul>
                    <?php
endif;
?>
                </div>
                <div class="mt-10 sm:mt-0">
                    <h4 class="text-sm font-semibold leading-6 text-white">Unirme</h4>
                    <?php
                    // Para leer desde una página de opciones, se añade 'option' como segundo parámetro.
                    if ( have_rows( 'campo_de_unirme', 'option' ) ) :
                    ?>
                    <ul role="list" class="mt-6 space-y-4">
                        <?php
                     // El bucle también necesita el parámetro 'option'.
                    while ( have_rows( 'campo_de_unirme', 'option' ) ) : the_row();

                     if ( get_row_layout() == 'unirme' ) :
            
                    // 'get_sub_field' también necesita el parámetro 'option'.
                    $link = get_sub_field( 'link_de_unirme' );
                    $title = get_sub_field( 'titulo_de_unirme' );

                     if ( $link && $title ) :
                    ?>
                        <li>
                            <a href="<?php echo $link['url']; ?>" target="<?php echo $link['target'] ? $link['target'] : '_self'; ?>"
                                class="text-sm leading-6 text-slate-300 hover:text-white">
                                <?php echo $title; ?>
                            </a>
                        </li>
                        <?php
                    endif;
                    endif; 
                    endwhile;
                    ?>
                    </ul>
                    <?php
                    endif;
                    ?>
                </div>
                <div>
                    <h4 class="text-sm font-semibold leading-6 text-white">Síguenos</h4>
                    <ul role="list" class="mt-6 space-y-4">
                        <li>
                            <a href="https://www.facebook.com/share/16vRnU2dDU/"
                                class="flex items-center gap-x-3 group">
                                <i
                                    class="fa-brands fa-facebook text-xl text-slate-400 group-hover:text-white transition-colors"></i>
                                <span
                                    class="text-sm leading-6 text-slate-300 group-hover:text-white transition-colors">Facebook</span>
                            </a>
                        </li>

                        <li>
                            <a href="https://www.instagram.com/elreyjesuspuntofijo?igsh=M3E4Z2VvOGhvemRw"
                                class="flex items-center gap-x-3 group">
                                <i
                                    class="fa-brands fa-instagram text-xl text-slate-400 group-hover:text-white transition-colors"></i>
                                <span
                                    class="text-sm leading-6 text-slate-300 group-hover:text-white transition-colors">Instagram</span>
                            </a>
                        </li>

                        <li>
                            <a href="https://www.youtube.com/@Elreyjesuspuntofijooficial"
                                class="flex items-center gap-x-3 group">
                                <i
                                    class="fa-brands fa-youtube text-xl text-slate-400 group-hover:text-white transition-colors"></i>
                                <span
                                    class="text-sm leading-6 text-slate-300 group-hover:text-white transition-colors">Youtube</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-16 border-t border-white/10 pt-8 sm:mt-20 lg:mt-24 sm:flex sm:items-center sm:justify-between">
            <p class="text-xs leading-5 text-slate-400">&copy; <span id="currentYear"></span> El Rey Jesus. All rights
                reserved.</p>
            <div class="mt-4 sm:mt-0 flex items-center space-x-4 text-xs leading-5 text-slate-400">
                <a href="#" class="hover:text-white">Políticas de Privacidad</a>
                <a href="#" class="hover:text-white">Términos y condiciones</a>
                <a href="#" class="hover:text-white">Configuración de Cookies</a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Tu script ahora funcionará porque existe un elemento con id="currentYear"
    document.getElementById('currentYear').textContent = new Date().getFullYear();
</script>
