# IndicadoresSJ

Sistema Laravel para captura, seguimiento y consolidacion de indicadores operativos por zona.

## Modulos principales

- `Dashboard`: tablero operativo del usuario con resumen general, acceso por zona y actividad reciente.
- `Zonas`: tablero ejecutivo por zona con filtros de ano y mes.
- `Indicadores`: captura mensual por indicador y zona.
- `MADRE`: consolidado administrativo por indicador.
- `Dashboard Ops`: tablero ejecutivo administrativo.
- `Periodos`: apertura, cierre y reapertura de periodos.
- `Zonas` y `Usuarios`: administracion base.
- `Auditoria`: trazabilidad de cambios.

## Requisitos

- PHP 8.1
- Composer
- Node.js 18
- MySQL
- Laragon o entorno equivalente

## Configuracion

Archivo `.env` minimo relevante:

```env
APP_NAME=IndicadoresSJ
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://172.16.16.90:8081

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=indicadoressj
DB_USERNAME=root
DB_PASSWORD=

SYSTEM_BASE_YEAR=2026
SYSTEM_FUTURE_YEAR_OFFSET=10
```

## Rango de anos

El sistema usa un rango de anos unificado en todo el proyecto.

Regla actual:

- ano minimo: el menor entre `SYSTEM_BASE_YEAR` y el ano mas antiguo existente en `periods`
- ano maximo: el mayor entre `ano actual + SYSTEM_FUTURE_YEAR_OFFSET` y el ano mas alto existente en `periods`

Esto permite:

- consultar anos historicos ya guardados
- operar anos futuros sin tocar codigo
- mantener consistencia entre indicadores, dashboard, MADRE, periodos y exportes

La logica esta centralizada en:

- [app/Services/YearRangeService.php](app/Services/YearRangeService.php)

## Instalacion

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
```

## Desarrollo

Compilar frontend:

```bash
npm run build
```

Limpiar cache de Laravel:

```bash
php artisan view:clear
php artisan optimize:clear
```

## Flujo funcional

1. Crear o abrir periodos.
2. Registrar capturas mensuales por indicador y zona.
3. Completar el analisis desde modal segun el indicador.
4. Consultar tablero del usuario en `/dashboard`.
5. Consultar tablero por zona en `/zonas/{zone}`.
6. Consultar consolidado por indicador en MADRE.
7. Consultar tablero ejecutivo en `admin/dashboard`.

## Estado actual de dashboards

### Dashboard del usuario

- filtro unificado por ano y mes
- resumen ejecutivo del periodo
- KPI de score medio, cobertura media, alertas y zonas criticas
- bloque de balance del periodo con focos de atencion autoincrementables segun zonas visibles
- layout adaptativo:
  - vista enfocada si el usuario solo ve una zona
  - vista comparativa si ve varias zonas
- actividad reciente en dos columnas
- accesos por zona con enlace directo a cada tablero zonal

### Dashboard por zona

- cabecera con ano, mes y estado del periodo
- panorama operativo de la zona
- score, cobertura y comparacion con periodo anterior
- focos de atencion
- estado de indicadores
- tendencia de 6 meses
- actividad reciente
- mapa operativo de indicadores con acceso directo

## Notas

- FT-OP-03 tiene una vista especial por su estructura de doble medicion y clasificacion de siniestros.
- Los cambios de frontend deben cerrarse con `npm run build`, `php artisan view:clear` y `php artisan optimize:clear`.
