# 🚀 Sistema de Control de Válvulas - Proyecto Actualizado

## 📋 Resumen de Cambios

He mejorado completamente tu sistema de control de válvulas con las siguientes funcionalidades:

---

## ✅ **Funcionalidad #1: Captura Automática de Consumo**

### ¿Qué hace?
- Cuando abres una válvula/bomba, el sistema registra la hora
- Cuando la cierras, calcula automáticamente:
  - **Litros consumidos** = (Flujo en L/min × Tiempo) / 60
  - Guarda en la tabla `consumo_final`

### Archivos modificados:
- `actualizarEstado.php` - Ahora con lógica de cálculo de consumo

### Ejemplo de flujo:
```
1. Click "Abrir Bomba" → Registra apertura
2. Espera tiempo X con flujo Y L/min
3. Click "Cerrar Bomba" → Calcula consumo y guarda
   Resultado: (Y × tiempo) / 60 = litros
```

---

## ✅ **Funcionalidad #2: Reportes en PDF**

### 📄 Archivo: `generarReporte.php`

**Características:**
- Genera PDF automático con:
  - Tabla de todos los consumos
  - Totales y promedios
  - Rango de fechas filtrable
  - Dispositivo filtrable
  - Fecha/hora de generación

**Uso:**
```
https://localhost/generarReporte.php?fecha_inicio=2026-05-01&fecha_fin=2026-05-31&dispositivo=1
```

**Resultado:** Descarga automática de `reporte_consumo_YYYYMMDD_HHMMSS.pdf`

---

## ✅ **Funcionalidad #3: Gráficos Interactivos**

### 📊 Archivo: `reportes.php`

**Acceso desde:** Menú principal → "Reportes"

**3 Gráficos incluidos:**

1. **Gráfico de Barras** - Consumo por dispositivo
   - Muestra: Bomba vs Calle 1 vs Calle 2
   - Unidad: Litros

2. **Gráfico de Líneas** - Consumo Diario
   - Muestra tendencia a lo largo del tiempo
   - Identifica patrones

3. **Gráfico de Líneas** - Historial Reciente
   - Últimos 20 registros
   - Detalles momento a momento

**Estadísticas en tiempo real:**
- Total de litros consumidos
- Consumo promedio por ciclo
- Tiempo total de funcionamiento
- Total de ciclos registrados

**Filtros dinámicos:**
- Por rango de fechas
- Por dispositivo específico
- Botón para descargar PDF del reporte

---

## ✅ **Funcionalidad #4: Seguridad Mejorada**

### 🔒 Prepared Statements

Todos los archivos ahora usan `prepared statements` para prevenir inyecciones SQL:
- ✅ `actualizarEstado.php`
- ✅ `procesarEsp.php`
- ✅ `obtenerLecturas.php`
- ✅ `mostrarHistorial.php`
- ✅ `generarReporte.php`
- ✅ `reportes.php`

**Beneficio:** Datos más seguros, protección contra ataques.

---

## ✅ **Funcionalidad #5: Seguridad Mejorada (Prepared Statements)**

### 🔒 Prepared Statements

Todos los archivos ahora usan `prepared statements` para prevenir inyecciones SQL:

- ✅ `actualizarEstado.php`
- ✅ `procesarEsp.php`
- ✅ `obtenerLecturas.php`
- ✅ `mostrarHistorial.php`
- ✅ `generarReporte.php`
- ✅ `reportes.php`

**Beneficio:** Datos más seguros, protección contra ataques.

---

## 📊 Tabla de Consumo Final (BD)

La información se guarda en `consumo_final`:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_consumo` | INT | ID único |
| `calle_id` | INT | ID dispositivo (1=Bomba, 2=Calle1, 3=Calle2) |
| `litros_totales` | FLOAT | Consumo calculado |
| `tiempo_segundos` | INT | Duración en segundos |
| `fecha_registro` | DATETIME | Cuándo se cerró |

---

## 🗂️ Estructura del Proyecto Actualizado

```
Valvulas/
├── index.php ......................... Panel principal (mejorado)
├── reportes.php ...................... Gráficos e estadísticas (NUEVO)
├── generarReporte.php ................ Generador de PDF (NUEVO)
├── actualizarEstado.php .............. Control de válvulas (mejorado)
├── procesarEsp.php ................... Datos del Arduino (mejorado)
├── obtenerLecturas.php ............... API de lecturas (mejorado)
├── mostrarHistorial.php .............. Historial de eventos (mejorado)
│
├── conexion.php ...................... Conexión a BD
├── estilos.css ....................... Estilos
├── historial.php ..................... Historial de eventos
├── información.php ................... Información del sistema
│
├── DOCUMENTACION.md .................. Guía técnica completa (NUEVO)
├── README.md ......................... Este archivo
│
├── fpdf182/ .......................... Librería FPDF
├── MySQL/
│   └── controlvalvulasbd.sql ......... Script de BD
└── ValvulasScript/
    └── ValvulasF.ino ................. Código Arduino
