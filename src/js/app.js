// Motion (antes Framer Motion) se carga como global desde /build/js/motion.js
const { animate, inView, stagger, press } = window.Motion ?? {};
const hayMotion = typeof animate === 'function';
const reducirMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const animar = hayMotion && !reducirMovimiento;

const resorte = { type: 'spring', stiffness: 260, damping: 22 };

document.addEventListener('DOMContentLoaded', function(){
    handleClickMenu();
    handleCards();
    handleModal();
    Carrito.iniciar();
    if(animar){
        animarHome();
        animarAlScroll();
        animarBotones();
    }
});

/* ---------- Utilidades ---------- */

const ICONOS = {
    mas: '<svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>',
    menos: '<svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>',
    basura: '<svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/></svg>',
    check: '<svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"/></svg>',
};

// Evita inyectar HTML al pintar textos
const escapar = (texto) => String(texto).replace(/[&<>"']/g, c => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
));

// 21500 -> "21.5k$" (mismo formato que el diseño)
const formatoK = (precio) => `${Math.round(precio / 100) / 10}k$`;

// 21500 -> "$ 21.500" (para el mensaje de WhatsApp)
const formatoPesos = (precio) => new Intl.NumberFormat('es-CO', {
    style: 'currency', currency: 'COP', maximumFractionDigits: 0,
}).format(precio);

const bloquearScroll = (bloquear) => {
    document.body.classList.toggle('modal-open', bloquear);
};

let temporizadorToast;
const mostrarToast = (mensaje) => {
    const toast = document.getElementById('toast');
    if(!toast) return;
    toast.innerHTML = `${ICONOS.check}<span>${escapar(mensaje)}</span>`;
    toast.hidden = false;
    if(animar) animate(toast, { opacity: [0, 1], y: [30, 0] }, resorte);

    clearTimeout(temporizadorToast);
    temporizadorToast = setTimeout(async () => {
        if(animar) await animate(toast, { opacity: 0, y: 30 }, { duration: 0.2 });
        toast.hidden = true;
    }, 2200);
};

// Lee los datos de un producto desde los atributos data- de su tarjeta
const productoDesdeCard = (card) => ({
    id: card.dataset.id,
    nombre: card.dataset.nombre,
    categoria: card.dataset.categoria,
    precio: Number(card.dataset.precio),
    imagen: card.dataset.imagen,
    descripcion: card.querySelector('.card--burger__description')?.textContent.trim() ?? '',
});

/* ---------- Menu movil ---------- */

const handleClickMenu = ()=>{
    //se declaran las variables y su seleccion en el dom
    const btn = document.getElementById("btn-menu")
    const nav = document.getElementById("nav")
    if(!btn || !nav) return;

    btn.addEventListener('click', e => {
        e.preventDefault();
        const activo = nav.classList.toggle("active")
        btn.classList.toggle("active")
        btn.setAttribute('aria-expanded', activo);

        if(animar){
            animate(btn, { rotate: activo ? 90 : 0 }, resorte);
            if(activo){
                animate(nav.querySelectorAll(".header__nav__link"),
                    { opacity: [0, 1], y: [20, 0] },
                    { delay: stagger(0.05, { startDelay: 0.1 }) });
            }
        }
    });

}

/* ---------- Tarjetas del menu ---------- */

const handleCards = ()=>{
    document.querySelectorAll(".menu .card--burger").forEach(card => {
        card.addEventListener('click', () => Modal.abrir(card));
        card.addEventListener('keydown', e => {
            if(e.key === 'Enter' || e.key === ' '){
                e.preventDefault();
                Modal.abrir(card);
            }
        });

        // Boton "+" : agrega directo sin abrir el modal
        card.querySelector(".card--burger__add")?.addEventListener('click', e => {
            e.stopPropagation();
            Carrito.agregar(productoDesdeCard(card), 1);
            if(animar) animate(e.currentTarget, { scale: [0.7, 1], rotate: [0, 180] }, resorte);
        });
    });
}

/* ---------- Modal de producto (uno solo para todas las tarjetas) ---------- */

