<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Hotel Reservas

Repositorio destinado a la reserva de una habitacion en el hotel elegido.

## Instalacion
Una vez clonado el repositorio, nos paramos dentro proyecto y ejecutamos los siguientes comandos en la consola
~~~~~~~~~~~~~~~~~~~
composer install
~~~~~~~~~~~~~~~~~~~
~~~~~~~~~~~~~~~~~~~
composer update
~~~~~~~~~~~~~~~~~~~

Ejecutamos las migraciones y los seeders
~~~~~~~~~~~~~~~~~~~
php artisan migrate --seed
~~~~~~~~~~~~~~~~~~~

Luego levantamos el proyecto ejecutando en consola
~~~~~~~~~~~~~~~~~~~
php artisan:make serve
~~~~~~~~~~~~~~~~~~~

## Endpoints

- Nombre: register
- Descripcion: servicio utilizado para poder registrarnos.
- Metodo HTTP: POST

URL: 
~~~~~~~~~~~~~~~~~~~
http://localhost:8000/api/register
~~~~~~~~~~~~~~~~~~~
Request:
~~~~~~~~~~~~~~~~~~~
{
    "name": "daniel",
    "email": "danie2@gmail.com",
    "password": "12345678"
}
~~~~~~~~~~~~~~~~~~~

Response:
~~~~~~~~~~~~~~~~~~~
{
    "success": true,
    "message": "successfully registered"
}
~~~~~~~~~~~~~~~~~~~

CURL:
~~~~~~~~~~~~~~~~~~~
curl --location --request POST 'http://localhost:8000/api/register' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "daniel",
    "email": "daniel@gmail.com",
    "password": "12345678"
}'
~~~~~~~~~~~~~~~~~~~
Advertencia: El email no se puede repetir. 

---
- Nombre: login
- Descripcion: servicio utilizado para poder autenticarse en el sistema, este genera un token para poder usar algunos endpoinst.
- Metodo HTTP: POST

URL: 
~~~~~~~~~~~~~~~~~~~
http://localhost:8000/api/login
~~~~~~~~~~~~~~~~~~~
Request:
~~~~~~~~~~~~~~~~~~~
{
    "email": "daniel@gmail.com",
    "password": "12345678"
}
~~~~~~~~~~~~~~~~~~~

Response:
~~~~~~~~~~~~~~~~~~~
{
    "success": true,
    "message": "started session with success",
    "token": "8|uFjQXxNrQbP0vjP3EIfv86AmZxYJlFG0RiUUGcvDf4e957af"
}
~~~~~~~~~~~~~~~~~~~
CURL:
~~~~~~~~~~~~~~~~~~~
curl --location --request POST 'http://localhost:8000/api/login' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "daniel@gmail.com",
    "password": "12345678"
}'
~~~~~~~~~~~~~~~~~~~
Advertencia: El token generado tiene duracion de 1 hora, luego expirara y no podra seguir usando algunos endpoints.

---
- Nombre: logout
- Descripcion: servicio utilizado para poder cerrar la sesión en el sistema.
- Authorization Type Token Bear
- Metodo HTTP: POST

URL: 
~~~~~~~~~~~~~~~~~~~
http://localhost:8000/api/logout
~~~~~~~~~~~~~~~~~~~

Response:
~~~~~~~~~~~~~~~~~~~
{
    "success": true,
    "message": "logged out successfully"
}
~~~~~~~~~~~~~~~~~~~
CURL:
~~~~~~~~~~~~~~~~~~~
curl --location --request POST 'http://localhost:8000/api/logout' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 7|oewnxWgXM3f3kGgKjmxYwyL6hYyz0z7eaVsGyZcI01ba6078' \
--data-raw ''
~~~~~~~~~~~~~~~~~~~

---
- Nombre: hotels
- Descripcion: servicio utilizado para poder consultar hoteles segun el pais y la localizacion.
- Authorization Type Token Bear
- Metodo HTTP: POST

URL: 
~~~~~~~~~~~~~~~~~~~
http://localhost:8000/api/hotels
~~~~~~~~~~~~~~~~~~~

Request:
~~~~~~~~~~~~~~~~~~~
{
    "country":"argentina",
    "location": "buenos aires"
}
~~~~~~~~~~~~~~~~~~~

