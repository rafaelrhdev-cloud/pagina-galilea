# Tu Bienestar Psicología — Sitio web de Lic. Galilea López Suárez

Sitio web con sistema de citas funcional (PHP + MySQL). Incluye página pública y panel
privado para que Galilea confirme o rechace las solicitudes de cita.

## 📁 Estructura del proyecto

```
/ (raíz del hosting, ej. public_html)
├── index.html                  ← página principal
├── .htaccess
├── assets/
│   ├── css/style.css
│   ├── js/script.js
│   └── img/                    ← aquí van las fotos reales (ver abajo)
└── backend/
    ├── config.php               ← datos de conexión a la base de datos (EDITAR)
    ├── schema.sql                ← estructura de la base de datos (IMPORTAR)
    ├── setup_admin.php           ← crear la contraseña del panel (USAR UNA VEZ Y BORRAR)
    ├── .htaccess
    ├── api/
    │   ├── disponibilidad.php    ← entrega horarios ocupados (lo usa la página)
    │   └── solicitar_cita.php    ← recibe las solicitudes del formulario
    └── admin/
        ├── login.php             ← acceso al panel
        ├── dashboard.php         ← lista de citas, confirmar / rechazar / eliminar
        ├── actions.php
        ├── logout.php
        └── admin.css
```

## 🚀 Pasos para subir el sitio (hosting con cPanel o similar)

1. **Crear la base de datos MySQL** desde tu panel de hosting:
   - Crea una base de datos (ej. `tubienestar`).
   - Crea un usuario MySQL y una contraseña, y asígnalo a esa base con todos los permisos.

2. **Importar la estructura**: en phpMyAdmin, abre la base de datos y usa
   "Importar" para subir el archivo `backend/schema.sql`. Esto crea las tablas
   `citas` y `administradores`, además de 2 citas de **ejemplo** para que veas cómo
   se ve el sistema funcionando (puedes borrarlas luego desde el panel).

3. **Configurar la conexión**: abre `backend/config.php` y reemplaza:
   ```php
   $DB_HOST = 'localhost';
   $DB_NAME = 'tubienestar';
   $DB_USER = 'tubienestar_user';
   $DB_PASS = 'CAMBIA_ESTA_CONTRASENA';
   ```
   con los datos reales que te dio tu hosting.

4. **Subir todos los archivos** (todo el contenido de esta carpeta, incluyendo
   `index.html` y la carpeta `backend/`) a la raíz de tu hosting (normalmente `public_html`).
   Requiere PHP 7.4+ y MySQL/MariaDB (casi cualquier hosting compartido lo trae).

5. **Crear la contraseña del panel**: visita una sola vez en tu navegador:
   `https://tudominio.com/backend/setup_admin.php`
   Escribe el usuario (sugerido: `galilea`) y la contraseña que ella quiera usar, y da clic
   en "Crear / actualizar". Después de esto, **borra el archivo `setup_admin.php`** del
   servidor por seguridad (puedes volver a subirlo si necesitas cambiar la contraseña luego).

6. **Listo.** El sitio público está en `https://tudominio.com/`.
   El panel privado para confirmar citas está en `https://tudominio.com/backend/admin/login.php`
   (guarda este enlace, no está enlazado desde la página pública).

## 🗓️ Cómo funciona el sistema de citas

- Las citas posibles son de **3:00 p.m. a 8:00 p.m.**, en bloques de 1 hora (puedes
  cambiar esto en `backend/config.php`, variable `$HORAS_DISPONIBLES`).
- Cuando alguien llena el formulario en la página, la cita se guarda como **"pendiente"**.
  Todavía NO está agendada: el horario se bloquea para que nadie más lo pida mientras
  Galilea decide.
- Galilea entra al panel (`backend/admin/login.php`), revisa los datos, **habla
  personalmente con la persona**, y solo entonces da clic en **"Confirmar"**. Ahí la
  cita pasa a "confirmada" y se bloquea ya de forma definitiva en la página principal.
- Si decide que no procede, puede dar clic en **"Rechazar"**, y ese horario se libera
  automáticamente para que otra persona lo pueda solicitar.
- También puede **"Eliminar"** cualquier cita (por ejemplo, las 2 citas de ejemplo
  que vienen precargadas para que veas cómo funciona).

## 🖼️ Cómo poner las fotos reales

El diseño usa tarjetas de color como marcador de posición en "Sobre mí" y "Consultorio".
Para usar fotos reales:

1. Sube las fotos a `assets/img/` (por ejemplo `assets/img/galilea.jpg`,
   `assets/img/consultorio-1.jpg`, etc).
2. En `index.html`, busca los bloques con clase `about-photo` y `cphoto`, y dentro de
   cada uno agrega: `<img src="assets/img/tu-foto.jpg" alt="...">` (puedes borrar el
   fondo de color del CSS si prefieres, o dejarlo como respaldo si la imagen no carga).

## 🎨 Paleta de colores

| Color | Hex | Uso |
|---|---|---|
| Verde sage | `#BFC7B4` | Color principal / acentos |
| Verde sage oscuro | `#6F7A62` | Botones, encabezados |
| Verde profundo | `#4A5440` | Títulos, fondo de servicios |
| Arcilla cálido | `#C98F63` | Detalles, cursivas |
| Crema | `#F8F6F0` | Fondo general |

## 🔐 Seguridad / notas importantes

- Cambia la contraseña del panel periódicamente desde `setup_admin.php` (vuelve a
  subirlo cuando lo necesites y bórralo después de usarlo).
- El archivo `backend/config.php` contiene la contraseña de la base de datos: nunca lo
  compartas ni lo subas a un repositorio público.
- El aviso de privacidad incluido es una base de ejemplo: te recomendamos que un abogado
  o especialista en protección de datos lo revise antes de publicarlo.
- Este sitio no emite facturas (tal como se indicó).

## 🧪 Datos de prueba incluidos

`schema.sql` incluye 2 citas de ejemplo (una pendiente y una confirmada) para que
puedas probar el flujo completo: verlas en el panel, confirmarlas/rechazarlas, y ver
cómo se bloquean o liberan los horarios en la página principal. Bórralas cuando ya
no las necesites.