const Modal = {
    el: null,
    producto: null,
    origen: null,

    abrir(card){
        const modal = this.el;
        if(!modal || modal.classList.contains("active")) return;

        this.producto = productoDesdeCard(card);
        this.origen = card;
        const p = this.producto;

        modal.querySelector('#modal-img').src = p.imagen;
        modal.querySelector('#modal-img').alt = p.nombre;
        modal.querySelector('#modal-categoria').textContent = p.categoria;
        modal.querySelector('#modal-titulo').textContent = p.nombre;
        modal.querySelector('#modal-precio').textContent = formatoK(p.precio);
        modal.querySelector('#modal-descripcion').textContent = p.descripcion;
        this.setCantidad(1, false);

        modal.classList.add("active");
        bloquearScroll(true);
        modal.querySelector('.btnClose').focus({ preventScroll: true });

        if(animar){
            animate(modal, { opacity: [0, 1] }, { duration: 0.25 });
            animate(modal.querySelector(".modal--burger__contenedor"),
                { opacity: [0, 1], scale: [0.85, 1], y: [40, 0] }, resorte);
            animate(modal.querySelector(".modal--burger__img"),
                { rotate: [-25, 0], scale: [0.6, 1] }, { ...resorte, delay: 0.1 });
        }
    },

    async cerrar(){
        const modal = this.el;
        if(!modal?.classList.contains("active") || modal.dataset.cerrando) return;

        if(animar){
            modal.dataset.cerrando = "true";
            animate(modal.querySelector(".modal--burger__contenedor"),
                { scale: 0.9, y: 30, opacity: 0 }, { duration: 0.2 });
            await animate(modal, { opacity: 0 }, { duration: 0.25 });
            modal.querySelectorAll("[style]").forEach(el => el.removeAttribute("style"));
            modal.removeAttribute("style");
            delete modal.dataset.cerrando;
        }

        modal.classList.remove("active");
        if(!Carrito.abierto) bloquearScroll(false);
        this.origen?.focus({ preventScroll: true });
    },

    cantidad(){
        return Number(this.el.querySelector('.cantidad__input').value) || 1;
    },

    setCantidad(valor, animarTotal = true){
        const cantidad = Math.min(Math.max(Math.round(valor) || 1, 1), 10);
        const input = this.el.querySelector('.cantidad__input');
        input.value = cantidad;
        this.el.querySelector('[data-cambio="-1"]').disabled = cantidad <= 1;
        this.el.querySelector('[data-cambio="1"]').disabled = cantidad >= 10;

        const total = this.el.querySelector('#modal-total');
        total.textContent = formatoK(this.producto.precio * cantidad);
        if(animar && animarTotal) animate(total, { scale: [1.3, 1] }, resorte);
    },
};

const handleModal = ()=>{
    const modal = document.getElementById('modal-producto');
    if(!modal) return;
    Modal.el = modal;

    modal.querySelector('.btnClose').addEventListener('click', () => Modal.cerrar());
    // cerrar al hacer click fuera del contenido
    modal.addEventListener('click', e => {
        if(e.target === modal) Modal.cerrar();
    });

    modal.querySelectorAll('[data-cambio]').forEach(btn => {
        btn.addEventListener('click', () => Modal.setCantidad(Modal.cantidad() + Number(btn.dataset.cambio)));
    });
    modal.querySelector('.cantidad__input').addEventListener('change', e => Modal.setCantidad(e.target.value));

    // "Añadir al carrito"
    modal.querySelector('#modal-form').addEventListener('submit', async e => {
        e.preventDefault();
        Carrito.agregar(Modal.producto, Modal.cantidad());
        await Modal.cerrar();
    });

    // "Comprar ahora": agrega y abre el carrito para terminar el pedido
    modal.querySelector('#modal-comprar').addEventListener('click', async () => {
        Carrito.agregar(Modal.producto, Modal.cantidad(), false);
        await Modal.cerrar();
        Carrito.abrir();
    });
}

/* ---------- Carrito (se guarda en el navegador del cliente) ---------- */