Response:
~~~~~~~~~~~~~~~~~~~
{
    "success": true,
    "message": "select a hotel",
    "data": {
        "hotels": [
            {
                "name": "Sofitel Buenos Aires Recoleta",
                "rating": 4
            },
            {
                "name": "Park Tower, a Luxury Collection Hotel, Buenos Aires",
                "rating": 4
            },
            ...   
            ...
            ...
}
~~~~~~~~~~~~~~~~~~~

CURL:
~~~~~~~~~~~~~~~~~~~
curl --location --request POST 'http://localhost:8000/api/hotels' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 8|uFjQXxNrQbP0vjP3EIfv86AmZxYJlFG0RiUUGcvDf4e957af' \
--header 'Content-Type: application/json' \
--data-raw '{
    "country":"argentina",
    "location": "buenos aires"
}'
~~~~~~~~~~~~~~~~~~~

---
- Nombre: rooms
- Descripcion: servicio utilizado para poder consultar las habitaciones segun el hotel.
- Authorization Type Token Bear
- Metodo HTTP: POST

URL: 
~~~~~~~~~~~~~~~~~~~
http://localhost:8000/api/rooms
~~~~~~~~~~~~~~~~~~~

Request:
~~~~~~~~~~~~~~~~~~~
{
    "hotel": "Park Tower, a Luxury Collection Hotel, Buenos Aires"
}
~~~~~~~~~~~~~~~~~~~

Response:
~~~~~~~~~~~~~~~~~~~
{
    "success": true,
    "message": "select a room type",
    "data": {
        "hotel": "Park Tower, a Luxury Collection Hotel, Buenos Aires",
        "room_types": [
            "Non-smoking rooms",
            "Suites",
            "Family rooms"
        ]
    }
}
~~~~~~~~~~~~~~~~~~~

CURL:
~~~~~~~~~~~~~~~~~~~
curl --location --request POST 'http://localhost:8000/api/rooms' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|XllJoCVtrXXKOJLULYZCZRoNqLR8ZULPf3nNpaYl924e95cf' \
--header 'Content-Type: application/json' \
--data-raw '{
    "hotel": "Park Tower, a Luxury Collection Hotel, Buenos Aires"
}'
~~~~~~~~~~~~~~~~~~~
Advertencia: Antes debe buscar con el endpoints **hotels**, y selecionar un hotel.

---
- Nombre: reservation
- Descripcion: servicio utilizado para poder hacer una reserva segun el hotel y la habitacion.
- Authorization Type Token Bear
- Metodo HTTP: POST

URL: 
~~~~~~~~~~~~~~~~~~~
http://localhost:8000/api/reservation
~~~~~~~~~~~~~~~~~~~

Request:
~~~~~~~~~~~~~~~~~~~
{
    "hotel": "park Tower, a Luxury Collection Hotel, Buenos Aires",
    "room":"family rooms",
    "dateFrom":"28-11-2024",
    "dateUntil": "29-11-2024"
}
~~~~~~~~~~~~~~~~~~~

Response:
~~~~~~~~~~~~~~~~~~~
{
    "success": true,
    "message": "successfully reservation",
    "data": {
        "reservationCode": "GVZJ0VH5",
        "details": {
            "hotel": "park Tower, a Luxury Collection Hotel, Buenos Aires",
            "room": "family rooms",
            "address": "Avenida Leandro N Alem 1193, Buenos Aires C1001AAG Argentina",
            "dateFrom": "28-11-2024",
            "dateUntil": "29-11-2024",
            "paymentStatus": "PENDING"
        }
    }
}
~~~~~~~~~~~~~~~~~~~

CURL:
~~~~~~~~~~~~~~~~~~~
curl --location --request POST 'http://localhost:8000/api/reservation' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 9|XllJoCVtrXXKOJLULYZCZRoNqLR8ZULPf3nNpaYl924e95cf' \
--header 'Content-Type: application/json' \
--data-raw '{
    "hotel": "park Tower, a Luxury Collection Hotel, Buenos Aires",
    "room":"family rooms",
    "dateFrom":"28-11-2024",
    "dateUntil": "29-11-2024"
}'
~~~~~~~~~~~~~~~~~~~
Advertencia: Antes debe utilizar el endpoints **hotels** y **rooms** 

## Base de datos
- DER

<a href="https://ibb.co/LSzgZCG"><img src="https://i.ibb.co/Y27dLtr/Captura-de-pantalla-de-2024-01-26-21-32-55.png" alt="Captura-de-pantalla-de-2024-01-26-21-32-55" border="0"></a>

Es una base de datos MySQL, para trabajar localmente debe configurar el archivo .env y ejecutar el siguiente comando para generar la base de datos

~~~~~~~~~~~~~~~~~~~
php artisan migrate --seed
~~~~~~~~~~~~~~~~~~~

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
