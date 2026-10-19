// Funcionalidad del panel de administracion
document.addEventListener('DOMContentLoaded', function(){
    autocerrarAlertas();
    handleFiltros();
    handleEliminar();
    handleFormularioProducto();
});

const { animate: animarAdmin } = window.Motion ?? {};

const formatoKAdmin = (precio) => `${Math.round((Number(precio) || 0) / 100) / 10}k$`;

// Los mensajes de exito desaparecen solos
const autocerrarAlertas = ()=>{
    document.querySelectorAll('[data-autocerrar]').forEach(alerta => {
        setTimeout(async () => {
            if(animarAdmin) await animarAdmin(alerta, { opacity: 0, y: -10 }, { duration: 0.3 });
            alerta.remove();
            // quita ?status=... para que el mensaje no vuelva al recargar
            history.replaceState(null, '', location.pathname);
        }, 4000);
    });
}

// Buscador y filtro por categoria del listado
const handleFiltros = ()=>{
    const buscar = document.getElementById('buscar');
    const filtro = document.getElementById('filtro-categoria');
    if(!buscar || !filtro) return;

    const filas = [...document.querySelectorAll('.table__producto')];
    const contador = document.getElementById('contador');
    const sinResultados = document.getElementById('sin-resultados');

    const aplicar = () => {
        const texto = buscar.value.trim().toLowerCase();
        const categoria = filtro.value;
        let visibles = 0;

        filas.forEach(fila => {
            const coincide = fila.dataset.nombre.includes(texto)
                && (!categoria || fila.dataset.categoria === categoria);
            fila.hidden = !coincide;
            if(coincide) visibles++;
        });

        contador.textContent = `${visibles} ${visibles === 1 ? 'producto' : 'productos'}`;
        sinResultados.hidden = visibles > 0 || filas.length === 0;
    };

    buscar.addEventListener('input', aplicar);
    filtro.addEventListener('change', aplicar);
}

// Confirmacion antes de borrar un producto
const handleEliminar = ()=>{
    const dialogo = document.getElementById('confirmar');
    if(!dialogo) return;

    document.querySelectorAll('[data-eliminar]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('confirmar-id').value = btn.dataset.eliminar;
            document.getElementById('confirmar-nombre').textContent = btn.dataset.nombre;
            dialogo.showModal();
            if(animarAdmin) animarAdmin(dialogo, { opacity: [0, 1], scale: [0.9, 1] }, { type: 'spring', stiffness: 300, damping: 22 });
        });
    });

    document.getElementById('confirmar-cancelar').addEventListener('click', () => dialogo.close());
    dialogo.addEventListener('click', e => {
        if(e.target === dialogo) dialogo.close();
    });
}

// Formulario de crear / editar producto
const handleFormularioProducto = ()=>{
    const form = document.getElementById('form-producto');
    if(!form) return;

    const nombre = form.querySelector('#nombre');
    const precio = form.querySelector('#precio');
    const descripcion = form.querySelector('#descripcion');
    const inputImagen = form.querySelector('#imagen');
    const dropzone = document.getElementById('dropzone');
    const contador = document.getElementById('contador-descripcion');

    // Vista previa de la tarjeta en tiempo real
    const actualizarPreview = () => {
        document.getElementById('preview-nombre').textContent = nombre.value || 'Nombre del producto';
        document.getElementById('preview-descripcion').textContent = descripcion.value || 'Aquí aparecerá la descripción del producto.';
        document.getElementById('preview-precio').textContent = formatoKAdmin(precio.value);

        const largo = descripcion.value.trim().length;
        contador.textContent = largo >= 50 ? `${largo} caracteres ✓` : `${largo} / 50 caracteres mínimo`;
        contador.classList.toggle('ok', largo >= 50);
    };
    [nombre, precio, descripcion].forEach(input => input.addEventListener('input', actualizarPreview));
    actualizarPreview();

    const mostrarImagen = (url) => {
        const preview = document.getElementById('imagen-preview');
        preview.src = url;
        preview.hidden = false;
        document.getElementById('preview-img').src = url;
        if(animarAdmin) animarAdmin('#preview-img', { scale: [0.6, 1], rotate: [-15, 0] }, { type: 'spring', stiffness: 200, damping: 14 });
    };

    const procesarArchivo = async (archivo) => {
        if(!archivo || !archivo.type.startsWith('image/')) return;
        mostrarImagen(URL.createObjectURL(archivo));

        // Se convierte a WebP y se reduce en el navegador: subidas mas rapidas y paginas mas livianas
        const optimizado = await convertirAWebp(archivo).catch(() => null);
        if(optimizado && optimizado.size < archivo.size){
            const datos = new DataTransfer();
            datos.items.add(optimizado);
            inputImagen.files = datos.files;
        }
    };

    inputImagen.addEventListener('change', () => procesarArchivo(inputImagen.files[0]));

    // Arrastrar y soltar
    ['dragenter', 'dragover'].forEach(evento => dropzone.addEventListener(evento, e => {
        e.preventDefault();
        dropzone.classList.add('arrastrando');
    }));
    ['dragleave', 'drop'].forEach(evento => dropzone.addEventListener(evento, e => {
        e.preventDefault();
        dropzone.classList.remove('arrastrando');
    }));
    dropzone.addEventListener('drop', e => {
        const archivo = e.dataTransfer.files[0];
        if(!archivo) return;
        const datos = new DataTransfer();
        datos.items.add(archivo);
        inputImagen.files = datos.files;
        procesarArchivo(archivo);
    });
}

// Redimensiona la imagen (max 900px de ancho) y la convierte a WebP con un canvas
const convertirAWebp = async (archivo, anchoMaximo = 900) => {
    const imagen = await createImageBitmap(archivo);
    const escala = Math.min(1, anchoMaximo / imagen.width);
    const canvas = document.createElement('canvas');
    canvas.width = Math.round(imagen.width * escala);
    canvas.height = Math.round(imagen.height * escala);
    canvas.getContext('2d').drawImage(imagen, 0, 0, canvas.width, canvas.height);

    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/webp', 0.85));
    if(!blob || blob.type !== 'image/webp') throw new Error('WebP no soportado');
    return new File([blob], archivo.name.replace(/\.\w+$/, '') + '.webp', { type: 'image/webp' });
}