```

---

## 🎯 Cómo Usar las Nuevas Funcionalidades

### 1️⃣ Ver Reportes
```
1. Entra a http://localhost/Valvulas/index.php
2. Haz clic en "Reportes" en el menú
3. Verás gráficos, estadísticas y tabla de consumos
```

### 2️⃣ Filtrar Datos
```
1. En la página de Reportes
2. Selecciona:
   - Fecha inicio
   - Fecha fin
   - Dispositivo (opcional)
3. Haz clic en "Filtrar"
```

### 3️⃣ Descargar PDF
```
1. En Reportes, ajusta los filtros
2. Haz clic en "Descargar PDF"
3. Se descarga automáticamente: reporte_consumo_YYYYMMDD_HHMMSS.pdf
```

### 4️⃣ Usar la sección de reportes
```
1. En el panel principal (index.php)
2. Haz clic en "Reportes" en el menú
3. Verás gráficos, estadísticas y tabla de consumos
```

---

## 📈 Ejemplo de Flujo Completo

### Escenario: Distribuir agua a Calle 1

```
1. Hago clic en "Abrir Válvula" de Calle 1
2. El sistema registra apertura
3. El flujo de agua circula durante 30 minutos
4. Hago clic en "Cerrar Válvula" → Se calcula el consumo
   - Consumo: (10 L/min × 1800 seg) / 60 = 300 litros
   - Guarda en consumo_final:
     * calle_id: 2
     * litros_totales: 300
     * tiempo_segundos: 1800
5. En Reportes veo:
   - 300 litros consumidos
   - Gráficos de tendencia
6. Puedo descargar PDF con toda la información
```

---

## 🔧 Tecnologías Usadas

| Componente | Tecnología |
|-----------|-----------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP 8.0+ |
| BD | MySQL 8.0 |
| Gráficos | Chart.js 3.9.1 |
| PDF | FPDF 1.82 |
| IoT | Arduino + Sensores de flujo |

---

## 🚨 Notas Importantes

✅ **Los consumos se guardan solo cuando CIERRAS el dispositivo**

✅ **El flujo debe venir del Arduino en tiempo real** (verifica `estado_sistema`)

✅ **Los gráficos se actualizan automáticamente** al filtrar

✅ **Los PDFs se generan dinámicamente** (no ocupan espacio en servidor)

✅ **La seguridad está mejorada** con prepared statements

---

## ❓ Troubleshooting

| Problema | Solución |
|----------|----------|
| No aparecen gráficos | Verifica que haya datos en `consumo_final` |
| Error al descargar PDF | Revisa que la carpeta `fpdf182/` exista |
| Consumo muestra 0 | Verifica que el flujo venga del Arduino |
| Error de seguridad | Asegúrate que la BD está corriendo |

---

## 📚 Documentación Completa

Para información técnica detallada, consulta `DOCUMENTACION.md`

---

## ✨ Próximas Mejoras Sugeridas

1. 🔐 **Autenticación de usuarios**
2. 📢 **Alertas si consumo excede límites**
3. ⏰ **Programación automática de horarios**
4. 📋 **Tabla de auditoría de cambios**
5. 👥 **Sistema multi-usuario/distribuidor**
6. 📊 **Exportación a Excel**

---

## 🎉 ¡Listo para Usar!

Todos los archivos están optimizados, seguros y probados.
No hay errores de sintaxis ✅

**Última actualización:** 17 de Mayo de 2026
**Versión:** 2.0
**Estado:** ✅ Funcional y Listo para Producción

---

¿Preguntas o necesitas más funcionalidades? 
Puedo seguir mejorando el sistema según tus necesidades 🚀
