<style>
    /* Tus estilos CSS (con cambios) */
    .thumbnail {
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.3s ease-in-out;
        flex-shrink: 0;
    }

    .thumbnail:hover,
    .thumbnail.active {
        opacity: 1;
    }

    /* --- ESTILOS MODIFICADOS --- */
    .thumbnail-container {
        display: flex;
        /* Hacemos que el contenedor crezca para ocupar el espacio disponible */
        flex: 1;
        overflow-x: hidden;
        scroll-behavior: smooth;
        gap: 0.5rem;
        padding: 0.25rem 0;
        /* Esto previene problemas de desbordamiento en contenedores flex */
        min-width: 0;
    }
    /* --- FIN DE ESTILOS MODIFICADOS --- */

    .thumbnail-container::-webkit-scrollbar {
        display: none;
    }

    .thumbnail-container {
        scrollbar-width: none;
    }

    .main-nav-arrow,
    .thumb-nav-arrow {
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border-radius: 9999px;
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.3s;
        flex-shrink: 0;
    }

    .main-nav-arrow:hover,
    .thumb-nav-arrow:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }

    .main-nav-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }
</style>

<div class="max-w-4xl mx-auto bg-white rounded-lg p-4">
    <div class="text-center pb-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-blue-600 mb-6"><?php the_field('titulo_del_templo'); ?></h2>
        <div class="text-lg text-gray-600 italic"><?php the_field('descripcion_del_templo'); ?></div>
        <div class="text-medium text-black-500 font-bold"><?php the_field('descripcion_del_templo_2'); ?></div>
    </div>

    <?php
    $rows = get_field('fotos_del_templo');
    if ($rows) :
    ?>
    <div class="relative mb-4">
        <img id="main-image" src="<?php echo esc_url($rows[0]['foto']['url']); ?>" alt="<?php echo esc_attr($rows[0]['foto']['alt']); ?>" class="w-full h-auto rounded-md">
        
        <button id="prev-main" class="main-nav-arrow left-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button id="next-main" class="main-nav-arrow right-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <div class="flex items-center gap-2">
        <button id="prev-thumb" class="thumb-nav-arrow">&lt;</button>
        
        <div id="thumbnail-container" class="thumbnail-container">
            <?php
            // Bucle que genera solo las imágenes DENTRO del contenedor
            foreach ($rows as $row) {
                if ($row['acf_fc_layout'] == 'fotos') {
                    $galeria_imagenes = $row['foto'];
                    if ($galeria_imagenes) {
                        ?>
                        <img src="<?php echo esc_url($galeria_imagenes['url']); ?>" alt="<?php echo esc_attr($galeria_imagenes['alt']); ?>" class="thumbnail w-32 h-20 rounded-md">
                        <?php
                    }
                }
            }
            ?>
        </div>
        
        <button id="next-thumb" class="thumb-nav-arrow">&gt;</button>
    </div>
    <?php
    else :
        echo '<p>No hay contenido para mostrar.</p>';
    endif;
    ?>
</div>

<script>
    // Tu JavaScript (con cambios)
    document.addEventListener('DOMContentLoaded', () => {
        const mainImage = document.getElementById('main-image');
        const thumbnails = document.querySelectorAll('.thumbnail');
        if (!mainImage || thumbnails.length === 0) return;
        
        const thumbnailContainer = document.getElementById('thumbnail-container');
        const prevMainBtn = document.getElementById('prev-main');
        const nextMainBtn = document.getElementById('next-main');
        const prevThumbBtn = document.getElementById('prev-thumb');
        const nextThumbBtn = document.getElementById('next-thumb');
        let currentIndex = 0;

        function showImage(index, preventScroll = false) {
            if (index < 0) {
                currentIndex = thumbnails.length - 1;
            } else if (index >= thumbnails.length) {
                currentIndex = 0;
            } else {
                currentIndex = index;
            }
            const thumbSrc = thumbnails[currentIndex].src;
            const newImageSrc = thumbSrc.replace(/-\d+x\d+(?=\.\w+$)/, '');
            mainImage.src = newImageSrc;
            mainImage.alt = thumbnails[currentIndex].alt;
            thumbnails.forEach((thumb, i) => {
                thumb.classList.toggle('active', i === currentIndex);
            });

            // Sincroniza el scroll de las miniaturas con la imagen activa
            if (!preventScroll) {
                thumbnails[currentIndex].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            }
        }

        prevMainBtn.addEventListener('click', () => showImage(currentIndex - 1));
        nextMainBtn.addEventListener('click', () => showImage(currentIndex + 1));
        thumbnails.forEach((thumbnail, index) => {
            thumbnail.addEventListener('click', () => showImage(index));
        });

        // --- LÓGICA DE SCROLL MODIFICADA ---
        prevThumbBtn.addEventListener('click', () => {
            // Se desplaza una cantidad igual al ancho visible del contenedor
            const scrollAmount = thumbnailContainer.clientWidth;
            thumbnailContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });

        nextThumbBtn.addEventListener('click', () => {
            // Se desplaza una cantidad igual al ancho visible del contenedor
            const scrollAmount = thumbnailContainer.clientWidth;
            thumbnailContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        // --- FIN DE LÓGICA DE SCROLL MODIFICADA ---

        if (thumbnails.length > 0) {
            showImage(0, true);
        }
    });
</script>