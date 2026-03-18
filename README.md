\# AgroWeather API / AgroSense



\## Descripción 



Sistema de \*\*consulta y análisis agroclimático\*\* que consolida datos de múltiples APIs meteorológicas para brindar \*\*recomendaciones agrícolas\*\*.



Este proyecto es una API REST que permite a usuarios del sector agrícola consultar el \*\*pronóstico del tiempo para una ubicación específica\*\*. Obtiene datos de diversas fuentes meteorológicas, los unifica y genera \_recomendaciones básicas\_ sobre la \_favorabilidad\_ para actividades como siembra, riego o cosecha. 



\- Folmer, Javier Armando (Número de legajo: 18400)

\- Gareis, Soledad Estefanía (Número de legajo: 18397)

\- Godoy, Tamara Cecilia (Número de legajo: 18390)



\## MVP (Producto Mínimo Viable)



El sistema permite a un usuario consultar el pronóstico para los \*\*próximos 7 días\*\* en una ubicación (coordenadas o ciudad). Consolida información de al menos una API externa (por ejemplo, OpenWeatherMap) y muestra:

\- Probabilidad de lluvia diaria (%).

\- Temperaturas máximas y mínimas (°C).

\- Volumen de precipitacion (mm).

\- Una recomendación simple ("Favorable para siembra", "Riesgo de heladas", etc.) basada en reglas de negocio predefinidas.



\## Entidades Principales



Como entidades principales mencionamos a la \*\*consulta\*\* y al \*\*pronostico diario\*\*.

La consulta representa una solicitud realizada por un usuario, que tiene como atributos \*\*id\*\* (int), \*\*ubicacion\*\* (string,decimales), \*\*fechaConsulta\*\*  (date time) y \*\*usuarioId\*\* (string). El pronostico diario tiene como atributos \*\*id\*\* (int), \*\*consultaId\*\* (int), \*\*fecha\*\* (datetime), \*\*tempMax\*\* (decimal), \*\*tempMin\*\* (decimal), \*\*probLluvia\*\* (int), \*\*precipitacionMm\*\* (decimal), \*\*vientoKmH\*\* (decimal), \*\*recomendacion\*\* (string) y \*\*fuenteApi\*\* (string).



La relación que existe entre ellas es de 1:N (una consulta, tiene muchos pronosticos diarios) y de N:1 (cada pronosticoDiario pertenece a una consulta).





\## Historias de Usuario



1\. Como \*\*usuario agrícola\*\*, quiero \*\*ingresar una ubicación (latitud/longitud)\*\* para obtener el \*\*pronóstico climático\*\* específico de mi campo.

2\. Como \*\*usuario\*\*, quiero ver el \*\*pronóstico consolidado\*\* para los próximos \*\*7 días\*\* para planificar mis actividades de campo a corto plazo.

3\. Como \*\*usuario\*\*, quiero que el sistema me muestre la probabilidad de lluvia diaria para decidir si debo regar o posponer la fumigación.

4\. Como \*\*usuario\*\*, quiero recibir una alerta o indicador visual si se esperan temperaturas extremas (heladas o calor excesivo) para proteger mis cultivos.

5\. Como \*\*usuario\*\*, quiero que el sistema me dé una recomendación simple (Ej: "Favorable para siembra", "Riesgo de heladas") basada en los datos climáticos para tomar decisiones rápidas.



Como criterios de aceptación básicos debemos tener en cuenta que el ingreso de \*\*ubicación y pronóstico\*\* debe ser validado por el sistema para que la latitud esté entre -90 y 90, y la longitud entre -180 y 180. De manera tal que si las coordenadas son válidas, la API debe retornar el clima actual (temperatura, humedad y estado del cielo).En cambio, si las coordenadas son inválidas, el sistema debe devolver un error 400 Bad Request con un mensaje claro.



