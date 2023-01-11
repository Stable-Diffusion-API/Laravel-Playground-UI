# Laravel-Playground-UI
Stable Diffusion Playground UI in Laravel PHP

![alt text](https://stablediffusionapi.com//storage/generations/playground.jpeg)

![alt text](https://stablediffusionapi.com//storage/generations/playground1.jpeg)


## How to Setup 

Clone the Project
```
git clone https://github.com/Stable-Diffusion-API/Laravel-Playground-UI.git
```

Navigate into the project directory and install Dependencies 
```
composer install

npm install
```

Copy the content of env.example to .env.

```
cp .env.example .env
```

Generate APP key
```
php artisan key:generate
```

Make sure to add your api key. You can get it from [Stable Diffusion API Website](https://stablediffusionapi.com/)
```
STABLE_DIFFUSION_API_KEY=
```

Serve the Project
```
php artisan serve
```






