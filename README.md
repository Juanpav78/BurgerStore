# 🍔 BurgerStore

> Tienda online de hamburguesas con menú, carrito, pedidos por WhatsApp y un panel de administración para gestionar los productos.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Sass](https://img.shields.io/badge/Sass-CC6699?style=flat&logo=sass&logoColor=white)
![Gulp](https://img.shields.io/badge/Gulp-CF4647?style=flat&logo=gulp&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)

## ✨ Funcionalidades

- **Sitio público:** inicio, menú de hamburguesas, información del restaurante, domicilios y sección "Encuéntranos".
- **Carrito lateral:** el pedido se guarda en el navegador mientras el cliente navega.
- **Pedido por WhatsApp:** el carrito se envía como mensaje con el resumen del pedido al número configurado.
- **Panel de administración:** login de administrador y CRUD de hamburguesas (crear, editar y eliminar, con imagen).
- **Animaciones** con Motion e imágenes optimizadas a WebP.

## 🛠️ Stack

| Capa | Tecnologías |
| --- | --- |
| Backend | PHP orientado a objetos (clases `Producto` y `Usuario`), Composer con autoload PSR-4 |
| Base de datos | MySQL (`database/burgershop_crud.sql`) |
| Estilos | Sass organizado en base, layout, components y admin |
| Build | Gulp para compilar Sass y JS, sharp para optimizar imágenes |
| Frontend | JavaScript sin frameworks y Motion |

## 🚀 Cómo correrlo

Requisitos: PHP 8, MySQL, Composer y Node.js.

```bash
git clone https://github.com/Juanpav78/BurgerStore.git
cd BurgerStore
composer install
npm install
cp .env.example .env
```

1. Crea la base de datos importando `database/burgershop_crud.sql`.
2. Completa el `.env` con los datos de MySQL (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_PORT`), el número de WhatsApp, el horario y las redes sociales.
3. Compila los estilos y levanta el servidor:

```bash
npm run dev     # Gulp en modo watch
npm run serve   # http://localhost:3000
```

| Comando | Acción |
| --- | --- |
| `npm run build` | Compila Sass, JS e imágenes en `build/` |
| `npm run dev` | Compila y vigila cambios |
| `npm run serve` | Servidor PHP local |
| `npm run empaquetar` | Build + paquete listo para subir al hosting |

## 📁 Estructura

```
├── admin/              # Panel: listado, crear y actualizar productos
├── classes/            # Modelos Producto y Usuario
├── includes/           # Configuración, funciones y plantillas
├── database/           # Script SQL
├── src/sass, src/js    # Código fuente de estilos y scripts
└── build/              # Archivos compilados
```

---

Hecho por **Juan Pablo Alvarado** · [Portafolio](https://juanpalvaradov.com) · [LinkedIn](https://www.linkedin.com/in/juanpavpandax/) · [GitHub](https://github.com/Juanpav78)