const Carrito = {
    CLAVE: 'burgerstore-carrito',
    CLAVE_CLIENTE: 'burgerstore-cliente',
    MAXIMO: 20,
    items: [],
    abierto: false,
    el: null,

    iniciar(){
        this.el = document.getElementById('carrito');
        if(!this.el) return;
        this.items = this.leer(this.CLAVE, []);

        document.getElementById('btn-carrito')?.addEventListener('click', () => this.abrir());
        document.getElementById('carrito-cerrar').addEventListener('click', () => this.cerrar());
        document.getElementById('carrito-fondo').addEventListener('click', () => this.cerrar());
        document.getElementById('carrito-vaciar').addEventListener('click', () => {
            this.items = [];
            this.guardar();
            mostrarToast('Carrito vaciado');
        });

        // Botones de cantidad y quitar de cada producto (delegacion de eventos)
        document.getElementById('carrito-lista').addEventListener('click', e => {
            const btn = e.target.closest('button[data-accion]');
            if(!btn) return;
            const id = btn.closest('[data-id]').dataset.id;
            if(btn.dataset.accion === 'quitar') this.quitar(id);
            else this.cambiarCantidad(id, Number(btn.dataset.accion));
        });

        // Recordar nombre y direccion para el proximo pedido
        const form = document.getElementById('carrito-form');
        const cliente = this.leer(this.CLAVE_CLIENTE, {});
        form.nombre.value = cliente.nombre ?? '';
        form.direccion.value = cliente.direccion ?? '';
        form.addEventListener('submit', e => {
            e.preventDefault();
            this.enviarWhatsapp(form);
        });

        // Si el carrito cambia en otra pestaña, se sincroniza
        window.addEventListener('storage', e => {
            if(e.key !== this.CLAVE) return;
            this.items = this.leer(this.CLAVE, []);
            this.render();
        });

        document.addEventListener('keydown', e => {
            if(e.key !== 'Escape') return;
            if(Modal.el?.classList.contains('active')) Modal.cerrar();
            else if(this.abierto) this.cerrar();
        });

        this.render(false);
    },

    leer(clave, porDefecto){
        try {
            return JSON.parse(localStorage.getItem(clave)) ?? porDefecto;
        } catch {
            return porDefecto;
        }
    },

    guardar(){
        try {
            localStorage.setItem(this.CLAVE, JSON.stringify(this.items));
        } catch {
            // Modo privado o almacenamiento bloqueado: el carrito vive solo en esta pagina
        }
        this.render();
    },

    agregar(producto, cantidad = 1, avisar = true){
        const item = this.items.find(i => i.id === producto.id);
        if(item){
            item.cantidad = Math.min(item.cantidad + cantidad, this.MAXIMO);
            item.precio = producto.precio;
        }else{
            const { id, nombre, precio, imagen } = producto;
            this.items.push({ id, nombre, precio, imagen, cantidad: Math.min(cantidad, this.MAXIMO) });
        }
        this.guardar();
        if(avisar) mostrarToast(`${cantidad} x ${producto.nombre} añadido al carrito`);
    },

    cambiarCantidad(id, cambio){
        const item = this.items.find(i => i.id === id);
        if(!item) return;
        item.cantidad += cambio;
        if(item.cantidad < 1) return this.quitar(id);
        item.cantidad = Math.min(item.cantidad, this.MAXIMO);
        this.guardar();
    },

    quitar(id){
        this.items = this.items.filter(i => i.id !== id);
        this.guardar();
    },

    unidades(){
        return this.items.reduce((total, i) => total + i.cantidad, 0);
    },

    total(){
        return this.items.reduce((total, i) => total + i.precio * i.cantidad, 0);
    },

    render(animarContador = true){
        const lista = document.getElementById('carrito-lista');
        lista.innerHTML = this.items.map(i => `
            <div class="cart__burger" data-id="${escapar(i.id)}">
                <div class="cart__img">
                    <img src="${escapar(i.imagen)}" alt="${escapar(i.nombre)}" width="80" height="64" loading="lazy">
                </div>
                <div class="cart__info">
                    <h3 class="cart__title">${escapar(i.nombre)}</h3>
                    <span class="cart__price">${formatoK(i.precio * i.cantidad)}</span>
                    <div class="cart__fila">
                        <div class="cantidad cantidad--mini">
                            <button class="cantidad__btn" type="button" data-accion="-1" aria-label="Quitar uno">${ICONOS.menos}</button>
                            <span class="cantidad__input">${i.cantidad}</span>
                            <button class="cantidad__btn" type="button" data-accion="1" aria-label="Agregar uno" ${i.cantidad >= this.MAXIMO ? 'disabled' : ''}>${ICONOS.mas}</button>
                        </div>
                        <button class="cart__quitar" type="button" data-accion="quitar" aria-label="Quitar ${escapar(i.nombre)}">${ICONOS.basura}</button>
                    </div>
                </div>
            </div>`).join('');

        document.getElementById('carrito-total').textContent = formatoK(this.total());
        this.el.classList.toggle('vacio', this.items.length === 0);

        const contador = document.getElementById('carrito-contador');
        if(contador){
            const unidades = this.unidades();
            const cambio = contador.textContent !== String(unidades);
            contador.textContent = unidades;
            contador.hidden = unidades === 0;
            if(animar && animarContador && cambio && unidades > 0){
                animate(contador, { scale: [1.6, 1] }, { type: 'spring', stiffness: 400, damping: 12 });
                animate('#btn-carrito', { rotate: [-12, 12, 0] }, { duration: 0.4 });
            }
        }
    },

    abrir(){
        if(this.abierto || !this.el) return;
        this.abierto = true;
        const fondo = document.getElementById('carrito-fondo');
        fondo.hidden = false;
        this.el.classList.add('active');
        this.el.setAttribute('aria-hidden', 'false');
        bloquearScroll(true);
        document.getElementById('carrito-cerrar').focus({ preventScroll: true });

        if(animar){
            animate(fondo, { opacity: [0, 1] }, { duration: 0.25 });
            animate(this.el, { x: ['100%', '0%'] }, { type: 'spring', stiffness: 300, damping: 32 });
            animate(this.el.querySelectorAll('.cart__burger'),
                { opacity: [0, 1], x: [40, 0] }, { delay: stagger(0.05, { startDelay: 0.15 }) });
        }
    },

    async cerrar(){
        if(!this.abierto) return;
        this.abierto = false;
        const fondo = document.getElementById('carrito-fondo');

        if(animar){
            animate(fondo, { opacity: 0 }, { duration: 0.2 });
            await animate(this.el, { x: '100%' }, { duration: 0.25, ease: 'easeIn' });
            this.el.removeAttribute('style');
            fondo.removeAttribute('style');
        }

        fondo.hidden = true;
        this.el.classList.remove('active');
        this.el.setAttribute('aria-hidden', 'true');
        if(!Modal.el?.classList.contains('active')) bloquearScroll(false);
        document.getElementById('btn-carrito')?.focus({ preventScroll: true });
    },

    // Arma la lista del pedido y abre WhatsApp con el mensaje listo para enviar
    enviarWhatsapp(form){
        const numero = this.el.dataset.whatsapp;
        if(!numero){
            mostrarToast('El número de WhatsApp de la tienda aún no está configurado');
            return;
        }
        if(!form.reportValidity()) return;

        const nombre = form.nombre.value.trim();
        const direccion = form.direccion.value.trim();
        const notas = form.notas.value.trim();
        try {
            localStorage.setItem(this.CLAVE_CLIENTE, JSON.stringify({ nombre, direccion }));
        } catch {}

        const lineas = this.items.map(i =>
            `• ${i.cantidad} x ${i.nombre} — ${formatoPesos(i.precio * i.cantidad)}`);

        const mensaje = [
            '¡Hola Burger Store! 🍔 Quiero hacer este pedido:',
            '',
            ...lineas,
            '',
            `*Total: ${formatoPesos(this.total())}*`,
            '',
            `👤 Nombre: ${nombre}`,
            `📍 Dirección: ${direccion}`,
            notas ? `📝 Notas: ${notas}` : null,
        ].filter(l => l !== null).join('\n');

        window.open(`https://wa.me/${numero}?text=${encodeURIComponent(mensaje)}`, '_blank', 'noopener');
        mostrarToast('¡Pedido listo! Termina de enviarlo en WhatsApp');
    },
};

