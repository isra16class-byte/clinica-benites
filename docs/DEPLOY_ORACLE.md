# Deploy en Oracle Cloud "Always Free" — guía de continuidad

Este documento acompaña al `Dockerfile`/`compose.prod.yaml` agregados el
02 sep 2026. Cubre la parte que Claude no puede hacer por el usuario (crear
la VM en la consola de Oracle, que requiere la cuenta real) y el checklist
para levantar el proyecto ahí una vez que la VM existe.

**Importante — sin probar en el sandbox**: Claude no tiene Docker disponible
en su sandbox, y el acceso de red está restringido a un listado corto de
dominios (no incluye Docker Hub, los repos de Alpine, ni fonts.bunny.net).
Todo lo de este documento y los archivos de Docker están escritos y
revisados a mano contra la documentación real de cada herramienta, pero
**la primera vez que se corra en la VM real puede aparecer algún ajuste
necesario** — mismo patrón que ya pasó antes con el bug de `facturas.total`
o el patch de compatibilidad de `dazza-dev/laravel-sri-ec` (ver
`CHANGELOG.md`): se corrige ahí mismo cuando aparezca.

## 1. Crear la VM en Oracle Cloud (hace falta la cuenta real del usuario)

1. **Consola → Compute → Instances → Create instance.**
2. **Image and shape**: cambiar la shape por defecto (suele ser paga) a
   **Ampere → `VM.Standard.A1.Flex`**. Elegir 2 OCPU / 12 GB RAM (o menos si
   la cuenta ya tiene el límite recortado de junio 2026 — revisar el tope
   real disponible en la propia consola antes de confirmar).
3. Imagen: **Ubuntu** (cualquier LTS reciente disponible para Ampere/ARM).
4. **Add SSH keys**: generar un par nuevo o subir una pública existente —
   sin esto no hay forma de entrar por SSH después.
5. **Networking**: dejar la VCN/subnet que Oracle ofrece por defecto (crea
   una si es la primera vez).
6. Confirmar y crear. Si sale "Out of host capacity" (error conocido y
   común para shapes Ampere en free tier): reintentar en unos minutos o
   probar otro Availability Domain de la misma región.

## 2. Firewall doble de Oracle — el paso que más se olvida

Oracle bloquea el tráfico en **dos capas independientes**; hay que abrir el
puerto 80 (y 443 si se agrega HTTPS después) en **ambas**, o no entra
tráfico aunque el contenedor esté corriendo bien:

1. **Security List de la VCN** (capa de red, en la consola):
   `Networking → Virtual Cloud Networks → (tu VCN) → Security Lists →
   Default Security List → Add Ingress Rules`. Agregar regla de entrada:
   origen `0.0.0.0/0`, protocolo TCP, puerto destino `80`.
2. **Firewall del sistema operativo** (dentro de la VM, por SSH). Ubuntu
   suele traer `iptables`/`netfilter` con reglas propias además de las de
   Oracle:
   ```bash
   sudo iptables -I INPUT 6 -m state --state NEW -p tcp --dport 80 -j ACCEPT
   sudo netfilter-persistent save
   ```
   (el número `6` de posición puede variar — revisar `sudo iptables -L
   INPUT -n --line-numbers` antes si la regla no aplica).

## 3. Instalar Docker en la VM

```bash
ssh ubuntu@<IP_PUBLICA_DE_LA_VM>

curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER
# cerrar sesión SSH y volver a entrar para que el grupo tome efecto
```

## 4. Clonar el repo y configurar `.env` de producción

```bash
git clone https://github.com/isra16class-byte/clinica-benites.git
cd clinica-benites
cp .env.example .env
```

Editar `.env` y ajustar, como mínimo, respecto al `.env.example` de
desarrollo:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=http://<IP_PUBLICA_DE_LA_VM>    # o el dominio, cuando esté comprado

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=clinica_benites
DB_USERNAME=clinica
DB_PASSWORD=<una contraseña fuerte real, no la de desarrollo>
```

`APP_KEY` puede quedar vacío — `docker/entrypoint.sh` lo genera solo la
primera vez si falta.

## 5. Levantar todo

```bash
docker compose -f compose.prod.yaml up -d --build
```

La primera vez, el build de la imagen (`npm run build` + `composer
install`) puede tardar varios minutos en una VM Ampere de este tamaño —
es esperable, no significa que algo esté colgado.

`docker/entrypoint.sh` corre automáticamente, en este orden: espera a
MySQL → genera `APP_KEY` si falta → `storage:link` → `migrate --force` →
cachea config/rutas/vistas/eventos. No hace falta correr nada de eso a
mano la primera vez.

## 6. Verificación

- `docker compose -f compose.prod.yaml ps` — los dos servicios (`app`,
  `mysql`) deben quedar `healthy`/`running`.
- `docker compose -f compose.prod.yaml logs -f app` — revisar que no haya
  quedado colgado en "Esperando a MySQL..." (si eso pasa por más de un
  minuto, revisar que `DB_PASSWORD` sea igual en ambos servicios del
  `.env`).
- Abrir `http://<IP_PUBLICA_DE_LA_VM>` en el navegador — debería verse el
  sitio público. `/admin` para el panel de Filament (sin usuario todavía:
  ver paso 7).

## 7. Crear el primer usuario admin

```bash
docker compose -f compose.prod.yaml exec app php artisan make:filament-user
docker compose -f compose.prod.yaml exec app php artisan tinker --execute="
  \$u = App\Models\User::first();
  \$u->rol = 'admin';
  \$u->save();
"
```

(Opcional, solo para ver datos de ejemplo mientras se prueba: `... exec app
php artisan db:seed --class=DemoHistoricoSeeder` — **no correr esto si ya
hay datos reales de pacientes cargados**, hoy no se ha hecho esa entrevista
formal así que en esta primera prueba debería ser seguro.)

## 8. Pendiente para dejarlo listo para producción real (no solo prueba)

- **HTTPS**: falta agregar un proxy con certificado (ej. Caddy o
  `certbot` + nginx) — hoy el `compose.prod.yaml` sirve solo HTTP en el
  puerto 80. No cerrar esto hasta tener el dominio `.com` comprado
  (sección 2 de `MEMORIA.md`).
- **Backups de MySQL**: el volumen `mysql-data` persiste mientras no se
  borre el contenedor/volumen a mano, pero eso no es un backup real (no
  protege contra un error de borrado ni contra que se pierda la VM
  entera). Falta un cron con `mysqldump` a algún storage externo (ej.
  Oracle Object Storage, que también tiene una cuota Always Free).
- **`opcache.validate_timestamps=0`** (`docker/php/opcache.ini`): en
  producción esto es correcto (mejor rendimiento), pero significa que un
  cambio de código no se refleja hasta reiniciar el contenedor
  (`docker compose -f compose.prod.yaml restart app`) o reconstruir la
  imagen — no es un bug si after a un deploy nuevo la app parece "vieja".
- **Certificado .p12 / RUC del SRI**: sigue siendo un trámite del cliente,
  no técnico (ver `docs/FACTURACION_ELECTRONICA_SRI.md`) — no bloquea este
  deploy de prueba.
