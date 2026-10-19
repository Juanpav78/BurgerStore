const fs = require('fs');
const path = require('path');
const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sharp = require('sharp');

function buildStyles() {
  return src('./src/sass/index.scss')
    .pipe(sass({ style: 'compressed' }).on('error', sass.logError))
    .pipe(dest('./build/css'));
}

// Copia la libreria Motion (antes Framer Motion) para usarla sin bundler
function vendorJs() {
  return src('./node_modules/motion/dist/motion.js')
    .pipe(dest('./build/js'));
}

// Convierte a WebP (mucho mas liviano) y redimensiona las imagenes.
// Solo procesa las que aun no tienen su version .webp o que cambiaron.
async function convertir(origen, destino, ancho) {
  fs.mkdirSync(destino, { recursive: true });
  const archivos = fs.readdirSync(origen).filter(f => /\.(png|jpe?g)$/i.test(f));

  await Promise.all(archivos.map(async archivo => {
    const entrada = path.join(origen, archivo);
    const salida = path.join(destino, archivo.replace(/\.(png|jpe?g)$/i, '.webp'));
    if (fs.existsSync(salida) && fs.statSync(salida).mtimeMs >= fs.statSync(entrada).mtimeMs) return;

    await sharp(entrada)
      .resize({ width: ancho, withoutEnlargement: true })
      .webp({ quality: 80 })
      .toFile(salida);
  }));
}

// Imagenes del diseño (src/img) -> build/img
function imagenesDiseno() {
  return convertir('./src/img', './build/img', 1400);
}

// Imagenes de productos subidas por el admin (img/) -> img/*.webp al lado del original
function imagenesProductos() {
  return convertir('./img', './img', 700);
}

function dev() {
  watch('./src/sass/**/*.scss', buildStyles);
}

exports.buildStyles = buildStyles;
exports.vendorJs = vendorJs;
exports.imagenes = parallel(imagenesDiseno, imagenesProductos);
exports.build = parallel(buildStyles, vendorJs, exports.imagenes);
exports.watch = series(exports.build, dev);
exports.default = exports.watch;