Con respecto al \*\*pronóstico consolidado (7 días)\*\*, la interfaz debe mostrar una lista o tabla con exactamente 7 días correlativos (incluyendo el actual), donde cada día debe incluir, como mínimo la temperatura máxima, la temperatura mínima y el estado del tiempo. A su vez, el sistema debe promediar o elegir la fuente más confiable si hay discrepancias entre las APIs consultadas.



La \*\*probabilidad de lluvia y decisiones de riego\*\* deben ser mostradas por el sistema en porcentaje de probabilidad de lluvia (0% a 100%), en donde se debe incluir el volumen de precipitación esperado en milímetros (mm). Si la probabilidad es mayor al 60%, debe aparecer un mensaje o icono de "No regar/No fumigar".



En cuanto a las \*\*alertas de temperaturas extremas (Heladas/Calor)\*\* el sistema debe marcar en rojo o mostrar un icono de alerta si la temperatura supera los 35°C (Calor extremo) o debe marcar en azul o mostrar un icono de copo de nieve si la temperatura baja de los 2°C (Riesgo de helada). La alerta debe ser visible en la pantalla principal del pronóstico sin que el usuario tenga que buscarla.



La \*\*recomendación simple (Favorable/Riesgo)\*\* debe cruzar al menos dos variables (ej: Viento < 15km/h y Lluvia < 20%) para dar el estado "Favorable para fumigación". La recomendación debe redactarse en lenguaje natural y simple (máximo 15 palabras). Si los datos climáticos no están disponibles, el sistema debe mostrar "Recomendación no disponible".



\## Tecnologías Utilizadas (Ejemplo)



\- PHP



\## Endpoints Iniciales



\###1. GET /health

Descripción: Endpoint de verificación de salud del servicio. Útil para monitoreo y para comprobar que la API está operativa.



Respuesta esperada (200 OK):

{

&#x20; "estado": "OK",

&#x20; "mensaje": "Servicio funcionando correctamente",

&#x20; "timestamp": "2026-03-17T10:30:00Z"

}

\###2. POST /api/pronostico/consultar

Descripción: Crea una nueva consulta de pronóstico para una ubicación determinada. La ubicación puede ser un nombre de ciudad o coordenadas geográficas. El sistema consulta las APIs meteorológicas externas, guarda la consulta y los pronósticos diarios en la base de datos, y retorna la información obtenida.



Cuerpo de la solicitud (JSON):

{

&#x20; "ubicacion": "Buenos Aires, Argentina"

}

O también acepta coordenadas:

{

&#x20; "lat": -34.6037,

&#x20; "lon": -58.3816

}

Respuesta esperada (201 Created):



json

{

&#x20; "id": 123,

&#x20; "ubicacion": "Buenos Aires, Argentina",

&#x20; "fechaConsulta": "2026-03-17T10:35:00Z",

&#x20; "pronosticos": \[

&#x20;   {

&#x20;     "fecha": "2026-03-17",

&#x20;     "tempMax": 28.5,

&#x20;     "tempMin": 18.2,

&#x20;     "probLluvia": 10,

&#x20;     "recomendacion": "Favorable para siembra",

&#x20;     "fuente": "OpenWeatherMap"

&#x20;   },

&#x20;   {

&#x20;     "fecha": "2026-03-18",

&#x20;     "tempMax": 27.0,

&#x20;     "tempMin": 17.5,

&#x20;     "probLluvia": 40,

&#x20;     "recomendacion": "Posible lluvia, no fumigar",

&#x20;     "fuente": "OpenWeatherMap"

&#x20;   }

&#x20;   // ... (hasta 7 días)

&#x20; ]

}



\###3. GET /api/pronostico/consultas/{id}

Descripción: Obtiene la información detallada de una consulta previa, incluyendo todos los pronósticos diarios asociados, a partir de su identificador único.



Respuesta esperada (200 OK):



json

