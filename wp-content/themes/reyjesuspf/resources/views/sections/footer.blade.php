<footer class="bg-slate-800 text-white" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Pie de página</h2>

    <div class="mx-auto max-w-7xl px-6 pb-8 pt-16 sm:pt-24 lg:px-8 lg:pt-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-16">
            <div class="space-y-6 pr-8">
                <div class="flex items-center gap-3">

                    <img src="<?php the_field('logoblanco', 'option'); ?>" alt="Logo" class="w-16">
                    <p class="px-4 text-base font-medium leading-6 text-slate-200">
                        El Rey Jesús Punto Fijo
                    </p>

                </div>
                <p class="text-base leading-6 text-slate-300">
                    <?php the_field('titulo_del_footer', 'option'); ?>
                </p>
                <p class="text-base leading-6 text-slate-300">
                    <?php the_field('descripcion_del_footer', 'option'); ?>
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
                    <ul role="list" class="mt-6 space-y-4">
                        <li>
                            <a href="/siembra" class="text-sm leading-6 text-slate-300 hover:text-white">
                                <span
                                    class="text-sm leading-6 text-slate-300 group-hover:text-white transition-colors">Sembrar</span>
                            </a>
                        </li>
                        <li>
                            <a href="/contacto" class="text-sm leading-6 text-slate-300 hover:text-white">
                                <span
                                    class="text-sm leading-6 text-slate-300 group-hover:text-white transition-colors">Contacto</span>
                            </a>
                        </li>
                        <?php if (!is_user_logged_in()) : ?>
                        <li>
                            <a href="/login" class="text-sm leading-6 text-slate-300 hover:text-white">
                                <span
                                    class="text-sm leading-6 text-slate-300 group-hover:text-white transition-colors">Iniciar
                                    sesión</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (!is_user_logged_in()) : ?>
                        <li>
                            <a href="/sign-up" class="text-sm leading-6 text-slate-300 hover:text-white">
                                <span
                                    class="text-sm leading-6 text-slate-300 group-hover:text-white transition-colors">Registrarme</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
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
