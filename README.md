# LogVisor - Analizador de Logs con IA

Aplicación web para subir archivos de log (`.log`), obtener una vista previa de su contenido y enviarlos a un servicio externo de inteligencia artificial para análisis de riesgo, hallazgos y recomendaciones.

## Requisitos

- PHP 8.3+
- Composer
- Node.js 18+
- SQLite (por defecto) o MySQL
- Servicio de análisis de IA (API externa, ver [Configuración del API](#configuración-del-api))

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd analisis_log

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Crear base de datos y tablas
php artisan migrate

# 5. Instalar dependencias JS y compilar assets
npm install
npm run build

# 6. Iniciar el servidor de desarrollo
php artisan serve
```

## Configuración del API

La aplicación se conecta a un servicio externo de análisis de logs (API Python) para procesar los archivos. El código fuente del API se encuentra en: [log_analyzer_api](https://github.com/Laime1/log_analyzer_api)

Configura la URL del servicio en el archivo `.env`:

```env
LOG_ANALYZER_API_URL=http://127.0.0.1:8000
```

El API externo debe exponer un endpoint `POST /analyze` que reciba el archivo como multipart/form-data y devuelva un JSON con la estructura:

```json
{
    "lines": 1500,
    "ai_analysis": {
        "risk_level": "bajo|medio|alto|critico",
        "summary": "Resumen del análisis...",
        "findings": ["Hallazgo 1", "Hallazgo 2"],
        "recommendations": ["Recomendación 1", "Recomendación 2"]
    }
}
```

## Uso

1. Acceder al panel de administración en `/admin` (requiere inicio de sesión)
2. En el menú lateral, hacer clic en **Analizar logs**
3. Subir un archivo `.log` (máximo 10 MB)
4. Ver la **vista previa** con las primeras 20 líneas del archivo
5. Hacer clic en **Analizar archivo** y esperar la respuesta del API
6. Consultar los resultados:
   - **Resumen** del contenido del log
   - **Nivel de riesgo** (bajo, medio, alto, crítico)
   - **Hallazgos** detectados
   - **Recomendaciones** sugeridas

## Estructura del proyecto (módulo de análisis)

```
app/
└── Filament/
    └── Pages/
        └── AnalyzeLogs.php          # Lógica del flujo: upload, preview, envío al API

resources/
└── views/
    └── filament/
        └── pages/
            └── analyze-logs.blade.php  # Vista del wizard de 2 pasos

config/
└── services.php                      # URL del API externo (log_analyzer.url)
```