{

&#x20; "id": 123,

&#x20; "ubicacion": "Buenos Aires, Argentina",

&#x20; "fechaConsulta": "2026-03-17T10:35:00Z",

&#x20; "pronosticos": \[

&#x20;   {

&#x20;     "fecha": "2026-03-17",

&#x20;     "tempMax": 28.5,

&#x20;     "tempMin": 18.2,

&#x20;     "probLluvia": 10,

&#x20;     "recomendacion": "Favorable para siembra",

&#x20;     "fuente": "OpenWeatherMap"

&#x20;   },

&#x20;   {

&#x20;     "fecha": "2026-03-18",

&#x20;     "tempMax": 27.0,

&#x20;     "tempMin": 17.5,

&#x20;     "probLluvia": 40,

&#x20;     "recomendacion": "Posible lluvia, no fumigar",

&#x20;     "fuente": "OpenWeatherMap"

&#x20;   }

&#x20;   // ... resto de días

&#x20; ]

}

Respuesta en caso de no existir (404 Not Found):



json

{

&#x20; "error": "No se encontró la consulta con id 123"

}



\###4. GET /api/pronostico/consultas

Descripción: Lista todas las consultas realizadas, con información resumida (sin los detalles de cada pronóstico). Soporta paginación mediante parámetros opcionales.



Parámetros de consulta (opcionales):



page: Número de página (por defecto 0)



limit: Cantidad de elementos por página (por defecto 10)



Respuesta esperada (200 OK):



json

{

&#x20; "page": 0,

&#x20; "limit": 10,

&#x20; "total": 25,

&#x20; "consultas": \[

&#x20;   {

&#x20;     "id": 123,

&#x20;     "ubicacion": "Buenos Aires, Argentina",

&#x20;     "fechaConsulta": "2026-03-17T10:35:00Z"

&#x20;   },

&#x20;   {

&#x20;     "id": 122,

&#x20;     "ubicacion": "Córdoba, Argentina",

&#x20;     "fechaConsulta": "2026-03-16T15:20:00Z"

&#x20;   },

&#x20;   {

&#x20;     "id": 121,

&#x20;     "ubicacion": "Rosario, Santa Fe",

&#x20;     "fechaConsulta": "2026-03-15T09:10:00Z"

&#x20;   }

&#x20;   // ... más consultas

&#x20; ]

}



\###5. GET /api/pronostico/actual

Descripción: Obtiene el pronóstico del día actual (o de los próximos días si se especifica) para una ubicación dada, consultando directamente las APIs meteorológicas externas sin almacenar la consulta en la base de datos. Útil para obtener información rápida sin generar historial.



Parámetros de consulta (obligatorios):



lat: Latitud (ej. -34.6037)



lon: Longitud (ej. -58.3816)



dias (opcional): Número de días a pronosticar (por defecto 1, máximo 7)



Respuesta esperada (200 OK):



json

{

&#x20; "ubicacion": {

&#x20;   "lat": -34.6037,

&#x20;   "lon": -58.3816,

&#x20;   "nombre": "Buenos Aires"

&#x20; },

&#x20; "pronosticos": \[

&#x20;   {

&#x20;     "fecha": "2026-03-17",

&#x20;     "tempMax": 28.5,

&#x20;     "tempMin": 18.2,

&#x20;     "probLluvia": 10,

&#x20;     "recomendacion": "Favorable para siembra",

&#x20;     "fuente": "OpenWeatherMap"

&#x20;   }

&#x20; ]

}

Respuesta en caso de error con parámetros (400 Bad Request):



json

{

&#x20; "error": "Debe proporcionar latitud y longitud válidas"

}



\####Resumen de Endpoints

Método	Ruta	                        Descripción

GET	/health	                        Verifica el estado del servicio.

POST	/api/pronostico/consultar	Crea una nueva consulta persistente.

GET	/api/pronostico/consultas/{id}	Obtiene detalles de una consulta guardada.

GET	/api/pronostico/consultas	Lista el historial de consultas (paginado).

GET	/api/pronostico/actual	        Consulta rápida sin persistencia.