/* ---------- Animaciones ---------- */

// Entrada del hero de la pagina principal
const animarHome = ()=>{
    const info = document.querySelector(".home__info");
    const burger = document.querySelector(".home__img");
    if(info){
        animate(info.children, { opacity: [0, 1], x: [-60, 0] },
            { delay: stagger(0.15), duration: 0.6, ease: 'easeOut' });
    }
    if(burger){
        // la rotacion de 12deg es la del diseño original
        animate(burger, { opacity: [0, 1], scale: [0.6, 1], rotate: [-30, 12] },
            { type: 'spring', stiffness: 120, damping: 14, delay: 0.2 });
    }
}

// Aparicion de secciones y tarjetas al hacer scroll
const animarAlScroll = ()=>{
    const grupos = [
        ".menu__title",
        ".banner__burger", ".banner__info",
        ".domicilio > *",
        ".restaurantes__title", ".card--restaurante",
    ];
    document.querySelectorAll(grupos.join(",")).forEach(el => {
        el.style.opacity = 0;
        inView(el, () => {
            animate(el, { opacity: [0, 1], y: [40, 0] }, { duration: 0.6, ease: 'easeOut' });
        }, { amount: 0.2 });
    });

    // las tarjetas del menu entran en cascada
    const contenedor = document.querySelector(".menu__contenedor");
    if(contenedor){
        const cards = contenedor.querySelectorAll(".card--burger");
        cards.forEach(card => card.style.opacity = 0);
        inView(contenedor, () => {
            animate(cards, { opacity: [0, 1], y: [60, 0], scale: [0.9, 1] },
                { delay: stagger(0.08), type: 'spring', stiffness: 200, damping: 20 });
        }, { amount: 0.1 });
    }
}

// Efecto de presionado en los botones
const animarBotones = ()=>{
    if(typeof press !== 'function') return;
    press(".boton, .boton--circle, .header__cart", el => {
        animate(el, { scale: 0.92 }, { duration: 0.1 });
        return () => animate(el, { scale: 1 }, resorte);
    });
}
