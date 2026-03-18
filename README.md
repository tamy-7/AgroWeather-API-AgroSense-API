# AgroWeather API / AgroSense

## Descripción
Sistema de **consulta y análisis agroclimático** que consolida datos de múltiples APIs meteorológicas para brindar **recomendaciones agrícolas**.

Esta API REST permite consultar el **pronóstico del tiempo para una ubicación específica**, unificar datos de diferentes fuentes y generar recomendaciones básicas sobre la favorabilidad para actividades de campo.

**Autores:**
- Folmer, Javier Armando (Legajo 18400)
- Gareis, Soledad Estefanía (Legajo 18397)
- Godoy, Tamara Cecilia (Legajo 18390)

---

## MVP (Producto Mínimo Viable)
El sistema permite consultar el pronóstico para los **próximos 7 días**, mostrando:

- Probabilidad de lluvia diaria (%)
- Temperaturas máximas y mínimas (°C)
- Volumen de precipitación (mm)
- Recomendación simple basada en reglas predefinidas

---

## Entidades Principales

### Consulta
- id (int)
- ubicacion (string/decimal)
- fechaConsulta (datetime)
- usuarioId (string)

### Pronóstico Diario
- id (int)
- consultaId (int)
- fecha (datetime)
- tempMax (decimal)
- tempMin (decimal)
- probLluvia (int)
- precipitacionMm (decimal)
- vientoKmH (decimal)
- recomendacion (string)
- fuenteApi (string)

**Relación:**  
1 Consulta → muchos Pronósticos (1:N)  
Cada Pronóstico → pertenece a 1 Consulta (N:1)

---

## Historias de Usuario

1. Como usuario agrícola, quiero ingresar una ubicación (lat/lon) para obtener el pronóstico de mi zona.
2. Como usuario, quiero ver el pronóstico consolidado de 7 días.
3. Como usuario, quiero ver la probabilidad de lluvia diaria.
4. Como usuario, quiero recibir alertas por temperaturas extremas.
5. Como usuario, quiero obtener recomendaciones simples en lenguaje claro.

### Criterios de aceptación
- Validación de latitud (-90 a 90) y longitud (-180 a 180).
- Si son inválidas → error **400 Bad Request**.
- Visualización de **exactamente 7 días**.
- Probabilidad > 60% → indicar "No regar/No fumigar".
- Temp > 35°C → alerta de calor.
- Temp < 2°C → alerta de helada.
- Recomendaciones redactadas en lenguaje simple (máx. 15 palabras).

---

## Tecnologías Utilizadas
- PHP

---

# Endpoints Iniciales

## 1. GET /health
Verifica la salud del servicio.

**Respuesta (200 OK):**
```json
{
  "estado": "OK",
  "mensaje": "Servicio funcionando correctamente",
  "timestamp": "2026-03-17T10:30:00Z"
}
2. POST /api/pronostico/consultar
Crea una nueva consulta de pronóstico.
Body (JSON):
JSON
{
  "ubicacion": "Buenos Aires, Argentina"
}
O coordenadas:
JSON
{
  "lat": -34.6037,
  "lon": -58.3816
}
Respuesta (201 Created):
JSON
{
  "id": 123,
  "ubicacion": "Buenos Aires, Argentina",
  "fechaConsulta": "2026-03-17T10:35:00Z",
  "pronosticos": [
    {
      "fecha": "2026-03-17",
      "tempMax": 28.5,
      "tempMin": 18.2,
      "probLluvia": 10,
      "recomendacion": "Favorable para siembra",
      "fuente": "OpenWeatherMap"
    },
    {
      "fecha": "2026-03-18",
      "tempMax": 27.0,
      "tempMin": 17.5,
      "probLluvia": 40,
      "recomendacion": "Posible lluvia, no fumigar",
      "fuente": "OpenWeatherMap"
    }
  ]
}
3. GET /api/pronostico/consultas/{id}
Obtiene los detalles de una consulta guardada.
Respuesta (200 OK):
JSON
{
  "id": 123,
  "ubicacion": "Buenos Aires, Argentina",
  "fechaConsulta": "2026-03-17T10:35:00Z",
  "pronosticos": [
    {
      "fecha": "2026-03-17",
      "tempMax": 28.5,
      "tempMin": 18.2,
      "probLluvia": 10,
      "recomendacion": "Favorable para siembra",
      "fuente": "OpenWeatherMap"
    },
    {
      "fecha": "2026-03-18",
      "tempMax": 27.0,
      "tempMin": 17.5,
      "probLluvia": 40,
      "recomendacion": "Posible lluvia, no fumigar",
      "fuente": "OpenWeatherMap"
    }
  ]
}
Error (404):
JSON
{
  "error": "No se encontró la consulta con id 123"
}
4. GET /api/pronostico/consultas
Lista todas las consultas realizadas.
Parámetros opcionales:
page (default 0)
limit (default 10)
Respuesta (200 OK):
JSON
{
  "page": 0,
  "limit": 10,
  "total": 25,
  "consultas": [
    {
      "id": 123,
      "ubicacion": "Buenos Aires, Argentina",
      "fechaConsulta": "2026-03-17T10:35:00Z"
    },
    {
      "id": 122,
      "ubicacion": "Córdoba, Argentina",
      "fechaConsulta": "2026-03-16T15:20:00Z"
    },
    {
      "id": 121,
      "ubicacion": "Rosario, Santa Fe",
      "fechaConsulta": "2026-03-15T09:10:00Z"
    }
  ]
}
5. GET /api/pronostico/actual
Consulta rápida sin persistencia.
Parámetros obligatorios:
lat
lon
dias (opcional, máx. 7)
Respuesta (200 OK):
JSON
{
  "ubicacion": {
    "lat": -34.6037,
    "lon": -58.3816,
    "nombre": "Buenos Aires"
  },
  "pronosticos": [
    {
      "fecha": "2026-03-17",
      "tempMax": 28.5,
      "tempMin": 18.2,
      "probLluvia": 10,
      "recomendacion": "Favorable para siembra",
      "fuente": "OpenWeatherMap"
    }
  ]
}
Error (400):
JSON
{
  "error": "Debe proporcionar latitud y longitud válidas"
}
Resumen de Endpoints
Método
Ruta
Descripción
GET
/health
Verifica estado del servicio
POST
/api/pronostico/consultar
Crea una nueva consulta
GET
/api/pronostico/consultas/{id}
Obtiene una consulta específica
GET
/api/pronostico/consultas
Lista consultas (paginado)
GET
/api/pronostico/actual
Consulta rápida sin guardar
