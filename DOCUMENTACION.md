# Sistema de Control de Válvulas - Documentación de Mejoras

## ✅ Nuevas Funcionalidades Implementadas

### 1. **Captura Automática de Consumo de Agua**
- **Archivo**: `actualizarEstado.php` (mejorado)
- El sistema ahora automáticamente:
  - Registra la hora de apertura de cada válvula/bomba
  - Registra la hora de cierre
  - Calcula el consumo total en litros: `Consumo = (Flujo L/min × Tiempo en segundos) / 60`
  - Almacena el consumo en la tabla `consumo_final` de la BD

**Seguridad mejorada**: Usa `prepared statements` en lugar de queries directas para prevenir inyecciones SQL.

---

### 2. **Generación de Reportes en PDF**
- **Archivo**: `generarReporte.php` (nuevo)
- **Características**:
  - Genera PDF automático con toda la información de consumo
  - Filtra por rango de fechas
  - Filtra por dispositivo específico
  - Muestra:
    - Total de litros consumidos
    - Tiempo total de funcionamiento
    - Consumo promedio por ciclo
    - Tabla detallada de todos los eventos
  - Descarga automática con nombre: `reporte_consumo_YYYYMMDD_HHMMSS.pdf`

**Uso**: 
```
/generarReporte.php?fecha_inicio=2026-05-01&fecha_fin=2026-05-31&dispositivo=1
```

---

### 3. **Página de Reportes con Gráficos Interactivos**
- **Archivo**: `reportes.php` (nuevo)
- **Acceso**: Navega a `reportes.php` desde el menú

**Características**:
- ✅ **Filtros dinámicos** por fecha e dispositivo
- ✅ **Estadísticas en tiempo real**:
  - Consumo total
  - Consumo promedio
  - Tiempo total de funcionamiento
  - Total de ciclos registrados

- ✅ **3 Gráficos interactivos** (con Chart.js):
  1. **Gráfico de barras**: Consumo por dispositivo (Bomba, Calle 1, Calle 2)
  2. **Gráfico de líneas**: Consumo diario (tendencia a lo largo del tiempo)
  3. **Gráfico de líneas**: Historial de consumo (últimos 20 registros)

- ✅ **Tabla detallada** con información completa:
  - Dispositivo
  - Consumo en litros
  - Tiempo en minutos
  - Flujo promedio
  - Fecha y hora del evento

- ✅ **Botón PDF**: Descarga el reporte en PDF desde la página

---

### 4. **Mejoras de Seguridad**
Todos los archivos PHP ahora usan `prepared statements`:
- ✅ `actualizarEstado.php`
- ✅ `procesarEsp.php`
- ✅ `obtenerLecturas.php`
- ✅ `mostrarHistorial.php`
- ✅ `generarReporte.php`
- ✅ `reportes.php`

**Beneficios**: Previene ataques de inyección SQL, datos más seguros.

---

### 6. **Mejoras en la Interfaz Principal**
- **Archivo**: `index.php` (mejorado)
- ✅ Agregó link a "Reportes" en el menú de navegación
- ✅ JavaScript mejorado para control en tiempo real
- ✅ Actualización visual del estado de botones (verde/rojo)
- ✅ Manejo correcto de respuestas JSON

---

## 📊 Cómo Funciona el Flujo

### Flujo de Captura de Consumo:
```
1. Usuario hace clic en "Abrir Bomba/Válvula"
   ↓
2. Se registra la APERTURA con timestamp en log_eventos
   ↓
3. El sistema calcula: Consumo = (Flujo × Tiempo) / 60
   ↓
4. Usuario hace clic en "Cerrar Bomba/Válvula"
   ↓
5. Se registra el CIERRE con timestamp en log_eventos
   ↓
6. Se calcula automáticamente y se guarda en consumo_final
```

---

## 🗂️ Estructura de Tablas en BD

### `consumo_final` (donde se guarda todo)
```
- id_consumo: ID único
- calle_id: ID del dispositivo (1=Bomba, 2=Calle1, 3=Calle2)
- litros_totales: Consumo en litros
- tiempo_segundos: Duración en segundos
- fecha_registro: Cuándo se cerró el dispositivo
```

---

## 📱 Rutas Principales

| URL | Descripción |
|-----|------------|
| `/index.php` | Panel principal de control |
| `/reportes.php` | Gráficos y estadísticas |
| `/generarReporte.php` | Descarga PDF del reporte |
| `/historial.php` | Histórico de eventos |
| `/actualizarEstado.php` | API para abrir/cerrar válvulas |

---

## 🎯 Ejemplos de Uso

### Descargar reporte de toda la Bomba en mayo
```
generarReporte.php?fecha_inicio=2026-05-01&fecha_fin=2026-05-31&dispositivo=1
```

### Ver solo reportes de Calle 1
```
reportes.php?dispositivo=2
```

### Ver reportes de un día específico
```
reportes.php?fecha_inicio=2026-05-20&fecha_fin=2026-05-20
```

---

## 🔧 Próximas Mejoras Recomendadas

1. **Autenticación**: Agregar login de usuarios
2. **Alertas**: Notificaciones si el consumo excede límites
3. **Programación automática**: Horarios predefinidos para abrir/cerrar
4. **Histórico de cambios**: Tabla de auditoría
5. **Múltiples distribuidores**: Sistema de usuarios independientes
6. **Exportación a Excel**: Adicional al PDF

---

## 💡 Tips Importantes

✅ Los datos de consumo se guardan **solo cuando cierras** el dispositivo
✅ El flujo se obtiene del Arduino en tiempo real
✅ Los gráficos se actualizan automáticamente
✅ Los PDFs se generan dinámicamente sin guardar archivos

---

## ❓ Troubleshooting

### No aparecen gráficos
- Verifica que hay datos en `consumo_final`
- Revisa la consola del navegador (F12) para errores

### Error al descargar PDF
- Asegúrate que la carpeta `fpdf182/` existe
- Verifica permisos de escritura temporal

### Consumo muestra 0 litros
- Verifica que el flujo venga del Arduino (revisar tabla `estado_sistema`)
- Asegúrate que el tiempo entre apertura y cierre es > 0

---

**Última actualización**: 17 de Mayo de 2026
**Versión**: 2.0
**Estado**: ✅ Funcional
