# Copia local — patch temporal de compatibilidad Laravel 13

Este es el código fuente real de `dazza-dev/laravel-sri-ec` v1.0.0
(descargado de https://github.com/dazza-dev/laravel-sri-ec, commit
`7ea7973`), copiado dentro del repo y usado vía un "path repository" de
Composer (ver `repositories` en el `composer.json` raíz) en vez de bajarlo
de Packagist.

**Por qué**: la única versión publicada del paquete (y también su fork
`clonixdev/laravel-sri-ec`, ambos del mismo autor) declara en su
`composer.json`:

```
"laravel/framework": "^8.0|^9.0|^10.0|^11.0|^12.0"
```

Sin Laravel 13, que es lo que usa este proyecto — Composer rechaza la
instalación directamente, sin llegar siquiera a intentar cargar el
código. Revisando el código real del paquete (`src/LaravelSriEcServiceProvider.php`),
no usa nada específico de una versión de Laravel — solo `ServiceProvider`,
`$this->app->singleton()`, `$this->publishes()`, `loadTranslationsFrom()`,
`commands()`, APIs estables desde hace muchas versiones. Es casi seguro
que el bloqueo es solo metadata desactualizada, no una incompatibilidad
real, pero eso no se pudo *probar* (sin PHP/Composer en el sandbox donde
se armó este patch).

**El único cambio hecho a este código** (todo lo demás es una copia
exacta): en `composer.json`, se agregó `|^13.0` al final del constraint de
`laravel/framework`. Ningún archivo PHP fue tocado.

## Cuándo dejar de usar esto

Cuando el autor (`dazza-dev`) publique una versión nueva del paquete en
Packagist que declare soporte para Laravel 13 (o confirmado que funciona
bien igual):

1. Borrar esta carpeta (`packages/dazza-dev/laravel-sri-ec/`).
2. Quitar la entrada `repositories` del `composer.json` raíz que apunta
   acá.
3. Correr `composer update dazza-dev/laravel-sri-ec` para que vuelva a
   bajar de Packagist.

No hay ningún issue abierto en el repo del paquete reportando esto (se
revisó el 01 sep 2026, 0 issues abiertos) — vale la pena abrir uno en
https://github.com/dazza-dev/laravel-sri-ec/issues pidiendo soporte para
Laravel 13, para no depender de este patch local indefinidamente.
