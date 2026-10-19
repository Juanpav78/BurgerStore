// Arma la carpeta deploy/ lista para subir a un hosting con PHP (InfinityFree, Hostinger...)
//   deploy/htdocs/                 -> se sube tal cual a la carpeta htdocs del hosting
//   deploy/burgershop_hosting.sql  -> se importa en phpMyAdmin
// Uso: npm run empaquetar -- --email=tu@correo.com
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const { execFileSync } = require('child_process');

const RAIZ = path.join(__dirname, '..');
const SALIDA = path.join(RAIZ, 'deploy');
const HTDOCS = path.join(SALIDA, 'htdocs');

const arg = (nombre, porDefecto) =>
  (process.argv.find(a => a.startsWith(`--${nombre}=`)) || '').split('=')[1] || porDefecto;
const email = arg('email', 'admin@burgerstore.com');

// Lo que necesita el sitio para funcionar (nada de Sass, node_modules ni archivos de diseño)
const INCLUIR = [
  '.htaccess', 'index.php', 'menu.php', 'login.php', 'cerrarSesion.php',
  'admin', 'classes', 'includes', 'vendor', 'build', 'src/js',
  'src/img/Logo.svg', 'src/img/logo.png',
];

const copiar = (origen, destino) => {
  const stat = fs.statSync(origen);
  if (stat.isDirectory()) {
    fs.mkdirSync(destino, { recursive: true });
    for (const hijo of fs.readdirSync(origen)) copiar(path.join(origen, hijo), path.join(destino, hijo));
  } else {
    fs.mkdirSync(path.dirname(destino), { recursive: true });
    fs.copyFileSync(origen, destino);
  }
};

fs.rmSync(SALIDA, { recursive: true, force: true });
for (const ruta of INCLUIR) copiar(path.join(RAIZ, ruta), path.join(HTDOCS, ruta));

// Imagenes de productos: solo la version optimizada (.webp) si existe, mas el .htaccess
const IMG = path.join(RAIZ, 'img');
fs.mkdirSync(path.join(HTDOCS, 'img'), { recursive: true });
for (const archivo of fs.readdirSync(IMG)) {
  const base = archivo.replace(/\.\w+$/, '');
  const esOriginal = /\.(png|jpe?g)$/i.test(archivo);
  if (esOriginal && fs.existsSync(path.join(IMG, base + '.webp'))) continue;
  fs.copyFileSync(path.join(IMG, archivo), path.join(HTDOCS, 'img', archivo));
}

// .env del servidor: datos de la base de datos vacios (los da el hosting)
// y el resto (WhatsApp, horario, redes) copiado del .env local
const leerEnv = (archivo) => {
  if (!fs.existsSync(archivo)) return {};
  return Object.fromEntries(fs.readFileSync(archivo, 'utf8').split(/\r?\n/)
    .filter(l => l.includes('=') && !l.trim().startsWith('#'))
    .map(l => { const i = l.indexOf('='); return [l.slice(0, i).trim(), l.slice(i + 1).trim()]; }));
};
const local = { ...leerEnv(path.join(RAIZ, '.env.example')), ...leerEnv(path.join(RAIZ, '.env')) };
const v = (clave) => local[clave] ?? '';
fs.writeFileSync(path.join(HTDOCS, '.env'), `# Datos de MySQL: estan en el panel de InfinityFree > MySQL Databases
DB_HOST=
DB_USER=
DB_PASS=
DB_NAME=
DB_PORT=3306

WHATSAPP_NUMERO=${v('WHATSAPP_NUMERO')}
TELEFONO=${v('TELEFONO')}
HORARIO=${v('HORARIO')}
INSTAGRAM_URL=${v('INSTAGRAM_URL')}
FACEBOOK_URL=${v('FACEBOOK_URL')}
TIKTOK_URL=${v('TIKTOK_URL')}
`);

// SQL para el hosting: sin CREATE DATABASE (la base se crea desde el panel)
// y con un admin nuevo con contraseña aleatoria
const password = crypto.randomBytes(9).toString('base64url');
const hash = execFileSync('php', ['-r', 'echo password_hash($argv[1], PASSWORD_BCRYPT);', password]).toString().trim();
const sql = fs.readFileSync(path.join(RAIZ, 'database', 'burgershop_crud.sql'), 'utf8')
  .replace(/^CREATE DATABASE[^\n]*\n/m, '')
  .replace(/^USE [^\n]*\n/m, '')
  .replace(/-- Usuario admin de prueba[^\n]*\n/, '')
  .replace(/^-- Password: 123456[^\n]*\n/m, '')
  .replace(/\('jpav@correo\.com', '[^']+'\)/, `('${email.replace(/'/g, "''")}', '${hash}')`);
if (sql.includes('jpav@correo.com')) throw new Error('No se pudo reemplazar el usuario admin en el SQL');
fs.writeFileSync(path.join(SALIDA, 'burgershop_hosting.sql'), sql);

const peso = (dir) => fs.readdirSync(dir, { withFileTypes: true })
  .reduce((t, e) => t + (e.isDirectory() ? peso(path.join(dir, e.name)) : fs.statSync(path.join(dir, e.name)).size), 0);

console.log(`
Listo: deploy/ (${(peso(HTDOCS) / 1024 / 1024).toFixed(1)} MB)
  deploy/htdocs/                  -> sube su CONTENIDO a la carpeta htdocs del hosting
  deploy/burgershop_hosting.sql   -> importalo en phpMyAdmin
  deploy/htdocs/.env              -> completa los datos DB_ antes de subir

Admin del sitio publicado (guardalo, no se vuelve a mostrar):
  Email:      ${email}
  Contraseña: ${password}
`);
